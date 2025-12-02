<?php
/**
 * CleanupLog Resource Model
 * Handles database operations for cleanup logs
 */
declare(strict_types=1);

namespace FlipDev\CustomerCleanup\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CleanupLog extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sickdaflip_customer_cleanup_log', 'log_id');
    }
}
