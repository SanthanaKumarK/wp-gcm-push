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
        $tableName  = $wpdb->prefix . 'gcm_push_users';
        
        $sql    = "SELECT `reg_id` FROM `$tableName` WHERE `reg_id`='$registerId'";
        $result = $wpdb->get_results($sql);

        if (!$result) {
            $sql = "INSERT INTO $tableName (reg_id) VALUES ('$registerId')";
            $q = $wpdb->query($sql);
            $status = 'You are registered successfully';
         } else {
            $status = 'You are already registered';
         }

        return array(
            'message' => $status,
        );
    }
}
