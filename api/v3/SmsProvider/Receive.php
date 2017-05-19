<?php

/**
 * SmsProvider.Receive API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_sms_provider_Receive_spec(&$spec) {
  $spec['from_number']['api.required'] = 1;
  $spec['from_number']['title'] = 'From number, can be set to any number';
  $spec['from_number']['description'] = 'Set to a phone number in the database if you want to match to a contact';
  $spec['content']['api.required'] = 1;
  $spec['content']['title'] = 'Content of SMS message';
  $spec['id']['title'] = 'Id of message';
  $spec['id']['title'] = 'can be set to any numeric value or left empty';
}

/**
 * SmsProvider.Receive API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_sms_provider_Receive($params) {
  require_once('io_3sd_dummysms.php');
  $sms = io_3sd_dummysms::singleton();
  if (!isset($params['id'])) {
    $params['id'] = NULL;
  }
  $smsResult = $sms->inbound($params['from_number'], $params['content'], $params['id']);
  if ($smsResult->id) {
    $params['id'] = $smsResult->id;
    $activity = civicrm_api3('Activity', 'getsingle', $params);
    return civicrm_api3_create_success(array($activity), $params, 'SmsProvider', 'Receive');
  }

  return civicrm_api3_create_error('Inbound SMS processing failed');
}
