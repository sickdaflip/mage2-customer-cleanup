# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-11-27

### Added
- Initial release of Customer Cleanup module
- Multi-criteria customer cleanup system
  - Customers without orders after configurable days
  - Customers inactive (no login) for configurable days
  - Customers with last order older than configurable years (min 10)
  - Option to include customers who never logged in
- Triple-layer safety system
  - Module disabled by default after installation
  - Dry run mode enabled by default
  - Email notifications disabled by default
- Visual warning system with status banner
  - Color-coded mode indicators (Gray/Yellow/Red)
  - Prominent warnings on all admin pages
  - Direct link to configuration
- Email notification system
  - Configurable warning period before deletion
  - Customizable email templates
  - Respects dry-run mode (simulated only)
- Granular ACL (Access Control Lists)
  - Separate permissions: view, delete, notify, log, config
  - Role-based access control
  - All operations logged with admin username
- Data protection features
  - Order anonymization instead of deletion (GDPR/DSGVO compliance)
  - Comprehensive audit logging
  - Database table for cleanup history
- Admin interface
  - Grid view of inactive customers with filters
  - Mass actions (delete, send notifications)
  - Cleanup log with full audit trail
  - Configuration section with inline help
- Complete documentation
  - README.md with feature overview
  - INSTALL.md with step-by-step installation
  - SAFETY_GUIDE.md with testing workflow
  - ACL_PERMISSIONS.md with permission structure
  - ACL_Quick_Reference.txt for quick lookup
- Clean uninstall process
  - Removes all database tables
  - Removes all configuration values
  - Complete cleanup on removal

### Technical Details
- PHP 8.4+ required
- Magento 2.4.8 compatible
- PSR-4 autoloading with src/ directory structure
- Professional module organization
- Comprehensive logging system (var/log/customer_cleanup.log)
- UI Components for admin grids
- Magento coding standards compliant

### Security
- Granular permissions prevent unauthorized access
- All operations logged with admin user identification
- Dry-run mode prevents accidental data loss
- Visual warnings before dangerous operations
- Module disabled by default

### Compliance
- GDPR "Right to be forgotten" compliance
- DSGVO compliant with order anonymization
- 10-year retention period configurable
- Audit trail for compliance requirements
- Data protection by default

---

## [Unreleased]

### Planned Features
- Scheduled/automatic cleanup via cron
- Customer notification preview before sending
- Export cleanup reports to CSV
- Dashboard widget with cleanup statistics
- Multi-website support improvements
- Customer group specific cleanup rules
- API endpoints for programmatic access
- Backup/restore functionality for deleted customers

---

## Version History

- **1.0.0** - Initial release (2024-11-27)
  - Complete customer cleanup functionality
  - Safety features and documentation
  - Production-ready with enterprise-grade security
