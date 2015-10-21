<?php

/**
 * Core Gcm push class
 *
 * @author  SS4U Development Team <info@softsolutions4u.com>
 * @version 1.0.0
 */

namespace GcmPush\Controllers;

/**
 * Core Gcm push class
 *
 * @author  SS4U Development Team <info@softsolutions4u.com>
 * @version 1.0.0
 */
class GcmPush
{
    protected $objSettings;

    public function __construct()
    {
        $this->objSettings = new GcmPushSettings();
        if (is_admin()) {
            add_action('admin_menu', array($this, 'getMenu'));
        }
    }

    /**
     * Get administrator menu
     */
    public function getMenu()
    {
        add_menu_page(
            'GCM Push',
            'GCM Push',
            'manage_options',
            'gcm-push',
            array($this, 'listUsers'),
            'dashicons-cloud'
        );
        add_submenu_page(
            'gcm-push',
            __('Settings', 'gcm-push'),
            __('Settings', 'gcm-push'),
            'manage_options',
            'gcm-push-settings',
            array($this->objSettings, 'showSettings')
        );
    }
    
    public function listUsers()
    {
        $listTable = new GcmPushUserListTable();
        $listTable->prepare_items();
        
        require_once WP_GCM_PUSH_PLUGIN_DIR .'/Views/Users.php';
    }

}
