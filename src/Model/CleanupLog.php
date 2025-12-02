<?php
/**
 * CleanupLog Model
 * Represents a single cleanup log entry
 */
declare(strict_types=1);

namespace FlipDev\CustomerCleanup\Model;

use Magento\Framework\Model\AbstractModel;
use FlipDev\CustomerCleanup\Api\Data\CleanupLogInterface;

class CleanupLog extends AbstractModel implements CleanupLogInterface
{
    /**
     * Action types
     */
    const ACTION_DELETED = 'deleted';
    const ACTION_ANONYMIZED = 'anonymized';
    const ACTION_NOTIFICATION_SENT = 'notification_sent';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Sickdaflip\CustomerCleanup\Model\ResourceModel\CleanupLog::class);
    }

    /**
     * Get log ID
     *
     * @return int|null
     */
    public function getLogId(): ?int
    {
        return $this->getData(self::LOG_ID) ? (int)$this->getData(self::LOG_ID) : null;
    }

    /**
     * Set log ID
     *
     * @param int $logId
     * @return $this
     */
    public function setLogId(int $logId): CleanupLogInterface
    {
        return $this->setData(self::LOG_ID, $logId);
    }

    /**
     * Get customer ID
     *
     * @return int
     */
    public function getCustomerId(): int
    {
        return (int)$this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set customer ID
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId(int $customerId): CleanupLogInterface
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get customer email
     *
     * @return string
     */
    public function getCustomerEmail(): string
    {
        return (string)$this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * Set customer email
     *
     * @param string $email
     * @return $this
     */
    public function setCustomerEmail(string $email): CleanupLogInterface
    {
        return $this->setData(self::CUSTOMER_EMAIL, $email);
    }

    /**
     * Get customer name
     *
     * @return string|null
     */
    public function getCustomerName(): ?string
    {
        return $this->getData(self::CUSTOMER_NAME);
    }

    /**
     * Set customer name
     *
     * @param string $name
     * @return $this
     */
    public function setCustomerName(string $name): CleanupLogInterface
    {
        return $this->setData(self::CUSTOMER_NAME, $name);
    }

    /**
     * Get action type
     *
     * @return string
     */
    public function getActionType(): string
    {
        return (string)$this->getData(self::ACTION_TYPE);
    }

    /**
     * Set action type
     *
     * @param string $actionType
     * @return $this
     */
    public function setActionType(string $actionType): CleanupLogInterface
    {
        return $this->setData(self::ACTION_TYPE, $actionType);
    }

    /**
     * Get reason
     *
     * @return string|null
     */
    public function getReason(): ?string
    {
        return $this->getData(self::REASON);
    }

    /**
     * Set reason
     *
     * @param string $reason
     * @return $this
     */
    public function setReason(string $reason): CleanupLogInterface
    {
        return $this->setData(self::REASON, $reason);
    }

    /**
     * Get dry run flag
     *
     * @return bool
     */
    public function isDryRun(): bool
    {
        return (bool)$this->getData(self::DRY_RUN);
    }

    /**
     * Set dry run flag
     *
     * @param bool $dryRun
     * @return $this
     */
    public function setDryRun(bool $dryRun): CleanupLogInterface
    {
        return $this->setData(self::DRY_RUN, $dryRun);
    }

    /**
     * Get admin user
     *
     * @return string|null
     */
    public function getAdminUser(): ?string
    {
        return $this->getData(self::ADMIN_USER);
    }

    /**
     * Set admin user
     *
     * @param string $adminUser
     * @return $this
     */
    public function setAdminUser(string $adminUser): CleanupLogInterface
    {
        return $this->setData(self::ADMIN_USER, $adminUser);
    }

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt(): string
    {
        return (string)$this->getData(self::CREATED_AT);
    }

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): CleanupLogInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
