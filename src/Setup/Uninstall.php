<?php
/**
 * Uninstall Script
 * Removes all module data and tables when uninstalling
 */
declare(strict_types=1);

namespace Sickdaflip\CustomerCleanup\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class Uninstall implements UninstallInterface
{
    /**
     * Module uninstall code
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function uninstall(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $connection = $setup->getConnection();

        // Drop cleanup log table
        $connection->dropTable($setup->getTable('sickdaflip_customer_cleanup_log'));

        // Remove configuration values
        $configTable = $setup->getTable('core_config_data');
        $connection->delete(
            $configTable,
            ["path LIKE ?" => 'customercleanup/%']
        );

        $setup->endSetup();
    }
}
