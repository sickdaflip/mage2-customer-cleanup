<?php
/**
 * Customer Filter Service
 * Identifies customers eligible for cleanup based on configured criteria
 */
declare(strict_types=1);

namespace FlipDev\CustomerCleanup\Service;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use FlipDev\CustomerCleanup\Helper\Config;

class CustomerFilterService
{
    /**
     * @var CustomerCollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * Constructor
     *
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param Config $config
     * @param DateTime $dateTime
     */
    public function __construct(
        CustomerCollectionFactory $customerCollectionFactory,
        OrderCollectionFactory $orderCollectionFactory,
        Config $config,
        DateTime $dateTime
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->config = $config;
        $this->dateTime = $dateTime;
    }

    /**
     * Get customers eligible for cleanup
     *
     * @return array Array of customer IDs with reasons
     */
    public function getEligibleCustomers(): array
    {
        if (!$this->config->isEnabled()) {
            return [];
        }

        $eligibleCustomers = [];

        // Get customers who never ordered and account is old enough
        if ($this->config->getNoOrdersDays() > 0) {
            $noOrderCustomers = $this->getCustomersWithoutOrders();
            $eligibleCustomers = array_merge($eligibleCustomers, $noOrderCustomers);
        }

        // Get customers who haven't logged in for a long time
        if ($this->config->getInactiveDays() > 0) {
            $inactiveCustomers = $this->getInactiveCustomers();
            $eligibleCustomers = array_merge($eligibleCustomers, $inactiveCustomers);
        }

        // Get customers whose last order is very old
        if ($this->config->getLastOrderYears() > 0) {
            $oldOrderCustomers = $this->getCustomersWithOldOrders();
            $eligibleCustomers = array_merge($eligibleCustomers, $oldOrderCustomers);
        }

        // Remove duplicates, keeping first reason
        $uniqueCustomers = [];
        foreach ($eligibleCustomers as $customerData) {
            $customerId = $customerData['customer_id'];
            if (!isset($uniqueCustomers[$customerId])) {
                $uniqueCustomers[$customerId] = $customerData;
            }
        }

        return array_values($uniqueCustomers);
    }

    /**
     * Get customers without orders
     *
     * @return array
     */
    private function getCustomersWithoutOrders(): array
    {
        $customers = [];
        $daysThreshold = $this->config->getNoOrdersDays();
        $dateThreshold = date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp() - ($daysThreshold * 86400));

        $customerCollection = $this->customerCollectionFactory->create();
        $customerCollection->addAttributeToFilter('created_at', ['lt' => $dateThreshold]);

        if ($this->config->includeNeverLoggedIn()) {
            // Include customers who never logged in
            $customerCollection->addAttributeToFilter(
                [
                    ['attribute' => 'last_logged_in', 'null' => true],
                    ['attribute' => 'last_logged_in', 'eq' => '']
                ]
            );
        }

        foreach ($customerCollection as $customer) {
            // Check if customer has any orders
            $orderCollection = $this->orderCollectionFactory->create();
            $orderCollection->addFieldToFilter('customer_id', $customer->getId());
            
            if ($orderCollection->getSize() === 0) {
                $customers[] = [
                    'customer_id' => $customer->getId(),
                    'reason' => sprintf(
                        'No orders since registration %d days ago',
                        $daysThreshold
                    )
                ];
            }
        }

        return $customers;
    }

    /**
     * Get inactive customers (no login for configured days)
     *
     * @return array
     */
    private function getInactiveCustomers(): array
    {
        $customers = [];
        $daysThreshold = $this->config->getInactiveDays();
        $dateThreshold = date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp() - ($daysThreshold * 86400));

        $customerCollection = $this->customerCollectionFactory->create();
        $customerCollection->addAttributeToFilter('last_logged_in', ['lt' => $dateThreshold]);

        foreach ($customerCollection as $customer) {
            $customers[] = [
                'customer_id' => $customer->getId(),
                'reason' => sprintf(
                    'No login for %d days (last login: %s)',
                    $daysThreshold,
                    $customer->getLastLoggedIn() ?: 'never'
                )
            ];
        }

        return $customers;
    }

    /**
     * Get customers with old orders
     *
     * @return array
     */
    private function getCustomersWithOldOrders(): array
    {
        $customers = [];
        $yearsThreshold = $this->config->getLastOrderYears();
        
        // Ensure minimum 10 years for legal compliance
        if ($yearsThreshold < 10) {
            $yearsThreshold = 10;
        }

        $dateThreshold = date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp() - ($yearsThreshold * 365 * 86400));

        // Get all orders older than threshold
        $orderCollection = $this->orderCollectionFactory->create();
        $orderCollection->addFieldToFilter('created_at', ['lt' => $dateThreshold]);
        $orderCollection->getSelect()
            ->group('customer_id')
            ->having('MAX(created_at) < ?', $dateThreshold);

        $customerIds = [];
        foreach ($orderCollection as $order) {
            if ($order->getCustomerId()) {
                $customerIds[$order->getCustomerId()] = $order->getCreatedAt();
            }
        }

        foreach ($customerIds as $customerId => $lastOrderDate) {
            // Check if customer has any newer orders
            $newerOrders = $this->orderCollectionFactory->create();
            $newerOrders->addFieldToFilter('customer_id', $customerId);
            $newerOrders->addFieldToFilter('created_at', ['gteq' => $dateThreshold]);

            if ($newerOrders->getSize() === 0) {
                $customers[] = [
                    'customer_id' => $customerId,
                    'reason' => sprintf(
                        'Last order more than %d years ago (last order: %s)',
                        $yearsThreshold,
                        $lastOrderDate
                    )
                ];
            }
        }

        return $customers;
    }

    /**
     * Check if specific customer is eligible for cleanup
     *
     * @param int $customerId
     * @return array|null Returns reason array or null if not eligible
     */
    public function isCustomerEligible(int $customerId): ?array
    {
        $eligibleCustomers = $this->getEligibleCustomers();
        
        foreach ($eligibleCustomers as $customerData) {
            if ($customerData['customer_id'] == $customerId) {
                return $customerData;
            }
        }

        return null;
    }
}
