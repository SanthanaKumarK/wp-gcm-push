<?php

/**
 * Core Gcm push settings class
 *
 * @author  SS4U Development Team <info@softsolutions4u.com>
 * @version 1.0.0
 */

namespace GcmPush\Controllers;

/**
 * Core Gcm push settings class
 *
 * @author  SS4U Development Team <info@softsolutions4u.com>
 * @version 1.0.0
 */
class GcmPushSettings
{
    protected $options;

    /**
     * Default constructor
     */
    public function __construct()
    {
        if (is_admin()) {
            add_action('admin_init', array($this, 'register'));
        }
    }

    /**
     * Register the settings values
     */
    public function register()
    {
        add_settings_section('gcm-push-setting-section', '', '', 'gcm-push');
        add_settings_field('api-key', __('Api Key', 'gcm-push'), array($this, 'apiKeyCallback'), 'gcm-push', 'gcm-push-setting-section');
        register_setting('gcm-push-setting-group', 'gcm-push-setting', '');
    }

    /**
     * Setting api key callback
     */
    function apiKeyCallback()
    {
        printf(
            '<input type="text" name="gcm-push-setting[api-key]" value="%s" />',
            isset( $this->options['api-key'] ) ? esc_attr( $this->options['api-key']) : ''
        );
    }

    /**
     * Render the settings page
     */
    public function showSettings()
    {
        $this->options = get_option('gcm-push-setting');
        require_once WP_GCM_PUSH_PLUGIN_DIR .'/Views/Settings.php';
    }
}
