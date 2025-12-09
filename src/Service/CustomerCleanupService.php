<?php
/**
 * Customer Cleanup Service
 * Main service for handling customer cleanup operations
 */
declare(strict_types=1);

namespace FlipDev\CustomerCleanup\Service;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use FlipDev\CustomerCleanup\Helper\Config;
use FlipDev\Core\Logger\Logger;
use FlipDev\CustomerCleanup\Model\CleanupLog;
use FlipDev\CustomerCleanup\Model\CleanupLogFactory;
use Magento\Framework\Registry;

class CustomerCleanupService
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var CleanupLogFactory
     */
    private $cleanupLogFactory;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var NotificationService
     */
    private $notificationService;

    /**
     * Constructor
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderCollectionFactory $orderCollectionFactory
     * @param Config $config
     * @param Logger $logger
     * @param CleanupLogFactory $cleanupLogFactory
     * @param Registry $registry
     * @param NotificationService $notificationService
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        OrderRepositoryInterface $orderRepository,
        OrderCollectionFactory $orderCollectionFactory,
        Config $config,
        Logger $logger,
        CleanupLogFactory $cleanupLogFactory,
        Registry $registry,
        NotificationService $notificationService
    ) {
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->config = $config;
        $this->logger = $logger;
        $this->cleanupLogFactory = $cleanupLogFactory;
        $this->registry = $registry;
        $this->notificationService = $notificationService;
    }

    /**
     * Delete or anonymize customer
     *
     * @param CustomerInterface $customer
     * @param string $reason
     * @param string|null $adminUser
     * @return bool
     * @throws LocalizedException
     */
    public function cleanupCustomer(CustomerInterface $customer, string $reason, ?string $adminUser = null): bool
    {
        $customerId = $customer->getId();
        $customerEmail = $customer->getEmail();
        $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
        $isDryRun = $this->config->isDryRunMode();

        try {
            // Log the operation
            if ($this->config->isDebugMode()) {
                $this->logger->logWithModule(
                    'info',
                    'FlipDev_CustomerCleanup',
                    sprintf('[%s] Processing customer', $isDryRun ? 'DRY RUN' : 'LIVE'),
                    [
                        'customer_id' => $customerId,
                        'customer_email' => $customerEmail,
                        'reason' => $reason
                    ]
                );
            }

            if (!$isDryRun) {
                // Check if customer has orders
                $orderCollection = $this->orderCollectionFactory->create();
                $orderCollection->addFieldToFilter('customer_id', $customerId);
                $hasOrders = $orderCollection->getSize() > 0;

                if ($hasOrders && $this->config->shouldAnonymizeOrders()) {
                    // Anonymize customer data in orders
                    $this->anonymizeCustomerOrders($customerId);
                    $actionType = CleanupLog::ACTION_ANONYMIZED;
                } else {
                    $actionType = CleanupLog::ACTION_DELETED;
                }

                // Prevent deletion of related entities by setting registry flag
                $this->registry->register('isSecureArea', true);

                // Delete the customer
                $this->customerRepository->delete($customer);

                // Unset registry flag
                $this->registry->unregister('isSecureArea');

                $this->logger->logWithModule(
                    'info',
                    'FlipDev_CustomerCleanup',
                    sprintf('Customer successfully %s', $actionType === CleanupLog::ACTION_ANONYMIZED ? 'anonymized' : 'deleted'),
                    [
                        'customer_id' => $customerId,
                        'customer_email' => $customerEmail,
                        'action' => $actionType
                    ]
                );
            } else {
                $actionType = CleanupLog::ACTION_DELETED;
            }

            // Create log entry
            $this->createLogEntry(
                $customerId,
                $customerEmail,
                $customerName,
                $actionType,
                $reason,
                $isDryRun,
                $adminUser
            );

            return true;

        } catch (\Exception $e) {
            $this->logger->logWithModule(
                'error',
                'FlipDev_CustomerCleanup',
                'Error cleaning up customer: ' . $e->getMessage(),
                [
                    'customer_id' => $customerId,
                    'customer_email' => $customerEmail,
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]
            );
            throw new LocalizedException(__('Failed to cleanup customer: %1', $e->getMessage()));
        }
    }

    /**
     * Anonymize customer data in orders
     *
     * @param int $customerId
     * @return void
     */
    private function anonymizeCustomerOrders(int $customerId): void
    {
        $orderCollection = $this->orderCollectionFactory->create();
        $orderCollection->addFieldToFilter('customer_id', $customerId);

        foreach ($orderCollection as $order) {
            // Anonymize customer information in order
            $order->setCustomerEmail('deleted@customer.com');
            $order->setCustomerFirstname('Deleted');
            $order->setCustomerLastname('Customer');
            
            // Anonymize billing address
            $billingAddress = $order->getBillingAddress();
            if ($billingAddress) {
                $billingAddress->setFirstname('Deleted');
                $billingAddress->setLastname('Customer');
                $billingAddress->setEmail('deleted@customer.com');
                $billingAddress->setTelephone('000000000');
                $billingAddress->save();
            }

            // Anonymize shipping address
            $shippingAddress = $order->getShippingAddress();
            if ($shippingAddress) {
                $shippingAddress->setFirstname('Deleted');
                $shippingAddress->setLastname('Customer');
                $shippingAddress->setEmail('deleted@customer.com');
                $shippingAddress->setTelephone('000000000');
                $shippingAddress->save();
            }

            $this->orderRepository->save($order);
        }

        if ($this->config->isDebugMode()) {
            $this->logger->logWithModule(
                'info',
                'FlipDev_CustomerCleanup',
                'Anonymized orders for customer',
                [
                    'customer_id' => $customerId,
                    'order_count' => $orderCollection->getSize()
                ]
            );
        }
    }

    /**
     * Send notification to customer before deletion
     *
     * @param CustomerInterface $customer
     * @param int $daysUntilDeletion
     * @return bool
     */
    public function sendDeletionWarning(CustomerInterface $customer, int $daysUntilDeletion): bool
    {
        if (!$this->config->isNotificationEnabled()) {
            return false;
        }

        $isDryRun = $this->config->isDryRunMode();

        try {
            if (!$isDryRun) {
                $this->notificationService->sendWarningEmail($customer, $daysUntilDeletion);
            }

            // Log notification
            $this->createLogEntry(
                (int)$customer->getId(),
                $customer->getEmail(),
                $customer->getFirstname() . ' ' . $customer->getLastname(),
                CleanupLog::ACTION_NOTIFICATION_SENT,
                sprintf('Warning sent: Account will be deleted in %d days', $daysUntilDeletion),
                $isDryRun,
                null
            );

            if ($this->config->isDebugMode()) {
                $this->logger->logWithModule(
                    'info',
                    'FlipDev_CustomerCleanup',
                    sprintf('[%s] Notification sent to customer', $isDryRun ? 'DRY RUN' : 'LIVE'),
                    [
                        'customer_id' => $customer->getId(),
                        'customer_email' => $customer->getEmail(),
                        'days_until_deletion' => $daysUntilDeletion
                    ]
                );
            }

            return true;

        } catch (\Exception $e) {
            $this->logger->logWithModule(
                'error',
                'FlipDev_CustomerCleanup',
                'Failed to send notification to customer: ' . $e->getMessage(),
                [
                    'customer_id' => $customer->getId(),
                    'customer_email' => $customer->getEmail(),
                    'exception' => get_class($e)
                ]
            );
            return false;
        }
    }

    /**
     * Create cleanup log entry
     *
     * @param int $customerId
     * @param string $email
     * @param string $name
     * @param string $actionType
     * @param string $reason
     * @param bool $isDryRun
     * @param string|null $adminUser
     * @return void
     */
    private function createLogEntry(
        int $customerId,
        string $email,
        string $name,
        string $actionType,
        string $reason,
        bool $isDryRun,
        ?string $adminUser
    ): void {
        try {
            $log = $this->cleanupLogFactory->create();
            $log->setCustomerId($customerId);
            $log->setCustomerEmail($email);
            $log->setCustomerName($name);
            $log->setActionType($actionType);
            $log->setReason($reason);
            $log->setDryRun($isDryRun);
            if ($adminUser) {
                $log->setAdminUser($adminUser);
            }
            $log->save();
        } catch (\Exception $e) {
            $this->logger->logWithModule(
                'error',
                'FlipDev_CustomerCleanup',
                'Failed to create log entry: ' . $e->getMessage(),
                [
                    'customer_id' => $customerId,
                    'customer_email' => $email,
                    'exception' => get_class($e)
                ]
            );
        }
    }
}
