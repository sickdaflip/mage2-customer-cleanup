<?php
/**
 * Mass Delete Controller
 * Handles batch deletion of selected customers
 */
declare(strict_types=1);

namespace FlipDev\CustomerCleanup\Controller\Adminhtml\Customer;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use FlipDev\CustomerCleanup\Service\CustomerCleanupService;
use FlipDev\CustomerCleanup\Helper\Config;

class MassDelete extends Action
{
    /**
     * Authorization level - requires explicit permission to delete customers
     */
    const ADMIN_RESOURCE = 'Sickdaflip_CustomerCleanup::cleanup_delete';

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
     * @var Session
     */
    private $authSession;

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
     * @param Session $authSession
     * @param Config $config
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CustomerCleanupService $cleanupService,
        CustomerRepositoryInterface $customerRepository,
        Session $authSession,
        Config $config
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->cleanupService = $cleanupService;
        $this->customerRepository = $customerRepository;
        $this->authSession = $authSession;
        $this->config = $config;
    }

    /**
     * Execute mass delete action
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

        $isDryRun = $this->config->isDryRunMode();

        // Show prominent warning if in dry run mode
        if ($isDryRun) {
            $this->messageManager->addWarningMessage(
                __('🔒 DRY RUN MODE ACTIVE: No customers will actually be deleted. This is a simulation only.')
            );
        } else {
            // Extra warning when NOT in dry run mode
            $this->messageManager->addWarningMessage(
                __('⚠️ LIVE MODE: Customers will be PERMANENTLY deleted! Make sure you have a backup!')
            );
        }

        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();
            $deletedCount = 0;
            $adminUser = $this->authSession->getUser()->getUsername();

            foreach ($collection as $customer) {
                try {
                    $customerModel = $this->customerRepository->getById($customer->getId());
                    $reason = sprintf('Manual deletion via admin by %s', $adminUser);
                    
                    $this->cleanupService->cleanupCustomer($customerModel, $reason, $adminUser);
                    $deletedCount++;
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(
                        __('Error processing customer %1: %2', $customer->getEmail(), $e->getMessage())
                    );
                }
            }

            if ($isDryRun) {
                $this->messageManager->addNoticeMessage(
                    __('🔒 DRY RUN COMPLETED: %1 customer(s) would have been processed. NO actual changes were made. Check the Cleanup Log for details.', $deletedCount)
                );
            } else {
                $this->messageManager->addSuccessMessage(
                    __('✅ Successfully processed %1 customer(s). Check the Cleanup Log for details.', $deletedCount)
                );
            }

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred: %1', $e->getMessage()));
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}
