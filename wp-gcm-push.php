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

spl_autoload_register(function($className) {
    $classArray = explode('\\', $className);
    if ($classArray[0] == 'GcmPush') {
        array_shift($classArray);
        $path = dirname(__FILE__) . '/Classes/' . implode('/', $classArray) .'.class.php';
        if (file_exists($path)) {
            require_once $path;
        }
    }
});
$gcmPush = new \GcmPush\Controllers\GcmPush();

// add actions
add_action('admin_menu', array($gcmPush, 'getAdminMenu'));
add_action('admin_init', array($gcmPush->getSettings(), 'registerSettings'));

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
          `id` int(11) NOT NULL AUTO_INCREMENT,
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
