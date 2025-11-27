<?php
/**
 * Database Schema Setup
 * Creates cleanup_log table for tracking all cleanup operations
 */
declare(strict_types=1);

namespace Sickdaflip\CustomerCleanup\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Install database schema
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        // Create cleanup_log table
        $table = $setup->getConnection()->newTable(
            $setup->getTable('sickdaflip_customer_cleanup_log')
        )->addColumn(
            'log_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
            'Log ID'
        )->addColumn(
            'customer_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Customer ID'
        )->addColumn(
            'customer_email',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Customer Email'
        )->addColumn(
            'customer_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Customer Name'
        )->addColumn(
            'action_type',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Action Type (deleted, anonymized, notification_sent)'
        )->addColumn(
            'reason',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Reason for Cleanup'
        )->addColumn(
            'dry_run',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Was Dry Run'
        )->addColumn(
            'admin_user',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Admin User Who Initiated'
        )->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Created At'
        )->addIndex(
            $setup->getIdxName('sickdaflip_customer_cleanup_log', ['customer_id']),
            ['customer_id']
        )->addIndex(
            $setup->getIdxName('sickdaflip_customer_cleanup_log', ['customer_email']),
            ['customer_email']
        )->addIndex(
            $setup->getIdxName('sickdaflip_customer_cleanup_log', ['action_type']),
            ['action_type']
        )->addIndex(
            $setup->getIdxName('sickdaflip_customer_cleanup_log', ['created_at']),
            ['created_at']
        )->setComment(
            'Customer Cleanup Log Table'
        );

        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
