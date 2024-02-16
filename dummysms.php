<?php

require_once 'dummysms.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function dummysms_civicrm_config(&$config) {
  _dummysms_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function dummysms_civicrm_install() {
  $groupID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionGroup','sms_provider_name','id','name');
  $params  =
    array('option_group_id' => $groupID,
      'label' => 'DummySMS',
      'value' => 'io.3sd.dummysms',
      'name'  => 'dummysms',
      'is_default' => 1,
      'is_active'  => 1,
      'version'    => 3,);

  civicrm_api3( 'option_value','create', $params );

  _dummysms_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function dummysms_civicrm_uninstall() {
  $optionID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionValue','dummysms','id','name');
  if ($optionID)
    CRM_Core_BAO_OptionValue::del($optionID);

  $filter    =  array('name'  => 'io.3sd.dummysms');
  $Providers =  CRM_SMS_BAO_Provider::getProviders(False, $filter, False);
  if ($Providers){
    foreach($Providers as $key => $value){
      CRM_SMS_BAO_Provider::del($value['id']);
    }
  }

}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function dummysms_civicrm_enable() {
  $optionID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionValue','dummysms' ,'id','name');
  if ($optionID)
    CRM_Core_BAO_OptionValue::setIsActive($optionID, TRUE);

  $filter    =  array('name' => 'io.3sd.dummysms');
  $Providers =  CRM_SMS_BAO_Provider::getProviders(False, $filter, False);
  if ($Providers){
    foreach($Providers as $key => $value){
      CRM_SMS_BAO_Provider::setIsActive($value['id'], TRUE);
    }
  }

  _dummysms_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function dummysms_civicrm_disable() {
  $optionID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionValue','dummysms','id','name');
  if ($optionID)
    CRM_Core_BAO_OptionValue::setIsActive($optionID, FALSE);

  $filter    =  array('name' =>  'io.3sd.dummysms');
  $Providers =  CRM_SMS_BAO_Provider::getProviders(False, $filter, False);
  if ($Providers){
    foreach($Providers as $key => $value){
      CRM_SMS_BAO_Provider::setIsActive($value['id'], FALSE);
    }
  }

}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *

 // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function dummysms_civicrm_navigationMenu(&$menu) {
  _dummysms_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'io.3sd.dummysms')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _dummysms_civix_navigationMenu($menu);
} // */

// /**
//  * Implements hook_civicrm_entityTypes().
//  *
//  * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
//  */
// function dummysms_civicrm_entityTypes(&$entityTypes) {
//   _dummysms_civix_civicrm_entityTypes($entityTypes);
// }
