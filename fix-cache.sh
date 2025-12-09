#!/bin/bash
# Complete cache cleanup script for FlipDev CustomerCleanup migration

echo "=== FlipDev CustomerCleanup Cache Fix Script ==="
echo ""

# Check if we're in Magento root
if [ ! -f "bin/magento" ]; then
    echo "ERROR: This script must be run from Magento root directory!"
    exit 1
fi

echo "Step 1: Removing ALL cache and generated files..."
rm -rfv var/cache/* var/page_cache/* var/view_preprocessed/* var/generation/* var/di/* generated/code/* generated/metadata/*

echo ""
echo "Step 2: Disabling maintenance mode (if enabled)..."
bin/magento maintenance:disable

echo ""
echo "Step 3: Recompiling dependency injection..."
bin/magento setup:di:compile

echo ""
echo "Step 4: Flushing all caches..."
bin/magento cache:flush

echo ""
echo "Step 5: Cleaning all caches..."
bin/magento cache:clean

echo ""
echo "Step 6: Checking module status..."
bin/magento module:status FlipDev_CustomerCleanup
bin/magento module:status FlipDev_Core

echo ""
echo "Step 7: Deploying static content..."
bin/magento setup:static-content:deploy -f de_DE en_US

echo ""
echo "=== DONE! ==="
echo ""
echo "IMPORTANT: Clear your browser cache or use Incognito mode!"
echo "- Chrome/Edge: CTRL+SHIFT+DELETE"
echo "- Firefox: CTRL+SHIFT+DELETE"
echo ""
echo "Then navigate to: Stores → Configuration → FlipDev → Customer Cleanup"
echo ""
