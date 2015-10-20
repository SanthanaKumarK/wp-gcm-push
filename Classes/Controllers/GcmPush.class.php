<?php

/**
 * 
 * Core Gcm push class
 *
 * @author       SS4U Development Team <info@softsolutions4u.com>
 * @version      1.0.0
 */
namespace GcmPush\Controllers;

/**
 * Core Gcm push class
 *
 * @author       SS4U Development Team <info@softsolutions4u.com>
 * @version      1.0.0
 */
class GcmPush
{
    protected $objSettings;

    public function __construct()
    {
        $this->objSettings = new GcmPushSettings();
    }
    
    public function getSettings()
    {
        return $this->objSettings;
    }

    public function getAdminMenu()
    {
        add_menu_page(
            'GCM Push',
            'GCM Push',
            'manage_options',
            'gcm-push-settings',
            array($this->objSettings, 'showSettings'),
            'dashicons-cloud'
        );
    }
}
