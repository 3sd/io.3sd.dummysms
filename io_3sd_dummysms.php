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


  const MESSAGE_DIRECTION_OUTBOUND = 1;
  const MESSAGE_DIRECTION_INBOUND = 2;

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
      $providerID = empty($providers) ? 0 : current($providers)['id'];
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
    $id = date('YmdHis');
    try {
      self::logToFile($id, $header['To'], $message, self::MESSAGE_DIRECTION_OUTBOUND);
      $this->createActivity($id, $message, $header, $jobID, $userID);
    } catch(Exception $e) {
      return PEAR::raiseError( $e->getMessage(), $e->getCode(), PEAR_ERROR_RETURN );
    }
    return $id;
  }

  /**
   * Write the given SMS message to the sms log file.
   *
   * @param string $id Unique (in the scope of the log file0 ID for this message.
   * @param string $number Mobile phone number for the recipient (if outbound)
   *  or sender (if inbound)
   * @param string $message The message content
   * @param int Whether the message is inbound (self::MESSAGE_DIRECTION_INBOUND)
   *  or outbound (self::MESSAGE_DIRECTION_OUTBOUND)
   */
  function logToFile($id, $number, $message, $direction_id) {
    $config = CRM_Core_Config::singleton();
    if (!empty($config->configAndLogDir)) {
      switch ($direction_id) {
        case self::MESSAGE_DIRECTION_INBOUND:
          $direction_label = "from";
        break;

        case self::MESSAGE_DIRECTION_OUTBOUND:
          $direction_label = "to";
        break;

      }
      $file = $config->configAndLogDir . "/sms_out.log";
      $line = "{$id}: {$direction_label}_number: {$number}; {$message}" . PHP_EOL;
      file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }
  }

  function inbound($from_number, $content, $id=NULL) {
    if (!isset($id)) {
      $id = date('YmdHis');
    }
    return parent::processInbound( $from_number, $content, NULL, $id );
  }
}
