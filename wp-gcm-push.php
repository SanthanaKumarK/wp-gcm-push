<?php
/**
 * Plugin Name: Google Cloud messaging
 * Plugin URI: http://softsolutions4u.com
 * Description: This plugin allows send push notification using Google cloud messaging
 * Version: 1.0.0
 * Author: Ss4u
 * Author URI: http://softsolutions4u.com
 * License: GPL2
 */

define('WP_GCM_PUSH_PLUGIN_DIR', dirname(__FILE__));

include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if (!is_plugin_active('json-api/json-api.php')) {
    add_action(
        'admin_notices',
        function() {
            require_once WP_GCM_PUSH_PLUGIN_DIR .'/Views/JsonApiError.php';
        }
    );
    return;
}

spl_autoload_register(function($className) {
    $classArray = explode('\\', $className);
    if ($classArray[0] == 'GcmPush') {
        array_shift($classArray);
        $path = WP_GCM_PUSH_PLUGIN_DIR . '/Classes/' . implode('/', $classArray) .'.class.php';
        if (file_exists($path)) {
            require_once $path;
        }
    }
});
$gcmPush = new \GcmPush\Controllers\GcmPush();

/**
 * Activate the plugin, Create table if not exists
 */
function gcm_push_activation() 
{
    global $wpdb;
    
    $tableName = $wpdb->prefix . 'gcm_push_users';
    
    $charsetCollate = $wpdb->get_charset_collate();
    if ($wpdb->get_var("show tables like '$tableName'") != $tableName) {
        $sql = "CREATE TABLE `$tableName` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `reg_id` text,
          `os` varchar(55) DEFAULT '' NOT NULL,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) $charsetCollate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
register_activation_hook(__FILE__, 'gcm_push_activation');
