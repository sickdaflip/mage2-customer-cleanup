<?php
/**
 * CleanupLog Collection
 * Collection of cleanup log entries
 */
declare(strict_types=1);

namespace Sickdaflip\CustomerCleanup\Model\ResourceModel\CleanupLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Sickdaflip\CustomerCleanup\Model\CleanupLog;
use Sickdaflip\CustomerCleanup\Model\ResourceModel\CleanupLog as CleanupLogResource;

class Collection extends AbstractCollection
{
    /**
     * ID field name
     *
     * @var string
     */
    protected $_idFieldName = 'log_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CleanupLog::class, CleanupLogResource::class);
    }
}
