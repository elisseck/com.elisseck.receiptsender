<?php
use CRM_Receiptsender_ExtensionUtil as E;

class CRM_Receiptsender_Form_SendReceipt extends CRM_Contribute_Form_Task_PDF {
/*
  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(E::ts('Send Receipt'));
    $contactID = CRM_Core_Session::singleton()->getLoggedInContactID();
    $contributionID = $_GET["contribution"];
    if (isset($contactID) && isset($contributionID)) {
        $contact = civicrm_api3('Contact', 'getsingle', [
          'id' => $contactID,
<<<<<<< HEAD
        ]);
        $from = civicrm_api3('domain', 'getsingle', [
          'id' => 1,
        ]);
=======
        ]);
        $from = civicrm_api3('domain', 'getsingle', [
          'id' => 1,
        ]);
>>>>>>> 974d53f3bee71de748c02c58de56196faa68f3da
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
<<<<<<< HEAD
        Civi::log()->debug(var_dump($subject));
=======
        Civi::log()->debug(var_dump($html));
>>>>>>> 974d53f3bee71de748c02c58de56196faa68f3da
    }
    $url = "/user";
    //CRM_Utils_System::redirect($url);
    parent::run();
  }
*/
}
