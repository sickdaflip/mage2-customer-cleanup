# ACL Permissions Documentation

## Overview

The Customer Cleanup module uses granular Access Control Lists (ACL) to ensure that only authorized admin users can perform cleanup operations.

## Permission Structure

```
Customer Cleanup (Sickdaflip_CustomerCleanup::customercleanup)
├── Manage Customer Cleanup (Sickdaflip_CustomerCleanup::cleanup)
│   ├── View Inactive Customers (::cleanup_view)
│   ├── Delete Customers (::cleanup_delete)
│   └── Send Notifications (::cleanup_notify)
├── View Cleanup Log (::cleanup_log)
└── Customer Cleanup Configuration (::config)
```

## Permission Levels Explained

### 1. View Inactive Customers (`cleanup_view`)
**Required for:**
- Accessing the "Inactive Customers" grid
- Viewing the list of customers eligible for cleanup
- Filtering and searching through inactive customers

**Does NOT allow:**
- Deleting customers
- Sending notification emails

**Use case:** Junior admins or support staff who need to review which customers are inactive but shouldn't perform deletions.

---

### 2. Delete Customers (`cleanup_delete`)
**Required for:**
- Using the "Delete" mass action
- Actually removing or anonymizing customer data
- Performing cleanup operations

**⚠️ HIGH-RISK PERMISSION**
- Should only be granted to senior staff
- Operations are logged with admin username
- Respects dry-run mode setting

**Use case:** Senior admins responsible for data cleanup and GDPR compliance.

---

### 3. Send Notifications (`cleanup_notify`)
**Required for:**
- Using the "Send Warning Email" mass action
- Notifying customers about upcoming account deletion
- Managing customer communication

**Use case:** Customer service managers who handle customer communications.

---

### 4. View Cleanup Log (`cleanup_log`)
**Required for:**
- Accessing the "Cleanup Log" page
- Viewing audit trail of all cleanup operations
- Reviewing who deleted what and when

**Use case:** Compliance officers, auditors, senior management.

---

### 5. Customer Cleanup Configuration (`config`)
**Required for:**
- Accessing Stores > Configuration > Customer Cleanup
- Changing cleanup criteria (days, years, etc.)
- Enabling/disabling module and dry-run mode
- Configuring email templates

**⚠️ HIGH-RISK PERMISSION**
- Can enable live mode (disable dry-run)
- Changes affect all cleanup operations

**Use case:** System administrators and technical leads only.

---

## Setting Up Permissions

### Via Admin Panel

1. Navigate to: **System > Permissions > User Roles**
2. Select or create a role
3. Go to "Role Resources" tab
4. Find "Customer Cleanup" in the resource tree
5. Check the specific permissions you want to grant

### Recommended Permission Sets

#### 🟢 **Support Staff (View Only)**
```
✅ View Inactive Customers
✅ View Cleanup Log
❌ Delete Customers
❌ Send Notifications
❌ Configuration
```
*Can review but not take action*

#### 🟡 **Customer Service Manager**
```
✅ View Inactive Customers
✅ Send Notifications
✅ View Cleanup Log
❌ Delete Customers
❌ Configuration
```
*Can communicate with customers but not delete*

#### 🟠 **Data Protection Officer**
```
✅ View Inactive Customers
✅ Delete Customers
✅ Send Notifications
✅ View Cleanup Log
❌ Configuration (request from admin)
```
*Can perform cleanup but not change rules*

#### 🔴 **System Administrator (Full Access)**
```
✅ View Inactive Customers
✅ Delete Customers
✅ Send Notifications
✅ View Cleanup Log
✅ Configuration
```
*Complete control over module*

---

## Best Practices

### 1. Principle of Least Privilege
Grant users only the minimum permissions they need to do their job.

### 2. Separation of Duties
- Configuration changes should require different permission than execution
- Consider having different admins for:
  - Configuration (Technical Lead)
  - Execution (Data Protection Officer)
  - Audit (Compliance Officer)

### 3. Audit Trail
All operations are logged with:
- Admin username
- Customer affected
- Action taken
- Timestamp
- Dry-run flag

Review logs regularly to ensure proper usage.

### 4. Testing Permissions
Create test admin accounts with different permission levels to verify:
- Users can only access what they should
- Unauthorized actions are properly blocked
- Error messages are clear

---

## Technical Implementation

### In Controllers

Each controller checks permissions via the `ADMIN_RESOURCE` constant:

```php
// Customer/Index.php
const ADMIN_RESOURCE = 'Sickdaflip_CustomerCleanup::cleanup_view';

// Customer/MassDelete.php  
const ADMIN_RESOURCE = 'Sickdaflip_CustomerCleanup::cleanup_delete';

// Customer/MassNotify.php
const ADMIN_RESOURCE = 'Sickdaflip_CustomerCleanup::cleanup_notify';

// Log/Index.php
const ADMIN_RESOURCE = 'Sickdaflip_CustomerCleanup::cleanup_log';
```

### In Menu Items

Menu items are hidden if user lacks permission:

```xml
<add id="Sickdaflip_CustomerCleanup::inactive_customers"
     resource="Sickdaflip_CustomerCleanup::cleanup_view"/>
```

### In UI Components

Grid data sources respect ACL:

```xml
<aclResource>Sickdaflip_CustomerCleanup::cleanup_view</aclResource>
```

---

## Troubleshooting

### "Access Denied" Error
**Problem:** Admin user sees "Access Denied" when trying to access Customer Cleanup

**Solution:**
1. Verify user's role has the required permission
2. Check System > Permissions > User Roles
3. Ensure role has "Customer Cleanup" resources checked
4. Log out and log back in after permission changes

### Menu Items Not Visible
**Problem:** Customer Cleanup menu doesn't appear

**Solution:**
1. Check if role has at least `Sickdaflip_CustomerCleanup::customercleanup` permission
2. Clear cache: `php bin/magento cache:flush`
3. Verify ACL is properly installed: `php bin/magento setup:upgrade`

### Mass Actions Not Working
**Problem:** Delete/Notify buttons don't appear or don't work

**Solution:**
1. Delete button requires: `cleanup_delete` permission
2. Notify button requires: `cleanup_notify` permission
3. Check browser console for JavaScript errors
4. Verify UI component ACL resources are correct

---

## Security Recommendations

1. **Limit Access**: Only grant Customer Cleanup permissions to trusted staff
2. **Monitor Logs**: Regularly review cleanup logs for suspicious activity
3. **Use Dry Run**: Keep dry-run mode enabled except during actual cleanup
4. **Rotate Permissions**: Review and update role permissions quarterly
5. **Document Changes**: Log any permission changes for audit purposes

---

## Compliance Notes

For GDPR/DSGVO compliance:
- Maintain audit trail of all deletions (automatic via cleanup log)
- Ensure proper authorization before granting delete permissions
- Document who has access to customer data cleanup
- Review permissions during compliance audits
