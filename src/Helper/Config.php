<?php
/**
 * Configuration Helper
 * Provides easy access to module configuration values
 */
declare(strict_types=1);

namespace FlipDev\CustomerCleanup\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use FlipDev\Core\Helper\Config as CoreConfig;

class Config extends AbstractHelper
{
    // Configuration paths
    const XML_PATH_ENABLED = 'customercleanup/general/enabled';
    const XML_PATH_DRY_RUN = 'customercleanup/general/dry_run';
    const XML_PATH_INACTIVE_DAYS = 'customercleanup/criteria/inactive_days';
    const XML_PATH_NO_ORDERS_DAYS = 'customercleanup/criteria/no_orders_days';
    const XML_PATH_LAST_ORDER_YEARS = 'customercleanup/criteria/last_order_years';
    const XML_PATH_NEVER_LOGGED_IN = 'customercleanup/criteria/never_logged_in';
    const XML_PATH_NOTIFICATION_ENABLED = 'customercleanup/notification/enabled';
    const XML_PATH_WARNING_DAYS = 'customercleanup/notification/warning_days';
    const XML_PATH_SENDER_EMAIL = 'customercleanup/notification/sender_email';
    const XML_PATH_EMAIL_TEMPLATE = 'customercleanup/notification/email_template';
    const XML_PATH_ANONYMIZE_ORDERS = 'customercleanup/deletion/anonymize_orders';
    const XML_PATH_DELETE_ADDRESSES = 'customercleanup/deletion/delete_addresses';
    const XML_PATH_DELETE_REVIEWS = 'customercleanup/deletion/delete_reviews';

    /**
     * @var CoreConfig
     */
    private $coreConfig;

    /**
     * Constructor
     *
     * @param Context $context
     * @param CoreConfig $coreConfig
     */
    public function __construct(
        Context $context,
        CoreConfig $coreConfig
    ) {
        parent::__construct($context);
        $this->coreConfig = $coreConfig;
    }

    /**
     * Check if module is enabled
     * Also checks if FlipDev_Core is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled(?int $storeId = null): bool
    {
        // First check if FlipDev Core is enabled
        if (!$this->coreConfig->isEnabled($storeId)) {
            return false;
        }

        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if debug mode is enabled (from FlipDev_Core)
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isDebugMode(?int $storeId = null): bool
    {
        return $this->coreConfig->isDebugMode($storeId);
    }

    /**
     * Check if dry run mode is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isDryRunMode(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DRY_RUN,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get inactive days threshold
     *
     * @param int|null $storeId
     * @return int
     */
    public function getInactiveDays(?int $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_INACTIVE_DAYS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get no orders days threshold
     *
     * @param int|null $storeId
     * @return int
     */
    public function getNoOrdersDays(?int $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_NO_ORDERS_DAYS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get last order years threshold
     *
     * @param int|null $storeId
     * @return int
     */
    public function getLastOrderYears(?int $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_LAST_ORDER_YEARS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if should include never logged in customers
     *
     * @param int|null $storeId
     * @return bool
     */
    public function includeNeverLoggedIn(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_NEVER_LOGGED_IN,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if email notifications are enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isNotificationEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_NOTIFICATION_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get warning days before deletion
     *
     * @param int|null $storeId
     * @return int
     */
    public function getWarningDays(?int $storeId = null): int
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_WARNING_DAYS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get email sender identity
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSenderEmail(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_SENDER_EMAIL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get email template ID
     *
     * @param int|null $storeId
     * @return string
     */
    public function getEmailTemplate(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if should anonymize orders instead of delete
     *
     * @param int|null $storeId
     * @return bool
     */
    public function shouldAnonymizeOrders(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ANONYMIZE_ORDERS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if should delete customer addresses
     *
     * @param int|null $storeId
     * @return bool
     */
    public function shouldDeleteAddresses(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DELETE_ADDRESSES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if should delete customer reviews
     *
     * @param int|null $storeId
     * @return bool
     */
    public function shouldDeleteReviews(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DELETE_REVIEWS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
