<?php
/**
 * Custom Logger Handler
 * Writes cleanup logs to separate file
 */
declare(strict_types=1);

namespace FlipDev\CustomerCleanup\Logger;

use Monolog\Logger;
use Magento\Framework\Logger\Handler\Base;

class Handler extends Base
{
    /**
     * Logging level
     *
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * Log file name
     *
     * @var string
     */
    protected $fileName = '/var/log/customer_cleanup.log';
}
