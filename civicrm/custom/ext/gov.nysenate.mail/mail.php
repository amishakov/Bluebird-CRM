<?php

require_once 'mail.civix.php';

define('FILTER_ALL', 0);
define('FILTER_IN_SD_ONLY', 1);
define('FILTER_IN_SD_OR_NO_SD', 2);

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function mail_civicrm_config(&$config) {
  _mail_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function mail_civicrm_xmlMenu(&$files) {
  _mail_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function mail_civicrm_install() {
  _mail_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function mail_civicrm_uninstall() {
  _mail_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function mail_civicrm_enable() {
  _mail_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function mail_civicrm_disable() {
  _mail_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function mail_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _mail_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function mail_civicrm_managed(&$entities) {
  _mail_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function mail_civicrm_caseTypes(&$caseTypes) {
  _mail_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function mail_civicrm_angularModules(&$angularModules) {
_mail_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function mail_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _mail_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function mail_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function mail_civicrm_navigationMenu(&$menu) {
  _mail_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'gov.nysenate.mail')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _mail_civix_navigationMenu($menu);
} // */

function mail_civicrm_alterAngular(\Civi\Angular\Manager $angular) {
  //inject mailing form options
  $changeSet = \Civi\Angular\ChangeSet::create('inject_options')
    ->alterHtml('~/crmMailing/BlockMailing.html', '_mail_alterMailingBlock');
  $angular->add($changeSet);

  //inject wizard
  $changeSet = \Civi\Angular\ChangeSet::create('inject_wizard')
    ->alterHtml('~/crmMailing/EditMailingCtrl/workflow.html', '_mail_alterMailingWizard');
  $angular->add($changeSet);

  //11041 adjust mailing summary
  $changeSet = \Civi\Angular\ChangeSet::create('modify_review')
    ->alterHtml('~/crmMailing/BlockReview.html', '_mail_alterMailingReview');
  $angular->add($changeSet);
}

function mail_civicrm_pageRun(&$page) {
  //Civi::log()->debug('mail_civicrm_pageRun', array('page' => $page));

  //11038
  if (is_a($page, 'Civi\Angular\Page\Main')) {
    CRM_Core_Resources::singleton()->addStyleFile('gov.nysenate.mail', 'css/mail.css');
  }
}

function mail_civicrm_entityTypes(&$entityTypes) {
  //Civi::log()->debug('mail_civicrm_entityTypes', array('entityTypes' => $entityTypes));

  //formally declare our additions to the mailing table as entity fields
  $entityTypes['CRM_Mailing_DAO_Mailing']['fields_callback'][] = function($class, &$fields) {
    //Civi::log()->debug('mail_civicrm_entityTypes', array('$class' => $class, 'fields' => $fields));

    $fields['all_emails'] = array(
      'name' => 'all_emails',
      'type' => CRM_Utils_Type::T_INT,
      'title' => 'All Emails',
    );

    $fields['exclude_ood'] = array(
      'name' => 'exclude_ood',
      'type' => CRM_Utils_Type::T_INT,
      'title' => 'Exclude Out of District Emails',
    );

    $fields['category'] = array(
      'name' => 'category',
      'type' => CRM_Utils_Type::T_STRING,
      'title' => 'Category',
      'maxlength' => 255,
    );
  };
}

function mail_civicrm_alterMailingRecipients(&$mailing, &$queue, $job_id, &$params, $context) {
  /*Civi::log()->debug('', array(
    '$mailing' => $mailing,
    '$queue' => $queue,
    '$job_id' => $job_id,
  ));*/

  if ($context == 'pre') {
    unset($params['filters']['on_hold']);
  }

  if ($context == 'post') {
    // NYSS 4628, 4879
    if ($mailing->all_emails) {
      _mail_addAllEmails($mailing->id, $mailing->exclude_ood);
    }

    if ($mailing->exclude_ood != FILTER_ALL) {
      _mail_excludeOOD($mailing->id, $mailing->exclude_ood);
    }

    // NYSS 5581
    if ($mailing->category) {
      _mail_excludeCategoryOptOut($mailing->id, $mailing->category);
    }

    //add email seed group
    _mail_addEmailSeeds($mailing->id);

    //dedupe emails as final step
    if ($mailing->dedupe_email) {
      _mail_dedupeEmail($mailing->id);
    }

    //remove onHold as we didn't do it earlier
    _mail_removeOnHold($mailing->id);
  }

  //don't need this anymore?
  //recalculate the total recipients
  /*if ($form->_submitValues['all_emails'] || $excludeOOD != FILTER_ALL || $mailingCat) {
    $count = CRM_Mailing_BAO_Recipients::mailingSize($mailing->id);
    $form->set('count', $count);
    $form->assign('count', $count);
  }*/
}

function mail_civicrm_pre($op, $objectName, $id, &$params) {
  //set exclude_ood and other fixed default values
  if ($objectName == 'Mailing') {
    //exclude_ood is set from config file
    $bbconfig = get_bluebird_instance_config();
    $excludeOOD = FILTER_ALL;
    if (isset($bbconfig['email.filter.district'])) {
      $filter_district = $bbconfig['email.filter.district'];
      switch ($filter_district) {
        case "1": case "strict": case "in_sd":
          $excludeOOD = FILTER_IN_SD_ONLY;
          break;
        case "2": case "fuzzy": case "in_sd_or_no_sd":
          $excludeOOD = FILTER_IN_SD_OR_NO_SD;
          break;
        default:
          $excludeOOD = FILTER_ALL;
      }
    }
    $params['exclude_ood'] = $excludeOOD;

    $params['url_tracking'] = 0;
    $params['forward_replies'] = 0;
    $params['auto_responder'] = 0;
    $params['open_tracking'] = 0;
    $params['visibility'] = 'Public Pages';
  }
}

//NYSS 4870
function _mail_removeOnHold( $mailing_id ) {
  $sql = "
DELETE FROM civicrm_mailing_recipients
 USING civicrm_mailing_recipients
  JOIN civicrm_email
    ON ( civicrm_mailing_recipients.email_id = civicrm_email.id
   AND   civicrm_email.on_hold > 0 )
 WHERE civicrm_mailing_recipients.mailing_id = %1";

  $params = array( 1 => array( $mailing_id, 'Integer' ) );

  $dao = CRM_Core_DAO::executeQuery( $sql, $params );
}

/**
 * @param phpQueryObject $doc
 *
 * construct custom wizard html
 */
function _mail_alterMailingWizard(phpQueryObject $doc) {
  $extDir = CRM_Core_Resources::singleton()->getPath('gov.nysenate.mail');
  $html = file_get_contents($extDir.'/html/workflow.html');
  $doc->find('div[ng-form=crmMailingSubform]')->html($html);
}

/**
 * @param phpQueryObject $doc
 *
 * inject custom fields
 */
function _mail_alterMailingBlock(phpQueryObject $doc) {
  //NYSS 5581 - mailing category options
  $catOptions = "<option value=''>- select -</option>";
  $opts = CRM_Core_DAO::executeQuery("
    SELECT ov.label, ov.value
    FROM civicrm_option_value ov
    JOIN civicrm_option_group og
      ON ov.option_group_id = og.id
      AND og.name = 'mailing_categories'
    ORDER BY ov.label
  ");
  while ($opts->fetch()) {
    $catOptions .= "<option value='{$opts->value}'>{$opts->label}</option>";
  }

  $doc->find('.crm-group')->append('
    <div crm-ui-field="{name: \'subform.nyss\', title: \'Mailing Category\', help: hs(\'category\')}">
      <select 
        crm-ui-id="subform.nyss" 
        crm-ui-select="{dropdownAutoWidth : true, allowClear: true, placeholder: ts(\'Category\')}"
        name="category" 
        ng-model="mailing.category"
      >'.$catOptions.'</select>
    </div>
    <div crm-ui-field="{name: \'subform.nyss\', title: \'All Emails?\', help: hs(\'all-emails\')}">
      <input
        type="checkbox"
        crm-ui-id="subform.nyss"
        name="all_emails" 
        ng-model="mailing.all_emails"
        ng-true-value="\'1\'"
        ng-false-value="\'0\'"
      >
    </div>
  ');
}

function _mail_alterMailingReview(phpQueryObject $doc) {
  $extDir = CRM_Core_Resources::singleton()->getPath('gov.nysenate.mail');
  $html = file_get_contents($extDir.'/html/BlockReview.html');
  $doc->find('.crm-group')->html($html);
}

// NYSS 4628
function _mail_addAllEmails($mailingID, $excludeOOD = FILTER_ALL) {
  $sql = "
    INSERT IGNORE INTO civicrm_mailing_recipients (mailing_id, email_id, contact_id)
    SELECT DISTINCT %1, e.id, e.contact_id
    FROM civicrm_email e
    JOIN civicrm_mailing_recipients mr
      ON e.contact_id = mr.contact_id
      AND mr.mailing_id = %1
      AND e.on_hold = 0
    WHERE e.id NOT IN (
      SELECT email_id
      FROM civicrm_mailing_recipients mr
      WHERE mailing_id = %1
    )
  ";
  $params = array(1 => array($mailingID, 'Integer'));
  CRM_Core_DAO::executeQuery($sql, $params);
} // _addAllEmails()

// NYSS 4879
function _mail_excludeOOD($mailingID, $excludeOOD) {
  //determine what SD we are in
  $bbconfig = get_bluebird_instance_config();
  $district = $bbconfig['district'];

  if (empty($district)) {
    return;
  }

  //create temp table to store contacts confirmed to be in district
  $tempTbl = "nyss_temp_excludeOOD_$mailingID";
  $sql = "CREATE TEMPORARY TABLE $tempTbl(contact_id INT NOT NULL, PRIMARY KEY(contact_id)) ENGINE=MyISAM;";
  CRM_Core_DAO::executeQuery($sql);

  $sql = "
    INSERT INTO $tempTbl
    SELECT DISTINCT mr.contact_id
    FROM civicrm_mailing_recipients mr
    JOIN civicrm_address a
      ON mr.contact_id = a.contact_id
    JOIN civicrm_value_district_information_7 di
      ON a.id = di.entity_id
    WHERE mailing_id = $mailingID
      AND ny_senate_district_47 = $district;
  ";
  CRM_Core_DAO::executeQuery($sql);

  //also include unknowns if option enabled
  if ($excludeOOD == FILTER_IN_SD_OR_NO_SD) {
    //include where no district is known or no address is present
    $sql = "
      INSERT INTO $tempTbl
      SELECT mr.contact_id
      FROM civicrm_mailing_recipients mr
      LEFT JOIN civicrm_address a
        ON mr.contact_id = a.contact_id
      LEFT JOIN civicrm_value_district_information_7 di
        ON a.id = di.entity_id
      WHERE mr.mailing_id = $mailingID
      GROUP BY mr.contact_id
      HAVING COUNT(di.ny_senate_district_47) = 0
    ";
    CRM_Core_DAO::executeQuery($sql);
  }

  $sql = "
    DELETE FROM civicrm_mailing_recipients
    USING civicrm_mailing_recipients
    LEFT JOIN $tempTbl
      ON civicrm_mailing_recipients.contact_id = $tempTbl.contact_id
    WHERE civicrm_mailing_recipients.mailing_id = $mailingID
      AND $tempTbl.contact_id IS NULL;
  ";
  CRM_Core_DAO::executeQuery($sql);

  //cleanup
  CRM_Core_DAO::executeQuery("DROP TABLE $tempTbl");
} // _excludeOOD()

// NYSS 5581
function _mail_excludeCategoryOptOut($mailingID, $mailingCat) {
  $sql = "
    DELETE FROM civicrm_mailing_recipients
    USING civicrm_mailing_recipients
    JOIN civicrm_email
      ON civicrm_mailing_recipients.email_id = civicrm_email.id
    WHERE FIND_IN_SET({$mailingCat}, civicrm_email.mailing_categories)
      AND civicrm_mailing_recipients.mailing_id = $mailingID
  ";
  //CRM_Core_Error::debug_var('sql', $sql);
  CRM_Core_DAO::executeQuery($sql);
} // _excludeCategoryOptOut()

function _mail_addEmailSeeds($mailingID) {
  $gid = CRM_Core_DAO::singleValueQuery("SELECT id FROM civicrm_group WHERE name LIKE 'Email_Seeds';");

  if (!$gid) {
    return;
  }

  $sql = "
    INSERT INTO civicrm_mailing_recipients ( mailing_id, contact_id, email_id )
    SELECT $mailingID, e.contact_id, e.id
    FROM civicrm_group_contact gc
    JOIN civicrm_email e
      ON gc.contact_id = e.contact_id
      AND gc.group_id = $gid
      AND gc.status = 'Added'
      AND e.on_hold = 0
      AND ( e.is_primary = 1 OR e.is_bulkmail = 1 )
    JOIN civicrm_contact c
      ON gc.contact_id = c.id
    LEFT JOIN civicrm_mailing_recipients mr
      ON gc.contact_id = mr.contact_id
      AND mr.mailing_id = $mailingID
    WHERE mr.id IS NULL
      AND c.is_deleted = 0;
  ";
  CRM_Core_DAO::executeQuery($sql);
} // _addEmailSeeds()

function _mail_dedupeEmail($mailing_id) {
  //if dedupeEmails, handle that now, as it was skipped earlier in the process
  $tempTbl = "nyss_temp_dedupe_emails_$mailing_id";
  $sql = "CREATE TEMPORARY TABLE $tempTbl (email_id INT NOT NULL, PRIMARY KEY(email_id)) ENGINE=MyISAM;";
  CRM_Core_DAO::executeQuery($sql);

  $sql = "
    INSERT INTO $tempTbl
    SELECT ANY_VALUE(mr.email_id) email_id
    FROM civicrm_mailing_recipients mr
    JOIN civicrm_email e
      ON mr.email_id = e.id
    WHERE mailing_id = %1
    GROUP BY e.email;
  ";
  CRM_Core_DAO::executeQuery($sql, array(1 => array($mailing_id, 'Positive')));

  //now remove contacts from the recipients table that are not found in the inclusion table
  $sql = "
    DELETE FROM civicrm_mailing_recipients
    USING civicrm_mailing_recipients
    LEFT JOIN $tempTbl
      ON civicrm_mailing_recipients.email_id = $tempTbl.email_id
    WHERE civicrm_mailing_recipients.mailing_id = %1
      AND $tempTbl.email_id IS NULL;
  ";
  CRM_Core_DAO::executeQuery($sql, array(1 => array($mailing_id, 'Positive')));

  //cleanup
  CRM_Core_DAO::executeQuery("DROP TABLE $tempTbl");
}