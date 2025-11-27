<?php
/**
 * Action Type Source Model
 * Provides options for action type dropdown in log grid
 */
declare(strict_types=1);

namespace Sickdaflip\CustomerCleanup\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Sickdaflip\CustomerCleanup\Model\CleanupLog;

class ActionType implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => CleanupLog::ACTION_DELETED, 'label' => __('Deleted')],
            ['value' => CleanupLog::ACTION_ANONYMIZED, 'label' => __('Anonymized')],
            ['value' => CleanupLog::ACTION_NOTIFICATION_SENT, 'label' => __('Notification Sent')]
        ];
    }
}
