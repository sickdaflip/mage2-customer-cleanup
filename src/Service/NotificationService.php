<?php
/**
 * Notification Service
 * Handles sending email notifications to customers
 */
declare(strict_types=1);

namespace Sickdaflip\CustomerCleanup\Service;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Sickdaflip\CustomerCleanup\Helper\Config;
use Sickdaflip\CustomerCleanup\Logger\Logger;

class NotificationService
{
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var StateInterface
     */
    private $inlineTranslation;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Constructor
     *
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param StoreManagerInterface $storeManager
     * @param Config $config
     * @param Logger $logger
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        StoreManagerInterface $storeManager,
        Config $config,
        Logger $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Send warning email to customer
     *
     * @param CustomerInterface $customer
     * @param int $daysUntilDeletion
     * @return bool
     */
    public function sendWarningEmail(CustomerInterface $customer, int $daysUntilDeletion): bool
    {
        try {
            $this->inlineTranslation->suspend();

            $storeId = $customer->getStoreId();
            $store = $this->storeManager->getStore($storeId);

            $templateVars = [
                'customer' => $customer,
                'days' => $daysUntilDeletion,
                'store' => $store
            ];

            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ];

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->config->getEmailTemplate($storeId))
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFromByScope($this->config->getSenderEmail($storeId), $storeId)
                ->addTo($customer->getEmail(), $customer->getFirstname() . ' ' . $customer->getLastname())
                ->getTransport();

            $transport->sendMessage();

            $this->inlineTranslation->resume();

            return true;

        } catch (\Exception $e) {
            $this->logger->error('Failed to send warning email: ' . $e->getMessage());
            $this->inlineTranslation->resume();
            return false;
        }
    }
}
