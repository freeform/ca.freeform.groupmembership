<?php
/**
 * Common functions for group membership
 */

function ca_freeform_groupmembership_getsettings () {
  // TODO set this up on enable and upgrade if changed
  $settings = array(
    'version' => '0.5',
  );
  $general = CRM_Core_BAO_Setting::getItem('ca.freeform.groupmembership', 'general');
  $settings['general'] = isset($general) ? $general : array();

  return $settings;
}

function ca_freeform_groupmembership_savesettings ($settings, $name = 'general') {
  $domain_id    = CRM_Core_Config::domainID();
  $result = CRM_Core_BAO_Setting::_setItem(array(), $settings[$name], 'ca.freeform.groupmembership', $name, $domain_id);


  return $settings;
}

/* To show the right renewal links on the dashboard */
function ca_freeform_groupmembership_get_renewal_links($contact_id) {
  $settings = ca_freeform_groupmembership_getsettings();
  $memberships = ca_freeform_groupmembership_get_memberships($contact_id);

  if (!empty($memberships)) {
    $link_list = array(
      'items' => array(),
      'type' => 'ul',
      'attributes' => array('class' => "membership-list")
    );
    foreach ($memberships as $membership) {
      if (array_search( $membership['membership_type_id'], $settings['hm_org_membership_type_id']) !== FALSE) {
        $page_id = $settings['hm_org_renewal_page_id'];
      }
      else {
        $page_id = $settings['hm_regular_renewal_page_id'];
      }
      $link_text = 'Renew ' . $membership['membership_name'] . ' Membership';
      $query = 'reset=1&id=' . $page_id;
      $link_list['items'][] = l(
        $link_text,
        'civicrm/contribute/transact',
        array(
          'attributes' => array('class' => 'renew'),
          'query' => array('reset' => '1', 'id' => $page_id), //$query,
        )
      );
    }
    return $link_list;

  }
}

function ca_freeform_groupmembership_get_memberships($contact_id, $only_current = FALSE) {
  $memberships = array();
  if ($contact_id == 0) {
    return $memberships;
  }
  // Get the current memberships
  $member_params = array(
    'contact_id' => $contact_id,
    'is_test' => 0,
    'version' => 3,
  );
  $results = civicrm_api("Membership","get", $member_params);
  if (($results['is_error'] == 0) && ($results['count'] > 0)) {
    if (!$only_current) {
      return $results['values'];
    }
    $memberships = $results['values'];
  }
  // Filter out expired etc.
  foreach ($memberships as $key => $membership) {
    if($membership['status_id'] > 3) {
      unset($memberships[$key]);
    }
  }
  return $memberships;
}

/**
 * @param $page
 */
function ca_freeform_groupmembership_contact_dashboard(&$page) {

  $config = CRM_Core_Config::singleton();

  //we only care about frontend display
  if (!$config->userFrameworkFrontend) {
    return;
  }

  //construct content for the user
  //can use $page->_contactId to filter api calls and queries on the user
  $links = ca_freeform_groupmembership_get_renewal_links($page->_contactId);

  // List memberships, assign content to a smarty variable so I can iterate through in the tpl file
  $page->assign('groupMembershipRenewalLinks', $links);
}