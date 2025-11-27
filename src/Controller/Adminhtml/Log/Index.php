<?php
/**
 * Cleanup Log Index Controller
 * Displays log of all cleanup operations
 */
declare(strict_types=1);

namespace Sickdaflip\CustomerCleanup\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * Authorization level - requires permission to view cleanup logs
     */
    const ADMIN_RESOURCE = 'Sickdaflip_CustomerCleanup::cleanup_log';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Sickdaflip_CustomerCleanup::cleanup_log');
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Cleanup Log'));

        return $resultPage;
    }
}
