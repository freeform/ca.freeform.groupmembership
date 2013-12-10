<?php
/**
 * Common functions for group membership
 */

function ca_freeform_groupmembership_getsettings () {
  $settings = array(
    'version' => '0.5',
  );
  $general = CRM_Core_BAO_Setting::getItem('ca.freeform.groupmembership', 'general');
  $settings['general'] = isset($general) ? $general : array();

  return $settings;
}
