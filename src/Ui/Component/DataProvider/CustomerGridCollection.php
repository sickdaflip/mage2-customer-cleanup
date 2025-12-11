<?php
/**
 * Customer Grid Collection for Cleanup Module
 * Filters customers based on configured cleanup criteria
 */
declare(strict_types=1);

namespace FlipDev\CustomerCleanup\Ui\Component\DataProvider;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;

class CustomerGridCollection extends SearchResult
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var bool
     */
    private $filtersApplied = false;

    /**
     * Constructor
     *
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param ScopeConfigInterface $scopeConfig
     * @param DateTime $dateTime
     * @param string $mainTable
     * @param string|null $resourceModel
     * @param string|null $identifierName
     * @param string|null $connectionName
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        ScopeConfigInterface $scopeConfig,
        DateTime $dateTime,
        string $mainTable = 'customer_entity',
        ?string $resourceModel = null,
        ?string $identifierName = null,
        ?string $connectionName = null,
        ?AdapterInterface $connection = null,
        ?AbstractDb $resource = null
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->dateTime = $dateTime;

        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel,
            $identifierName,
            $connectionName
        );
    }

    /**
     * Initialize select
     *
     * @return void
     */
    protected function _initSelect(): void
    {
        parent::_initSelect();
        $this->applyCleanupFilters();
    }

    /**
     * Apply cleanup criteria filters
     *
     * @return void
     */
    private function applyCleanupFilters(): void
    {
        if ($this->filtersApplied) {
            return;
        }

        $this->filtersApplied = true;

        // Check if module is enabled
        $isEnabled = $this->scopeConfig->isSetFlag('customercleanup/general/enabled');

        if (!$isEnabled) {
            // If module is disabled, show no customers
            $this->getSelect()->where('1 = 0');
            return;
        }

        $noOrdersDays = (int)$this->scopeConfig->getValue('customercleanup/criteria/no_orders_days');
        $inactiveDays = (int)$this->scopeConfig->getValue('customercleanup/criteria/inactive_days');
        $lastOrderYears = (int)$this->scopeConfig->getValue('customercleanup/criteria/last_order_years');
        $includeNeverLoggedIn = $this->scopeConfig->isSetFlag('customercleanup/criteria/never_logged_in');

        // Ensure minimum 10 years for legal compliance
        if ($lastOrderYears > 0 && $lastOrderYears < 10) {
            $lastOrderYears = 10;
        }

        $conditions = [];
        $connection = $this->getConnection();

        // Join customer_log table to get last login date
        $this->getSelect()->joinLeft(
            ['customer_log' => $this->getTable('customer_log')],
            'customer_log.customer_id = main_table.entity_id',
            ['last_login_at']
        );

        // Filter 1: Customers without orders (account older than threshold)
        if ($noOrdersDays > 0) {
            $dateThreshold = date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp() - ($noOrdersDays * 86400));

            // Subquery to check if customer has any orders
            $orderSubquery = $connection->select()
                ->from(['so' => $this->getTable('sales_order')], ['customer_id'])
                ->where('so.customer_id = main_table.entity_id');

            $noOrderCondition = 'main_table.created_at < ' . $connection->quote($dateThreshold)
                . ' AND NOT EXISTS (' . $orderSubquery . ')';

            if ($includeNeverLoggedIn) {
                // Only include customers who never logged in for this criteria
                $noOrderCondition .= ' AND (customer_log.last_login_at IS NULL)';
            }

            $conditions[] = '(' . $noOrderCondition . ')';
        }

        // Filter 2: Inactive customers (no login for configured days)
        if ($inactiveDays > 0) {
            $dateThreshold = date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp() - ($inactiveDays * 86400));

            // Customers who have logged in before but not recently
            $inactiveCondition = '(customer_log.last_login_at IS NOT NULL AND customer_log.last_login_at < ' . $connection->quote($dateThreshold) . ')';

            // Also include customers who never logged in and account is old enough
            if ($includeNeverLoggedIn) {
                $inactiveCondition .= ' OR (customer_log.last_login_at IS NULL AND main_table.created_at < ' . $connection->quote($dateThreshold) . ')';
            }

            $conditions[] = '(' . $inactiveCondition . ')';
        }

        // Filter 3: Customers whose last order is very old
        if ($lastOrderYears > 0) {
            $dateThreshold = date('Y-m-d H:i:s', $this->dateTime->gmtTimestamp() - ($lastOrderYears * 365 * 86400));

            // Subquery to get max order date per customer
            $maxOrderSubquery = $connection->select()
                ->from(['so' => $this->getTable('sales_order')], ['MAX(so.created_at)'])
                ->where('so.customer_id = main_table.entity_id');

            // Customer has orders, but all orders are older than threshold
            $oldOrderCondition = 'EXISTS (SELECT 1 FROM ' . $this->getTable('sales_order') . ' so WHERE so.customer_id = main_table.entity_id)'
                . ' AND (' . $maxOrderSubquery . ') < ' . $connection->quote($dateThreshold);

            $conditions[] = '(' . $oldOrderCondition . ')';
        }

        // If no criteria are configured, show no customers
        if (empty($conditions)) {
            $this->getSelect()->where('1 = 0');
            return;
        }

        // Combine conditions with OR (customer matches ANY criteria)
        $this->getSelect()->where(implode(' OR ', $conditions));
    }
}
