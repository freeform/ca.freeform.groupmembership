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