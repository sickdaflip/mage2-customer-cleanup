<?php
/**
 * Email Template Source Model
 * Provides email template options for admin configuration
 */
declare(strict_types=1);

namespace FlipDev\CustomerCleanup\Model\Config\Source\Email;

use Magento\Config\Model\Config\Source\Email\Template as BaseTemplate;
use Magento\Email\Model\ResourceModel\Template\CollectionFactory;
use Magento\Email\Model\Template\Config;
use Magento\Framework\Registry;

class Template extends BaseTemplate
{
    /**
     * Original template code for this source model
     */
    private const ORIG_TEMPLATE_CODE = 'customercleanup_notification_warning';

    /**
     * Constructor
     *
     * @param Registry $coreRegistry
     * @param CollectionFactory $templatesFactory
     * @param Config $emailConfig
     * @param array $data
     */
    public function __construct(
        Registry $coreRegistry,
        CollectionFactory $templatesFactory,
        Config $emailConfig,
        array $data = []
    ) {
        parent::__construct($coreRegistry, $templatesFactory, $emailConfig, $data);
        $this->setPath(self::ORIG_TEMPLATE_CODE);
    }

    /**
     * Set the original template code path
     *
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->_origTemplateCode = $path;
        return $this;
    }
}
