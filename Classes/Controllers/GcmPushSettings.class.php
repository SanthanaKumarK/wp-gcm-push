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
    public $options;

    /**
     * Default constructor
     */
    public function __construct()
    {
        if (is_admin()) {
            add_action('admin_init', array($this, 'register'));
        }
        $this->options = get_option('gcm-push-setting');
    }

    /**
     * Register the settings values
     */
    public function register()
    {
        add_settings_section('gcm-push-setting-section', '', '', 'gcm-push');
        add_settings_field('api-key', __('Google cloud messaging Api key', 'gcm-push'), array($this, 'apiKeyCallback'), 'gcm-push', 'gcm-push-setting-section');
        add_settings_field('send-notification-post-update', __('Send notification on post update', 'gcm-push'), array($this, 'sendNotificationPostOptionCallback'), 'gcm-push', 'gcm-push-setting-section');
        register_setting('gcm-push-setting-group', 'gcm-push-setting', '');
    }

    /**
     * Setting notification on post update callback
     */
    public function sendNotificationPostOptionCallback()
    {
        printf(
            '<input type="checkbox" %s name="gcm-push-setting[send-notification-post-update]" value="1" />',
            !empty($this->options['send-notification-post-update']) ? 'checked' : ''
        );
    }

    /**
     * Setting api key callback
     */
    public function apiKeyCallback()
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
        require_once WP_GCM_PUSH_PLUGIN_DIR .'/Views/Settings.php';
    }
}
