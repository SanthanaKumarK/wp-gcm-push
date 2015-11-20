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
    /**
     * Plugin Settings instance
     */
    protected $objSettings;

    /**
     * Default constructor
     */
    public function __construct()
    {
        $this->objSettings = new GcmPushSettings();
        if (is_admin()) {
            add_action('admin_menu', array($this, 'getAdminMenu'));
        }
        // add post page
        add_action('post_submitbox_misc_actions', array($this, 'getPostCheckBoxOption'));
        
        // Register controllers for json api
        add_filter('json_api_controllers', array($this, 'getJsonApiControllers'));
        add_filter('json_api_gcmpush_controller_path', array($this, 'getGcmPushControllerPath'));
        
        // Register hooks to send Gcm on update post
        add_action('publish_post', array($this, 'sendGcmPushNotification'), 10, 3);
    }

    /**
     * Get the post publish meta box
     */
    function getPostCheckBoxOption() {
        global $post;
        if ($this->canSendNotificationOnPostUpdate() && get_post_type($post) == 'post') {
            require_once WP_GCM_PUSH_PLUGIN_DIR .'/Views/SendNotificationCheckBox.php';
        }
    }

    /**
     * Check whether the plugin can able to send notification based on general the settings
     *
     * @return boolean TRUE|FALSE
     */
    public function canSendNotification()
    {
        $apiKey = $this->objSettings->options['api-key'];

        if (empty($apiKey)) {
            return false;
        }

        return true;
    }

    /**
     * Check whether the plugin can able to send notification for post update
     *
     * @return boolean TRUE|FALSE
     */
    public function canSendNotificationOnPostUpdate()
    {
        if (!$this->canSendNotification()) {
            return false;
        }
        $notificationOption = $this->objSettings->options['send-notification-post-update'];
        if (empty($notificationOption)) {
            return false;
        }

        return true;
    }

    /**
     * Hook to send push notification, while updating the post
     * 
     * @param integer $ID   Post id
     * @param string  $post Post instance
     * 
     * @return null
     */
    public function sendGcmPushNotification($ID, $post)
    {
        if (   !$this->canSendNotificationOnPostUpdate()
            || empty($_POST['gcm-push-send-notification'])
            || 'post' != get_post_type($post)
        ) {
            return;
        }

        $apiKey = $this->objSettings->options['api-key'];

        $postTitle  = get_the_title($post);
        $postUrl    = get_permalink($post);
        $postAuthor = get_the_author_meta('display_name', $post->post_author);
        $message    = array(
            'title'      => $postTitle,
            'message'    => 'Added by '. $postAuthor,
            'postId'     => $ID,
            'type'       => 'new_post',
            'subtitle'   => '',
            'tickerText' => '',
            'msgcnt'     => 1,
            'vibrate'    => 1,
            'contents'   => $postUrl
        );
        // Send notification
        try {
            $users        = $this->getAllUsers();
            $gcmMessenger = new GcmPushMessenger($apiKey);
            $gcmMessenger->setData($message);
            $gcmMessenger->addRegistrationId($users);
            $gcmMessenger->send();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
    
    /**
     * Hook to register the Gcm push json api controller
     * 
     * @param array $controllers Controllers array
     * 
     * @return array
     */
    function getJsonApiControllers($controllers)
    {
        $controllers[] = 'GcmPush';
        return $controllers;
    }
    
    /**
     * Return's the Gcm push json api controller class path
     * 
     * @return string
     */
    function getGcmPushControllerPath()
    {
        return dirname(__FILE__) . '/GcmPushJsonApi.class.php';
    }
    
    /**
     * Get administrator menu
     */
    public function getAdminMenu()
    {
        add_menu_page(
            __('GCM Push', 'gcm-push'),
            __('GCM Push', 'gcm-push'),
            'manage_options',
            'gcm-push',
            array($this, 'listUsers'),
            'dashicons-cloud'
        );
        add_submenu_page(
            'gcm-push',
            __('New Message', 'gcm-push'),
            __('New Message', 'gcm-push'),
            'manage_options',
            'gcm-push-new-message',
            array($this, 'sendMessage')
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
    
    /**
     * Return's all register devices id
     * 
     * @return array
     */
    public function getAllUsers()
    {
        global $wpdb;
        
        $tableName = $wpdb->prefix . 'gcm_push_users';
        $sql = "SELECT reg_id FROM $tableName";
        $res = $wpdb->get_results($sql);
        $users = array();
        if ($res != false) {
            foreach ($res as $row) {
                array_push($users, $row->reg_id);
            }
        }
        return $users;
    }
    
    /**
     * Parse User's overview section
     */
    public function listUsers()
    {
        $listTable = new GcmPushUserListTable();
        $listTable->prepare_items();
        
        require_once WP_GCM_PUSH_PLUGIN_DIR .'/Views/Users.php';
    }

    /**
     * Parse New Message page
     */
    public function sendMessage()
    {
        wp_register_script('chosen.js', WP_GCM_PUSH_PLUGIN_URL . 'Lib/chosen/jquery.chosen.js');
        wp_enqueue_script('chosen.js');
        wp_register_style('chosen.css', WP_GCM_PUSH_PLUGIN_URL . 'Lib/chosen/chosen.css');
        wp_enqueue_style('chosen.css');

        $apiKey = $this->objSettings->options['api-key'];
        if ($this->canSendNotification() && isset($_POST['send-notification'])) {
            try {
                $gcmMessenger = new GcmPushMessenger($apiKey);
                $gcmMessenger->setData(array('message' => $_POST['push-message']));
                $gcmMessenger->addRegistrationId($_POST['users']);
                $gcmMessenger->send();
            } catch (\Exception $e) {
                die($e->getMessage());
            }
        }
        $users = $this->getAllUsers();
        require_once WP_GCM_PUSH_PLUGIN_DIR .'/Views/NewMessage.php';
    }
}
