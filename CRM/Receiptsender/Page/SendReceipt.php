<?php
use CRM_Receiptsender_ExtensionUtil as E;

class CRM_Receiptsender_Page_SendReceipt extends CRM_Core_Page {

  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(E::ts('Send Receipt'));
    $contactID = CRM_Core_Session::singleton()->getLoggedInContactID();
    $contributionID = $_GET["contribution"];
    if (isset($contactID) && isset($contributionID)) {
        $contact = civicrm_api3('Contact', 'getsingle', [
          'id' => $contactID,
        ]);
        $from = civicrm_api3('domain', 'getsingle', [
          'id' => 1,
        ]);
        list($sendReceipt, $subject, $message, $html) = CRM_Core_BAO_MessageTemplate::sendTemplate(
          array(
            'groupName' => 'msg_tpl_workflow_contribution',
            'valueName' => 'contribution_offline_receipt',
            'contactId' => $contact['id'],
            'contributionId' => $contributionID,
            'from' => $from['from_email'],
            'toName' => $contact['display_name'],
            'toEmail' => $contact['email'],
            'isTest' => FALSE,
            'PDFFilename' => ts('receipt') . '.pdf',
            'isEmailPdf' => FALSE,
          )
        );
        Civi::log()->debug(var_dump($sendReceipt));
        Civi::log()->debug(var_dump($html));
    }
    $url = "/user";
    //CRM_Utils_System::redirect($url);
    parent::run();
  }

}
