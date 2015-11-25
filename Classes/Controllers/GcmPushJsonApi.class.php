<?php

/*
 Controller name: GcmPush
 Controller description: Api to send push notification using Google cloud messaging
 */

/**
 * Core Gcm push json api class
 *
 * @author  SS4U Development Team <info@softsolutions4u.com>
 * @version 1.0.0
 */
class JSON_API_GcmPush_Controller
{
    /**
     * Table name
     *
     * @var string
     */
    public $tableName;

    /**
     * Construct
     */
    public function __construct() {
        global $wpdb;
        $this->tableName = $wpdb->prefix . 'gcm_push_users';
    }

    /**
     * Register the device to the send the push notification
     * 
     * @return array
     */
    public function register()
    {
        global $json_api, $wpdb;
        
        if (!$json_api->query->id) {
            $json_api->error("You must include 'id' variable in your request. ");
        }
        $registerId = sanitize_text_field($json_api->query->id);
        $os         = '';
        
        $sql    = "SELECT `reg_id` FROM `$this->tableName` WHERE `reg_id`='$registerId'";
        $result = $wpdb->get_results($sql);

        if (!$result) {
            $sql = "INSERT INTO $this->tableName (reg_id) VALUES ('$registerId')";
            $q = $wpdb->query($sql);
            $status = 'You are registered successfully';
         } else {
            $status = 'You are already registered';
         }

        return array(
            'message' => $status,
        );
    }

    /**
     * Change the device status
     *
     * @return array
     */
    public function changeStatus() {
        global $json_api, $wpdb;
        if (!$json_api->query->id) {
            $json_api->error("unable to update the status");
        }

        $registerId = sanitize_text_field($json_api->query->id);
        $status = $json_api->query->status == 'true' ? '1' : '0';

        $updateSql = "UPDATE $this->tableName SET `status` = '$status' WHERE `reg_id`='$registerId'";
        $wpdb->query($updateSql);
        $notificationStatus = $this->getStatus();
        return array(
            'notificationStatus' => $notificationStatus,
        );
    }

    /**
     *
     * Get the device status by gcm token id.
     *
     * @return boolean true | false
     */
    public function getStatus() {
        global $json_api, $wpdb;
        if (!$json_api->query->id) {
            $json_api->error("unable to update the status");
        }

        $registerId = sanitize_text_field($json_api->query->id);
        $sql = "SELECT `status` FROM `$this->tableName` WHERE `reg_id`='$registerId'";
        $result = $wpdb->get_results($sql);
        return ($result[0]->status) ? true : false;
    }
}
