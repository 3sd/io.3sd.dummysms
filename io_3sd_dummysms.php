<?php


/**
 * Class CRM_SMS_Provider_Dummysms
 */
class io_3sd_dummysms extends CRM_SMS_Provider {

  /**
   * provider details
   * @var	string
   */
  protected $_providerInfo = array();
  protected $_id = 0;


  /**
   * We only need one instance of this object. So we use the singleton
   * pattern and cache the instance in this variable
   *
   * @var object
   * @static
   */
  static private $_singleton = array();

  /**
   * Constructor
   * @return void
   */
  function __construct($provider, $skipAuth = TRUE) {
    // Instantiate the dummysms client
    $this->provider = $provider;
  }

  /**
   * singleton function used to manage this object
   *
   * @return object
   * @static
   *
   */
  static function &singleton($providerParams = array(), $force = FALSE) {
    if(isset($providerParams['provider'])){
      $providers = CRM_SMS_BAO_Provider::getProviders(NULL, array('name' => $providerParams['provider']));
      $providerID = current($providers)['id'];
    }else{
      $providerID = CRM_Utils_Array::value('provider_id', $providerParams);
    }
    $skipAuth   = $providerID ? FALSE : TRUE;
    $cacheKey   = (int) $providerID;

    if (!isset(self::$_singleton[$cacheKey]) || $force) {
      $provider = array();
      if ($providerID) {
        $provider = CRM_SMS_BAO_Provider::getProviderInfo($providerID);
      }
      self::$_singleton[$cacheKey] = new io_3sd_dummysms($provider, $skipAuth);
    }
    return self::$_singleton[$cacheKey];
  }

  /**
   * Send an SMS Message to a log file
   *
   * @param array the message with a to/from/text
   *
   * @return mixed SID on success or PEAR_Error object
   * @access public
   */
  function send($recipients, $header, $message, $jobID = NULL, $userID = NULL) {
    // "Send" a message to ConfigAndLog/sms_out.log
    try{
      // Write to a file in ConfigAndLogDir
      $config = CRM_Core_Config::singleton();
      $id = date('YmdHis');
      if (!empty($config->configAndLogDir)) {
        $file = $config->configAndLogDir . "/sms_out.log";
        $message = $id . ': to_number: ' . $header['To'] . '; ' . $message;
        file_put_contents($file, $message.PHP_EOL, FILE_APPEND | LOCK_EX);
      }

    }catch(Exception $e) {
      return PEAR::raiseError( $e->getMessage(), $e->getCode(), PEAR_ERROR_RETURN );
    }

    $this->createActivity($id, $message, $header, $jobID, $userID);
    return $id;
  }

  function inbound($from_number, $content, $id=NULL) {
    if (!isset($id)) {
      $id = date('YmdHis');
    }
    return parent::processInbound( $from_number, $content, NULL, $id );
  }
}
