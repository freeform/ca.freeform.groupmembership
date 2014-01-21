<?php

require_once 'groupmembership.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function groupmembership_civicrm_config(&$config) {
  _groupmembership_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function groupmembership_civicrm_xmlMenu(&$files) {
  _groupmembership_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function groupmembership_civicrm_install() {
  return _groupmembership_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function groupmembership_civicrm_uninstall() {
  return _groupmembership_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function groupmembership_civicrm_enable() {
  return _groupmembership_civix_civicrm_enable();
}

function groupmembership_civicrm_navigationMenu(&$params) {
  // get the maximum key of $params using method mentioned in discussion
  // https://issues.civicrm.org/jira/browse/CRM-13803
  $navId = CRM_Core_DAO::singleValueQuery("SELECT max(id) FROM civicrm_navigation");
  if (is_integer($navId)) {
    $navId++;
  }
  // Find the Memberships menu
  foreach($params as $key => $value) {
    if ('Memberships' == $value['attributes']['name']) {
      $params[$key]['child'][$navId] = array (
        'attributes' => array (
          'label' => '',
          'name' => 'group membership settings separator',
          'url' => '',
          'permission' => 'access CiviMember,administer CiviCRM',
          'operator' => 'AND',
          'separator' => 1,
          'parentID' => $key,
          'navID' => $navId,
          'active' => 1
        )
      );
      $navId++;
      $params[$key]['child'][$navId] = array (
        'attributes' => array (
          'label' => 'Group Membership - General Settings',
          'name' => 'group membership general settings',
          'url' => 'civicrm/groupmembership/generalsettings?reset=1',
          'permission' => 'access CiviMember,administer CiviCRM',
          'operator' => 'AND',
          'separator' => null,
          'parentID' => $key,
          'navID' => $navId,
          'active' => 1
        )
      );
      $navId++;
      $params[$key]['child'][$navId] = array (
        'attributes' => array (
          'label' => 'Group Membership - Membership Settings',
          'name' => 'group membership membership settings',
          'url' => 'civicrm/groupmembership/membersettings?reset=1',
          'permission' => 'access CiviMember,administer CiviCRM',
          'operator' => 'AND',
          'separator' => null,
          'parentID' => $key,
          'navID' => $navId,
          'active' => 1
        )
      );
    }

  }
}

/**
 * Implementation of hook_civicrm_disable
 */
function groupmembership_civicrm_disable() {
  return _groupmembership_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function groupmembership_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _groupmembership_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function groupmembership_civicrm_managed(&$entities) {
  return _groupmembership_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_buildForm
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function groupmembership_civicrm_buildForm($formName, &$form) {
  $valid_forms = array(
    'CRM_Profile_Form_Edit',
    'CRM_Contribute_Form_Contribution_ThankYou',
    'CRM_Contribute_Form_Contribution_Main',
    // 'CRM_Contribute_Form_Contribution_Confirm',
  );

  if(array_search($formName, $valid_forms) === FALSE) {
    return;
  }

  $session = CRM_Core_Session::singleton();
  $contact_id = $session->get('userID');
  $contact_id  = (isset($contact_id) && intval($contact_id) > 0) ? $contact_id : 0;
  $settings = ca_freeform_groupmembership_getsettings();


  // Family Member profile
  if ($formName == 'CRM_Profile_Form_Edit') {
    $gid = $form->getVar( '_gid' );
    $valid_profiles = array();
    foreach ($settings['general'] as $group) {
      if ($group['group_member_profile']) {
        $valid_profiles[] = $group['group_member_profile'];
      }
    }
    if (array_search($gid,$valid_profiles) !== FALSE ) {
      // Look for the submitter contact id and set it to the value passed or from the session
      $submitter_contact_id = CRM_Utils_Array::value('scid', $_GET, $contact_id);
      if ($submitter_contact_id > 0) {
        if ($form->getAction() == CRM_Core_Action::ADD) {
          $defaults['id'] = $submitter_contact_id;
          $form->setDefaults($defaults);
        }
      }
    }
  }
}

function groupmembership_civicrm_dashboard( $contactID, &$contentPlacement ) {
  // REPLACE Activity Listing with custom content
  $contentPlacement = CRM_Utils_Hook::DASHBOARD_BELOW;
  return array( 'Custom Content' => "Here is some custom content: $contactID",
    'Custom Table' => "
<table>
<tr><th>Contact Name</th><th>Date</th></tr>
<tr><td>Foo</td><td>Bar</td></tr>
<tr><td>Goo</td><td>Tar</td></tr>
</table>
",
  );
}

function groupmembership_civicrm_pageRun(&$page) {
  if ( $page->getVar('_name') == 'CRM_Contact_Page_View_UserDashBoard' ) {
    ca_freeform_groupmembership_contact_dashboard(&$page);
  }
}


