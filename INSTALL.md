# Installation Guide - Sickdaflip CustomerCleanup

## System Requirements

### Minimum Requirements
- **PHP**: 8.4 or higher
- **Magento**: 2.4.8 or compatible
- **MySQL**: 8.0 or higher
- **Composer**: 2.x

### PHP Extensions Required
All standard Magento 2.4.8 PHP extensions

---

## Installation Methods

### Method 1: Manual Installation (Recommended for Development)

#### Step 1: Extract Module
```bash
# Navigate to Magento root
cd /path/to/magento

# Create module directory
mkdir -p app/code/Sickdaflip/CustomerCleanup

# Extract/copy module files
unzip Sickdaflip_CustomerCleanup.zip
cp -r Sickdaflip_CustomerCleanup/* app/code/Sickdaflip/CustomerCleanup/
```

#### Step 2: Enable Module
```bash
# Enable the module
php bin/magento module:enable Sickdaflip_CustomerCleanup

# Run setup upgrade
php bin/magento setup:upgrade

# Deploy static content (production mode)
php bin/magento setup:static-content:deploy -f

# Compile dependency injection
php bin/magento setup:di:compile

# Clear cache
php bin/magento cache:flush
```

#### Step 3: Verify Installation
```bash
# Check if module is enabled
php bin/magento module:status Sickdaflip_CustomerCleanup

# Should show: Module is enabled
```

---

### Method 2: Composer Installation (Recommended for Production)

#### Step 1: Add Repository (If not on Packagist)

Create or edit `composer.json` in Magento root:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "packages/sickdaflip/module-customercleanup",
            "options": {
                "symlink": false
            }
        }
    ]
}
```

Or for VCS (GitHub, GitLab, etc.):

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/your-org/module-customercleanup"
        }
    ]
}
```

#### Step 2: Require Module
```bash
# Require the module
composer require sickdaflip/module-customercleanup:^1.0

# Enable module
php bin/magento module:enable Sickdaflip_CustomerCleanup

# Run setup
php bin/magento setup:upgrade

# Deploy static content
php bin/magento setup:static-content:deploy -f

# Compile DI
php bin/magento setup:di:compile

# Clear cache
php bin/magento cache:flush
```

---

## Post-Installation Configuration

### Step 1: Verify Database Tables

Check that the cleanup log table was created:

```sql
-- Connect to your Magento database
USE magento_database;

-- Verify table exists
SHOW TABLES LIKE 'sickdaflip_customer_cleanup_log';

-- Check table structure
DESCRIBE sickdaflip_customer_cleanup_log;
```

Expected columns:
- log_id
- customer_id
- customer_email
- customer_name
- action_type
- reason
- dry_run
- admin_user
- created_at

### Step 2: Set Up Admin Permissions

1. Navigate to: **System → Permissions → User Roles**
2. Select or create appropriate roles
3. Go to **Role Resources** tab
4. Find **Customer Cleanup** in resource tree
5. Grant appropriate permissions based on role

See [ACL_PERMISSIONS.md](ACL_PERMISSIONS.md) for detailed permission structure.

### Step 3: Initial Configuration

1. Navigate to: **Stores → Configuration → Sickdaflip → Customer Cleanup**

2. **IMPORTANT**: Keep these defaults initially:
   - ✅ Enable Module: **NO** (disabled)
   - ✅ Dry Run Mode: **YES** (enabled)
   - ✅ Enable Email Notifications: **NO** (disabled)

3. Configure cleanup criteria according to your needs:
   - Days Since Last Login
   - Days Since Account Creation (No Orders)
   - Years Since Last Order (minimum 10 recommended)

4. Save configuration

### Step 4: Test in Dry Run Mode

1. Enable the module (set Enable Module = YES)
2. Keep Dry Run Mode = YES
3. Navigate to: **Customer Cleanup → Inactive Customers**
4. Select a few test customers
5. Try the mass actions (they will be simulated only)
6. Check: **Customer Cleanup → Cleanup Log**
7. Verify log entries show `dry_run = 1`

---

## Verification Checklist

After installation, verify:

- [ ] Module appears in `php bin/magento module:status`
- [ ] Database table `sickdaflip_customer_cleanup_log` exists
- [ ] Menu item "Customer Cleanup" appears in admin
- [ ] Configuration section available in Stores → Configuration
- [ ] Module is DISABLED by default
- [ ] Dry Run Mode is ENABLED by default
- [ ] Email notifications are DISABLED by default
- [ ] Admin permissions are properly set
- [ ] Log file created: `var/log/customer_cleanup.log`

---

## Troubleshooting

### Module Not Showing in Admin

**Problem**: Customer Cleanup menu doesn't appear

**Solutions**:
```bash
# Clear all caches
php bin/magento cache:flush

# Reindex if needed
php bin/magento indexer:reindex

# Verify module is enabled
php bin/magento module:status

# Check ACL permissions
# System → Permissions → User Roles → Your Role → Role Resources
```

### Database Table Not Created

**Problem**: `sickdaflip_customer_cleanup_log` table missing

**Solutions**:
```bash
# Run setup upgrade again
php bin/magento setup:upgrade

# Check for errors in logs
tail -f var/log/system.log

# Manually check schema
php bin/magento setup:db:status
```

### Permission Denied Errors

**Problem**: 403 or access denied when accessing module

**Solutions**:
1. Grant proper ACL permissions to your admin role
2. Log out and log back in
3. Clear cache: `php bin/magento cache:flush`

### Composer Installation Fails

**Problem**: Composer can't find package

**Solutions**:
```bash
# Validate composer.json
composer validate

# Update composer
composer self-update

# Clear composer cache
composer clear-cache

# Try with verbose output
composer require sickdaflip/module-customercleanup -vvv
```

---

## Upgrading

### From Previous Version

```bash
# Backup database first!
php bin/magento maintenance:enable

# Pull/install new version
composer update sickdaflip/module-customercleanup

# Run upgrade
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
php bin/magento cache:flush

php bin/magento maintenance:disable
```

---

## Uninstallation

### Complete Removal

```bash
# Enable maintenance mode
php bin/magento maintenance:enable

# Uninstall module (preserves config and data)
php bin/magento module:uninstall Sickdaflip_CustomerCleanup

# OR manually disable and remove
php bin/magento module:disable Sickdaflip_CustomerCleanup
php bin/magento setup:upgrade

# Remove via composer (if installed via composer)
composer remove sickdaflip/module-customercleanup

# Remove files (if manually installed)
rm -rf app/code/Sickdaflip/CustomerCleanup

# Disable maintenance
php bin/magento maintenance:disable
```

### Data Cleanup

The module's `Uninstall.php` automatically:
- Drops the `sickdaflip_customer_cleanup_log` table
- Removes all configuration values

If you want to keep the log for audit purposes, backup before uninstalling:

```sql
CREATE TABLE sickdaflip_customer_cleanup_log_backup 
AS SELECT * FROM sickdaflip_customer_cleanup_log;
```

---

## Production Deployment Checklist

Before deploying to production:

- [ ] Tested thoroughly on staging environment
- [ ] Database backup created and verified
- [ ] Dry run mode tested with real data
- [ ] Email templates reviewed and tested
- [ ] Admin permissions properly configured
- [ ] Legal requirements verified (10-year retention, etc.)
- [ ] Stakeholders informed about deployment
- [ ] Rollback plan prepared
- [ ] Monitoring/alerts set up for cleanup operations

---

## Support

For installation issues:
1. Check logs: `var/log/system.log`, `var/log/exception.log`
2. Review this guide and [README.md](README.md)
3. Consult [SAFETY_GUIDE.md](SAFETY_GUIDE.md)
4. Check [ACL_PERMISSIONS.md](ACL_PERMISSIONS.md)

---

## Security Notes

⚠️ **Important**:
- Module is disabled by default (safe)
- Dry run mode is enabled by default (safe)
- Always test on staging first
- Backup database before enabling live mode
- Limit "Delete" permissions to senior staff only
- Monitor cleanup logs regularly
