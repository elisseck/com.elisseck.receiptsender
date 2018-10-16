<?php
use CRM_Receiptsender_ExtensionUtil as E;

class CRM_Receiptsender_Form_SendReceipt extends CRM_Contribute_Form_Task_PDF {

  public function preProcess() {
    $id = CRM_Utils_Request::retrieve('id', 'Positive',
      $this, FALSE
    );

    if ($id) {
      $this->_contributionIds = array($id);
      $this->_componentClause = " civicrm_contribution.id IN ( $id ) ";
      $this->_single = TRUE;
      $this->assign('totalSelectedContributions', 1);
    }
    else {
      parent::preProcess();
    }

    // check that all the contribution ids have pending status
    $query = "
  SELECT count(*)
  FROM   civicrm_contribution
  WHERE  contribution_status_id != 1
  AND    {$this->_componentClause}";
    $count = CRM_Core_DAO::singleValueQuery($query);
    if ($count != 0) {
      CRM_Core_Error::statusBounce("Please select only online contributions with Completed status.");
    }

    $this->assign('single', $this->_single);

    $qfKey = CRM_Utils_Request::retrieve('qfKey', 'String', $this);
    $urlParams = 'force=1';
    if (CRM_Utils_Rule::qfKey($qfKey)) {
      $urlParams .= "&qfKey=$qfKey";
    }

    $url = CRM_Utils_System::url('civicrm/contribute/search', $urlParams);
    $breadCrumb = array(
      array(
        'url' => $url,
        'title' => ts('Search Results'),
      ),
    );
    CRM_Contact_Form_Task_EmailCommon ::preProcessFromAddress($this, FALSE);
    // we have all the contribution ids, so now we get the contact ids
    parent::setContactIDs();
    CRM_Utils_System::appendBreadCrumb($breadCrumb);
    CRM_Utils_System::setTitle(ts('Print Contribution Receipts'));

    // get all the details needed to generate a receipt
    $message = array();
    $template = CRM_Core_Smarty::singleton();

    $params['pdf_receipt'] = 1;
    $elements = self::getElements($this->_contributionIds, $params, $this->_contactIds);
    $elements['createPdf'] = 1;

    foreach ($elements['details'] as $contribID => $detail) {
      $input = $ids = $objects = array();

      if (in_array($detail['contact'], $elements['excludeContactIds'])) {
        continue;
      }

      $input['component'] = $detail['component'];

      $ids['contact'] = $detail['contact'];
      $ids['contribution'] = $contribID;
      $ids['contributionRecur'] = NULL;
      $ids['contributionPage'] = NULL;
      $ids['membership'] = CRM_Utils_Array::value('membership', $detail);
      $ids['participant'] = CRM_Utils_Array::value('participant', $detail);
      $ids['event'] = CRM_Utils_Array::value('event', $detail);

      if (!$elements['baseIPN']->validateData($input, $ids, $objects, FALSE)) {
        CRM_Core_Error::fatal();
      }

      $contribution = &$objects['contribution'];

      // set some fake input values so we can reuse IPN code
      $input['amount'] = $contribution->total_amount;
      $input['is_test'] = $contribution->is_test;
      $input['fee_amount'] = $contribution->fee_amount;
      $input['net_amount'] = $contribution->net_amount;
      $input['trxn_id'] = $contribution->trxn_id;
      $input['trxn_date'] = isset($contribution->trxn_date) ? $contribution->trxn_date : NULL;
      $input['receipt_update'] = 1;
      $input['contribution_status_id'] = $contribution->contribution_status_id;
      $input['paymentProcessor'] = empty($contribution->trxn_id) ? NULL :
        CRM_Core_DAO::singleValueQuery("SELECT payment_processor_id
          FROM civicrm_financial_trxn
          WHERE trxn_id = %1
          LIMIT 1", array(
            1 => array($contribution->trxn_id, 'String')));

      // CRM_Contribute_BAO_Contribution::composeMessageArray expects mysql formatted date
      $objects['contribution']->receive_date = CRM_Utils_Date::isoToMysql($objects['contribution']->receive_date);

      $values = array();
      if (isset($params['from_email_address']) && !$elements['createPdf']) {
        // CRM-19129 Allow useres the choice of From Email to send the receipt from.
        $fromDetails = explode(' <', $params['from_email_address']);
        $input['receipt_from_email'] = substr(trim($fromDetails[1]), 0, -1);
        $input['receipt_from_name'] = str_replace('"', '', $fromDetails[0]);
      }

      $mail = CRM_Contribute_BAO_Contribution::sendMail($input, $ids, $objects['contribution']->id, $values,
        $elements['createPdf']);

      if ($mail['html']) {
        $message[] = $mail['html'];
      }
      else {
        $message[] = nl2br($mail['body']);
      }

      // reset template values before processing next transactions
      $template->clearTemplateVars();
    }

    if ($elements['createPdf']) {
      CRM_Utils_PDF_Utils::html2pdf($message,
        'civicrmContributionReceipt.pdf',
        FALSE,
        $elements['params']['pdf_format_id']
      );
      $url = "/user";
      CRM_Utils_System::redirect($url);
    }
    else {
      if ($elements['suppressedEmails']) {
        $status = ts('Email was NOT sent to %1 contacts (no email address on file, or communication preferences specify DO NOT EMAIL, or contact is deceased).', array(1 => $elements['suppressedEmails']));
        $msgTitle = ts('Email Error');
        $msgType = 'error';
      }
      else {
        $status = ts('Your mail has been sent.');
        $msgTitle = ts('Sent');
        $msgType = 'success';
      }
      CRM_Core_Session::setStatus($status, $msgTitle, $msgType);
    }

  }

}
