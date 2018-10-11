<?php
use CRM_Receiptsender_ExtensionUtil as E;

class CRM_Receiptsender_Page_SendReceipt extends CRM_Core_Page {

  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(E::ts('Send Receipt'));
    $uid = $_GET["user"];
    $contributionID = $_GET["contribution"];
    if (isset($uid) && isset($contributionID)) {
      try {
        $result = civicrm_api3('Contribution', 'sendconfirmation', [
          'id' => $contributionID,
        ]);
      }
      catch (CiviCRM_API3_Exception $e) {
        $error = $e->getMessage();
        watchdog('CiviCRM Receipt', $error);
        CRM_Core_Error::debug_log_message($error);
      }
    }
    $url = "/user";
    CRM_Utils_System::redirect($url);
    parent::run();
  }

}
