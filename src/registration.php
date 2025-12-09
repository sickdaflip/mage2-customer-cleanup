<?php
/**
 * FlipDev CustomerCleanup Module Registration
 *
 * Handles customer data cleanup and GDPR compliance
 */

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'FlipDev_CustomerCleanup',
    __DIR__
);
