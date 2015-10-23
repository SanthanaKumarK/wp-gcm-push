<?php

/**
 * Core Gcm push messenger class
 *
 * @author  SS4U Development Team <info@softsolutions4u.com>
 * @version 1.0.0
 */

namespace GcmPush\Controllers;

/**
 * Core Gcm push messenger class
 *
 * @author  SS4U Development Team <info@softsolutions4u.com>
 * @version 1.0.0
 */
class GcmPushMessenger
{
    protected $gcmUrl = 'https://android.googleapis.com/gcm/send';
    protected $apiKey;
    protected $registrationIds = array();
    protected $data;

    /**
     * Google cloud messaging api key
     * 
     * @param string $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Add registration id to send notification
     * 
     * @param mixed $id Single Registration id or multiple registration id's as array
     * 
     * @return type
     */
    public function addRegistrationId($id)
    {
        if (empty($id)) {
            return;
        }
        if (is_string($id)) {
            $this->registrationIds[] = $id;
        } elseif (is_array($id)) {
            $this->registrationIds = array_merge($this->registrationIds, $id);
        }
        $this->registrationIds = array_values(array_unique($this->registrationIds));
    }

    /**
     * Set the data to be send in notification
     * 
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Dispatch the messages
     * 
     * @throws \Exception
     */
    public function send()
    {
        try {
            $fields = array('data' => $this->data);
            // Gcm allows only 1000 ids per request
            $sendIdsChunk = array_chunk($this->registrationIds, 1000);
            foreach ($sendIdsChunk as $ids) {
                $fields['registration_ids'] = $ids;
                $this->send($fields);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Init the curl fucntion to send the notification
     * 
     * @param array $fields Fields to be send in the message
     * 
     * @return string Result from the Google cloud messaging
     * @throws Exception
     */
    protected function sendCurl($fields)
    {
        if (empty($this->apiKey)) {
            throw new Exception('Api key is empty');
        }

        if (empty($fields)) {
            throw new Exception('Fields are empty');
        }

        $headers = array(
            'Authorization: key=' . $this->apiKey,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->gcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);

        return $result;
    }
}
