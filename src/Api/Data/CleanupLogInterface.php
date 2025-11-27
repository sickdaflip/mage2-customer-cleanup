<?php
/**
 * CleanupLog Interface
 * Defines contract for cleanup log entries
 */
declare(strict_types=1);

namespace Sickdaflip\CustomerCleanup\Api\Data;

interface CleanupLogInterface
{
    const LOG_ID = 'log_id';
    const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_EMAIL = 'customer_email';
    const CUSTOMER_NAME = 'customer_name';
    const ACTION_TYPE = 'action_type';
    const REASON = 'reason';
    const DRY_RUN = 'dry_run';
    const ADMIN_USER = 'admin_user';
    const CREATED_AT = 'created_at';

    /**
     * Get log ID
     *
     * @return int|null
     */
    public function getLogId(): ?int;

    /**
     * Set log ID
     *
     * @param int $logId
     * @return $this
     */
    public function setLogId(int $logId): self;

    /**
     * Get customer ID
     *
     * @return int
     */
    public function getCustomerId(): int;

    /**
     * Set customer ID
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId(int $customerId): self;

    /**
     * Get customer email
     *
     * @return string
     */
    public function getCustomerEmail(): string;

    /**
     * Set customer email
     *
     * @param string $email
     * @return $this
     */
    public function setCustomerEmail(string $email): self;

    /**
     * Get customer name
     *
     * @return string|null
     */
    public function getCustomerName(): ?string;

    /**
     * Set customer name
     *
     * @param string $name
     * @return $this
     */
    public function setCustomerName(string $name): self;

    /**
     * Get action type
     *
     * @return string
     */
    public function getActionType(): string;

    /**
     * Set action type
     *
     * @param string $actionType
     * @return $this
     */
    public function setActionType(string $actionType): self;

    /**
     * Get reason
     *
     * @return string|null
     */
    public function getReason(): ?string;

    /**
     * Set reason
     *
     * @param string $reason
     * @return $this
     */
    public function setReason(string $reason): self;

    /**
     * Get dry run flag
     *
     * @return bool
     */
    public function isDryRun(): bool;

    /**
     * Set dry run flag
     *
     * @param bool $dryRun
     * @return $this
     */
    public function setDryRun(bool $dryRun): self;

    /**
     * Get admin user
     *
     * @return string|null
     */
    public function getAdminUser(): ?string;

    /**
     * Set admin user
     *
     * @param string $adminUser
     * @return $this
     */
    public function setAdminUser(string $adminUser): self;

    /**
     * Get created at
     *
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): self;
}
