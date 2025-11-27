<?php
/**
 * Sickdaflip CustomerCleanup Module Registration
 * 
 * Handles customer data cleanup and GDPR compliance
 */

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Sickdaflip_CustomerCleanup',
    __DIR__
);
