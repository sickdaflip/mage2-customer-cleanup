# FlipDev_CustomerCleanup

Magento 2.4.8 module for GDPR/DSGVO-compliant customer data cleanup. Helps merchants manage inactive customers and fulfill the "right to be forgotten" obligations.

> **Module Structure**: This module follows modern PHP package structure with source code in `src/` directory and PSR-4 autoloading.

> **PHP 8.4 Required**: This module requires PHP 8.4 or higher for optimal performance and security.

## Requirements

- **PHP**: 8.4 or higher
- **Magento**: 2.4.8 or compatible version
- **Dependency**: FlipDev_Core module (required)

## Features

- **Flexible Cleanup Criteria**
  - Customers without orders after X days
  - Customers inactive (no login) for X days
  - Customers with last order older than X years (minimum 10 for legal compliance)
  - Customers who never logged in

- **Email Notifications**
  - Send warning emails before deletion
  - Configurable warning period
  - Customizable email templates

- **Data Protection**
  - Dry run mode for testing
  - Order anonymization instead of deletion (for legal compliance)
  - Comprehensive audit logging

- **Security & Permissions**
  - Granular ACL (Access Control Lists)
  - Separate permissions for view, delete, notify, and configure
  - All actions logged with admin username
  - Role-based access control

- **Admin Interface**
  - Grid view of inactive customers
  - Mass actions (delete, send notifications)
  - Cleanup log with full history
  - Easy configuration
  - Visual status warnings

## Installation

### Option 1: Composer Installation (Recommended)

```bash
composer require sickdaflip/mage2-customer-cleanup
bin/magento module:enable FlipDev_CustomerCleanup
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

### Option 2: Manual Installation

1. Copy the module to your Magento installation
2. Enable the module:
   ```bash
   bin/magento module:enable FlipDev_CustomerCleanup
   bin/magento setup:upgrade
   bin/magento setup:di:compile
   bin/magento cache:flush
   ```

### After Installation/Update

Always run these commands after installing or updating the module:

```bash
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:clean
bin/magento cache:flush
```

## Uninstallation

To completely remove the module and all its data:

```bash
bin/magento module:disable FlipDev_CustomerCleanup
bin/magento setup:upgrade
# Then remove the module via composer or manually
```

The uninstall process will:
- Drop the cleanup log database table
- Remove all module configuration from `core_config_data`
- Clean up all module data

## Safety Features

### Triple-Layer Safety System

1. **Module Disabled by Default**
   - After installation, the module is completely disabled
   - No operations can be performed until explicitly enabled
   - Must be configured before first use

2. **Dry Run Mode (DEFAULT: ON)**
   - All operations are simulated only
   - No actual deletions or emails are sent
   - Full logging shows what WOULD happen
   - **Always test with Dry Run first!**

3. **Visual Warning System**
   - Prominent status banner on every admin page
   - Color-coded warnings (Gray=Disabled, Yellow=Dry Run, Red=Live)
   - Clear indication of current mode
   - Direct link to configuration

4. **Email Notifications Disabled by Default**
   - Prevents accidental email spam
   - Must be explicitly enabled
   - Respects Dry Run Mode (no emails in test mode)

### Status Banner Examples

When you open the module in admin, you'll see:

- **Module DISABLED**: Gray banner - Nothing will happen
- **DRY RUN MODE**: Yellow banner - Safe testing mode, only logging
- **LIVE MODE**: Red pulsing banner - Real operations, deletions will occur!

## Configuration

Navigate to: **Stores > Configuration > FlipDev > Customer Cleanup**

### Configuration Best Practices

**STEP 1: Initial Setup (SAFE)**
- Keep "Enable Module" = NO
- Keep "Dry Run Mode" = YES
- Keep "Enable Email Notifications" = NO
- Configure your cleanup criteria

**STEP 2: Testing Phase**
- Enable Module = YES
- Keep "Dry Run Mode" = YES (still safe!)
- Test mass actions in admin
- Review cleanup logs to see what would happen

**STEP 3: Email Testing (Optional)**
- Enable "Email Notifications" = YES
- Keep "Dry Run Mode" = YES (no real emails!)
- Test email notifications
- Check logs for email simulation entries

**STEP 4: Production Use (CAREFUL!)**
- Set "Dry Run Mode" = NO
- Make a full database backup first!
- Test on staging environment first!
- Start with small batches

### General Settings
- **Enable Module**: Turn the module on/off
- **Dry Run Mode**: Test without actually deleting data (highly recommended for initial testing!)

### Cleanup Criteria
- **Days Since Last Login**: Delete customers who haven't logged in for X days (0 = disabled)
- **Days Since Account Creation (No Orders)**: Delete customers who registered but never ordered after X days (0 = disabled)
- **Years Since Last Order**: Delete customers with last order older than X years (minimum 10 for GDPR/HGB compliance)
- **Include Never Logged In**: Include customers who created account but never logged in

### Email Notification Settings
- **Enable Email Notifications**: Send warning emails before deletion
- **Warning Period (Days)**: How many days before deletion to send warning
- **Email Sender**: Which email identity to use
- **Email Template**: Customize the warning email

### Deletion Settings
- **Anonymize Instead of Delete Orders**: Keep orders but anonymize customer data (recommended for legal compliance!)
- **Delete Customer Addresses**: Remove stored addresses
- **Delete Customer Reviews**: Remove customer reviews

## Usage

### Setting Up Permissions

The module uses granular ACL (Access Control Lists) to control who can do what.

**Navigate to:** System > Permissions > User Roles

**Available Permissions:**
- **View Inactive Customers** - Access to customer grid (read-only)
- **Delete Customers** - Execute deletion operations
- **Send Notifications** - Send warning emails to customers
- **View Cleanup Log** - Access audit logs
- **Customer Cleanup Configuration** - Change module settings

**Recommended Setup:**
- Grant "Delete Customers" only to senior staff
- Grant "Configuration" only to system administrators
- All operations are logged with admin username

### Admin Interface

1. **View Inactive Customers**
   - Navigate to: **Customer Cleanup > Inactive Customers**
   - View all customers matching your cleanup criteria
   - Filter and search as needed

2. **Send Warning Emails**
   - Select customers in the grid
   - Choose "Send Warning Email" from mass actions
   - Customers receive notification with warning period

3. **Delete Customers**
   - Select customers in the grid
   - Choose "Delete" from mass actions
   - Confirm the action

4. **View Cleanup Log**
   - Navigate to: **Customer Cleanup > Cleanup Log**
   - Review all cleanup actions
   - Filter by action type, date, admin user, etc.

### Important Notes

**Safety First Approach**
- Module is disabled by default after installation
- Dry Run Mode is enabled by default
- Email notifications are disabled by default
- Visual status banner shows current mode at all times
- All operations are logged for audit trail

**Always start with Dry Run Mode enabled!**
- Test your configuration thoroughly
- Review the logs to see what would happen
- Test on staging environment first
- Only disable Dry Run when you're confident

**Legal Compliance**
- German/EU law requires keeping invoices for 10 years (HGB §257, AO §147)
- Enable "Anonymize Orders" to comply with retention requirements
- Orders are kept but customer data is anonymized
- Consult with a lawyer for specific compliance requirements

**Backups**
- Always backup your database before mass deletions
- Test on staging environment first
- Cannot undo deletions!

**Email Notifications**
- Disabled by default
- In Dry Run Mode: emails are simulated but not sent
- Check cleanup log to see which customers would receive emails
- Test email template before going live

## File Structure

```
FlipDev_CustomerCleanup/
├── src/                                   # Source code directory
│   ├── Api/
│   │   └── Data/
│   │       └── CleanupLogInterface.php
│   ├── Block/
│   │   └── Adminhtml/
│   │       └── StatusBanner.php           # Visual warning banner
│   ├── Controller/
│   │   └── Adminhtml/
│   │       ├── Customer/
│   │       │   ├── Index.php
│   │       │   ├── MassDelete.php
│   │       │   └── MassNotify.php
│   │       └── Log/
│   │           └── Index.php
│   ├── etc/
│   │   ├── acl.xml
│   │   ├── config.xml                     # Safe defaults
│   │   ├── di.xml
│   │   ├── email_templates.xml
│   │   ├── module.xml
│   │   └── adminhtml/
│   │       ├── di.xml                     # Admin-specific DI
│   │       ├── menu.xml
│   │       ├── routes.xml
│   │       └── system.xml
│   ├── Helper/
│   │   └── Config.php
│   ├── Logger/
│   │   ├── Handler.php
│   │   └── Logger.php
│   ├── Model/
│   │   ├── CleanupLog.php
│   │   ├── ResourceModel/
│   │   │   ├── CleanupLog.php
│   │   │   └── CleanupLog/
│   │   │       └── Collection.php
│   │   └── Source/
│   │       └── ActionType.php
│   ├── Service/
│   │   ├── CustomerCleanupService.php
│   │   ├── CustomerFilterService.php
│   │   └── NotificationService.php
│   ├── Setup/
│   │   ├── InstallSchema.php
│   │   └── Uninstall.php
│   ├── view/
│   │   ├── adminhtml/
│   │   │   ├── layout/
│   │   │   │   ├── customercleanup_customer_index.xml
│   │   │   │   └── customercleanup_log_index.xml
│   │   │   ├── templates/
│   │   │   │   └── status_banner.phtml
│   │   │   └── ui_component/
│   │   │       ├── customercleanup_customer_listing.xml
│   │   │       └── customercleanup_log_listing.xml
│   │   └── frontend/
│   │       └── email/
│   │           └── warning.html
│   └── registration.php
├── composer.json
├── LICENSE
└── README.md
```

## Database Tables

### flipdev_customer_cleanup_log
Stores all cleanup operations for audit purposes:
- log_id (primary key)
- customer_id
- customer_email
- customer_name
- action_type (deleted, anonymized, notification_sent)
- reason
- dry_run (flag)
- admin_user
- created_at

## Customization

### Custom Email Template
1. Navigate to: **Marketing > Email Templates**
2. Click "Add New Template"
3. Load template: "Customer Cleanup - Account Deletion Warning"
4. Customize as needed
5. Save and select in module configuration

### Extending Cleanup Criteria
You can extend `CustomerFilterService` to add custom cleanup criteria.

## Troubleshooting

### Common Issues

**"Email template is not defined" error:**
- Clear all caches: `bin/magento cache:flush`
- Recompile DI: `bin/magento setup:di:compile`

**"get_parent_class()" TypeError:**
- Clear generated code: `rm -rf generated/code/*`
- Recompile DI: `bin/magento setup:di:compile`
- Clear caches: `bin/magento cache:flush`

**Grid not loading:**
- Clear caches and recompile DI
- Check var/log files for specific errors

### Cache Commands

```bash
# Clear all caches
bin/magento cache:flush

# Recompile DI
bin/magento setup:di:compile

# Clear generated code (if needed)
rm -rf generated/code/*

# Full refresh
bin/magento setup:upgrade && bin/magento setup:di:compile && bin/magento cache:flush
```

## Support

For issues, improvements, or questions, please create an issue on GitHub.

## License

Proprietary - All rights reserved

## Changelog

### Version 1.0.0
- Initial release
- Multi-criteria customer cleanup
- Email notifications
- Triple-layer safety system
- Visual warning system with status banner
- Order anonymization for legal compliance
- Comprehensive logging
- Admin UI with grids and mass actions
- Clean uninstall process

## Credits

Developed by FlipDev for Magento 2.4.8
