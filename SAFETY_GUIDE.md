# 🔒 Sickdaflip_CustomerCleanup - Safety Guide

## 🛡️ Triple-Layer Safety System

This module implements a **triple-layer safety system** to prevent accidental data loss:

```
┌─────────────────────────────────────────────────────────────┐
│                    LAYER 1: MODULE DISABLED                  │
│  ❌ Module is OFF by default after installation             │
│  ❌ No operations possible until explicitly enabled          │
├─────────────────────────────────────────────────────────────┤
│                    LAYER 2: DRY RUN MODE                     │
│  🧪 Enabled by default                                       │
│  🧪 All operations are simulated only                        │
│  🧪 Full logging without actual changes                      │
├─────────────────────────────────────────────────────────────┤
│                    LAYER 3: VISUAL WARNINGS                  │
│  👁️ Status banner on every admin page                       │
│  👁️ Color-coded mode indicators                             │
│  👁️ Confirmation messages before actions                    │
└─────────────────────────────────────────────────────────────┘
```

## 📊 Status Banner Examples

### When Module is Disabled (Default after installation)
```
┌───────────────────────────────────────────────────────────┐
│ 🔒 Module is DISABLED - No cleanup operations will be     │
│    performed                                               │
│                                                            │
│ To start using Customer Cleanup, enable the module in     │
│ configuration.                                             │
│                                                            │
│ [ ⚙️ Configure Module Settings ]                          │
└───────────────────────────────────────────────────────────┘
```
**Color**: Gray - Nothing can happen ✅ SAFE

### When in Dry Run Mode (Recommended for testing)
```
┌───────────────────────────────────────────────────────────┐
│ 🧪 DRY RUN MODE - All operations are simulated only,      │
│    NO actual deletions or emails                          │
│                                                            │
│ Safe for testing! Review logs after operations to see     │
│ what would happen.                                         │
│ ⚠️ Email notifications are also DISABLED                  │
│                                                            │
│ [ ⚙️ Configure Module Settings ]                          │
└───────────────────────────────────────────────────────────┘
```
**Color**: Yellow - Test mode, only logging ✅ SAFE FOR TESTING

### When in LIVE Mode (Production)
```
┌───────────────────────────────────────────────────────────┐
│ ⚠️ LIVE MODE - All operations will be executed for real!  │
│                                                            │
│ ⚠️ CAUTION: Operations will permanently delete or modify  │
│ customer data!                                             │
│ Make sure you have a recent backup before proceeding!     │
│ 📧 Email notifications are ENABLED - Customers will       │
│ receive emails                                             │
│                                                            │
│ [ ⚙️ Configure Module Settings ]                          │
└───────────────────────────────────────────────────────────┘
```
**Color**: Red (pulsing) - Real operations! ⚠️ CAUTION

## 📋 Safe Testing Workflow

### Phase 1: Initial Setup (100% Safe)
```bash
# After installation, module status:
✅ Module Enabled: NO
✅ Dry Run Mode: YES
✅ Email Notifications: NO
→ Result: Nothing can happen, fully safe
```

**What you should do:**
1. Go to Stores > Configuration > Sickdaflip > Customer Cleanup
2. Configure cleanup criteria (days, years, etc.)
3. Save configuration
4. **DO NOT enable module yet**

### Phase 2: Dry Run Testing (Safe, only logging)
```bash
# Change settings to:
✅ Module Enabled: YES
✅ Dry Run Mode: YES (keep this!)
✅ Email Notifications: NO
→ Result: Operations logged but not executed
```

**What you should do:**
1. Navigate to Customer Cleanup > Inactive Customers
2. You'll see the yellow "DRY RUN MODE" banner
3. Select some customers
4. Click "Delete" or "Send Warning Email"
5. Check Customer Cleanup > Cleanup Log
6. Review what WOULD have happened
7. Repeat until satisfied

### Phase 3: Email Testing (Optional, still safe)
```bash
# Change settings to:
✅ Module Enabled: YES
✅ Dry Run Mode: YES (still safe!)
✅ Email Notifications: YES
→ Result: Email sending logged but not sent
```

**What you should do:**
1. Test "Send Warning Email" mass action
2. Check logs - you'll see notification entries
3. Verify email template looks good in config
4. NO actual emails are sent (Dry Run protects you!)

### Phase 4: Production Use (⚠️ CAREFUL!)
```bash
# Only after extensive testing:
✅ Module Enabled: YES
⚠️ Dry Run Mode: NO (DANGER!)
✅ Email Notifications: YES/NO (your choice)
→ Result: Real deletions and emails!
```

**BEFORE you do this:**
- [ ] Full database backup created
- [ ] Tested on staging environment
- [ ] Reviewed all cleanup logs from dry runs
- [ ] Verified cleanup criteria are correct
- [ ] Informed team about planned cleanup
- [ ] Started with small test batch

**What you should do:**
1. You'll see the red pulsing "LIVE MODE" banner
2. Start with a SMALL batch (5-10 customers)
3. Verify results immediately
4. Check cleanup log
5. If all good, proceed with larger batches

## 🚨 Emergency Stop

If something goes wrong or you want to stop immediately:

1. **Go to:** Stores > Configuration > Sickdaflip > Customer Cleanup
2. **Set:** Enable Module = NO
3. **Save Config**
4. **Clear Cache:** `php bin/magento cache:flush`

→ Module is now completely disabled and safe

## ✅ Safety Checklist Before Going Live

- [ ] Tested on staging environment
- [ ] Reviewed dry run logs thoroughly
- [ ] Database backup created (dated today)
- [ ] Cleanup criteria are correct (days/years)
- [ ] Email template reviewed and tested
- [ ] Team is informed
- [ ] Legal requirements checked (10-year retention for invoices)
- [ ] "Anonymize Orders" is enabled (if required by law)
- [ ] Small test batch planned for first live run

## 🔧 Configuration Hints in Admin

The configuration page shows helpful hints:

```
┌─────────────────────────────────────────────────────────────┐
│ ⚠️ SAFETY NOTICE                                            │
│                                                              │
│ • Start with Module DISABLED and Dry Run ENABLED            │
│ • Test thoroughly on staging environment first              │
│ • Always backup your database before enabling live mode     │
│ • Review cleanup logs after dry run operations              │
└─────────────────────────────────────────────────────────────┘
```

### Enable Module Field
```
⚠️ WARNING: When enabled, cleanup operations can be performed.
Keep DISABLED until you've tested thoroughly!
```

### Dry Run Mode Field
```
🔒 RECOMMENDED: When enabled, no actual deletions or emails occur
- only logging. Keep this ENABLED for testing!
```

## 📞 Support

If you're unsure about any safety aspect:
1. Keep the module disabled
2. Keep dry run mode enabled
3. Review the logs
4. Test on staging first
5. Start with small batches

**Remember**: It's better to be overcautious than to lose customer data!
