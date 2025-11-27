<?php
/**
 * Mass Notify Controller
 * Sends warning emails to selected customers
 */
declare(strict_types=1);

namespace Sickdaflip\CustomerCleanup\Controller\Adminhtml\Customer;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Sickdaflip\CustomerCleanup\Service\CustomerCleanupService;
use Sickdaflip\CustomerCleanup\Helper\Config;

class MassNotify extends Action
{
    /**
     * Authorization level - requires explicit permission to send notifications
     */
    const ADMIN_RESOURCE = 'Sickdaflip_CustomerCleanup::cleanup_notify';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CustomerCleanupService
     */
    private $cleanupService;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CustomerCleanupService $cleanupService
     * @param CustomerRepositoryInterface $customerRepository
     * @param Config $config
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CustomerCleanupService $cleanupService,
        CustomerRepositoryInterface $customerRepository,
        Config $config
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->cleanupService = $cleanupService;
        $this->customerRepository = $customerRepository;
        $this->config = $config;
    }

    /**
     * Execute mass notification action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        // SAFETY CHECK: Module must be explicitly enabled
        if (!$this->config->isEnabled()) {
            $this->messageManager->addErrorMessage(
                __('Customer Cleanup module is DISABLED in configuration. Please enable it first in Stores > Configuration > Sickdaflip > Customer Cleanup.')
            );
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
        }

        // SAFETY CHECK: Notifications must be enabled
        if (!$this->config->isNotificationEnabled()) {
            $this->messageManager->addErrorMessage(
                __('Email notifications are DISABLED in configuration. Please enable them first in Stores > Configuration > Sickdaflip > Customer Cleanup > Email Notification Settings.')
            );
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
        }

        $isDryRun = $this->config->isDryRunMode();

        // Show prominent warning if in dry run mode
        if ($isDryRun) {
            $this->messageManager->addWarningMessage(
                __('🔒 DRY RUN MODE ACTIVE: No emails will actually be sent. This is a simulation only.')
            );
        } else {
            // Extra warning when NOT in dry run mode
            $this->messageManager->addWarningMessage(
                __('📧 LIVE MODE: Warning emails will be sent to selected customers!')
            );
        }

        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $notifiedCount = 0;
            $warningDays = $this->config->getWarningDays();

            foreach ($collection as $customer) {
                try {
                    $customerModel = $this->customerRepository->getById($customer->getId());
                    
                    if ($this->cleanupService->sendDeletionWarning($customerModel, $warningDays)) {
                        $notifiedCount++;
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(
                        __('Error notifying customer %1: %2', $customer->getEmail(), $e->getMessage())
                    );
                }
            }

            if ($isDryRun) {
                $this->messageManager->addNoticeMessage(
                    __('🔒 DRY RUN COMPLETED: %1 notification(s) would have been sent. NO actual emails were sent. Check the Cleanup Log for details.', 
                        $notifiedCount
                    )
                );
            } else {
                $this->messageManager->addSuccessMessage(
                    __('✅ Warning emails sent to %1 customer(s). They have %2 days to login before deletion. Check the Cleanup Log for details.', 
                        $notifiedCount, 
                        $warningDays
                    )
                );
            }

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred: %1', $e->getMessage()));
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}
