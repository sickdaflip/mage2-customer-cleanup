<?php
/**
 * Status Banner Block
 * Shows prominent warning banner about module status in admin
 */
declare(strict_types=1);

namespace FlipDev\CustomerCleanup\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use FlipDev\CustomerCleanup\Helper\Config;

class StatusBanner extends Template
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * Check if module is enabled
     *
     * @return bool
     */
    public function isModuleEnabled(): bool
    {
        return $this->config->isEnabled();
    }

    /**
     * Check if in dry run mode
     *
     * @return bool
     */
    public function isDryRunMode(): bool
    {
        return $this->config->isDryRunMode();
    }

    /**
     * Check if notifications are enabled
     *
     * @return bool
     */
    public function isNotificationEnabled(): bool
    {
        return $this->config->isNotificationEnabled();
    }

    /**
     * Get configuration URL
     *
     * @return string
     */
    public function getConfigUrl(): string
    {
        return $this->getUrl('adminhtml/system_config/edit/section/customercleanup');
    }

    /**
     * Get status message
     *
     * @return string
     */
    public function getStatusMessage(): string
    {
        if (!$this->isModuleEnabled()) {
            return (string)__('Module is DISABLED - No cleanup operations will be performed');
        }

        if ($this->isDryRunMode()) {
            return (string)__('DRY RUN MODE - All operations are simulated only, NO actual deletions or emails');
        }

        return (string)__('LIVE MODE - All operations will be executed for real!');
    }

    /**
     * Get status type for styling
     *
     * @return string
     */
    public function getStatusType(): string
    {
        if (!$this->isModuleEnabled()) {
            return 'disabled';
        }

        if ($this->isDryRunMode()) {
            return 'safe';
        }

        return 'danger';
    }
}
