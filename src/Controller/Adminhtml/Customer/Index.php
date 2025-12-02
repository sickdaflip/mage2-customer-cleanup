<?php
/**
 * Customer Cleanup Index Controller
 * Displays grid of customers eligible for cleanup
 */
declare(strict_types=1);

namespace FlipDev\CustomerCleanup\Controller\Adminhtml\Customer;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * Authorization level - requires permission to view cleanup interface
     */
    const ADMIN_RESOURCE = 'Sickdaflip_CustomerCleanup::cleanup_view';

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
        $resultPage->setActiveMenu('Sickdaflip_CustomerCleanup::inactive_customers');
        $resultPage->getConfig()->getTitle()->prepend(__('Inactive Customers'));

        return $resultPage;
    }
}
