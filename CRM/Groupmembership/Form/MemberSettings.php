<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Groupmembership_Form_MemberSettings extends CRM_Core_Form {
  protected $_settings;

  function preProcess() {
    require_once 'groupmembership.inc';
    $this->_settings = ca_freeform_groupmembership_getsettings();
  }

  function buildQuickForm() {
    $defaults = array();
    $membership_options = $this->getGroupMemberships();
    $profile_options = $this->getUnReservedProfiles();
    $relationship_options = $this->getRelationships();

    $field_grouping = array(
      'apply_to_membership_type' => array('Apply to Membership Type', $membership_options),
      'use_relationship_type' => array('Use Relationship Type', $relationship_options),
      'group_member_profile' => array('Group Member Profile', $profile_options),
    );

    $existing = array();

    foreach ($this->_settings['general'] as $index => $setting_group) {
      // add form elements for existing settings
      foreach ($field_grouping as $key => $fg) {
        $name = "{$key}_{$index}";
        $this->add(
          'select',
          $name,
          $fg[0],
          $fg[1],
          true // is required
        );
        $existing[$index][] = $name;
        $defaults[$name] = isset($this->_settings['general'][$index][$key]) ?
          $this->_settings['general'][$index][$key] : NULL;
      }
    }
    $new_elements = array();
    // add form elements for new settings
    foreach ($field_grouping as $key => $fg) {
      $name = "{$key}_-1";
      $this->add(
        'select',
        $name,
        $fg[0],
        $fg[1],
        false // is required
      );
      $new_elements[] = $name;
    }
    // TODO: Add cancel button
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));
    //$list = $this->getRenderableElementNames();
    // export form elements
    $this->assign('existingNames', $existing);
    $this->assign('elementNames', $new_elements);
    $this->setDefaults($defaults);
    parent::buildQuickForm();
  }

  function postProcess() {
    $values = $this->exportValues();
    require_once 'groupmembership.inc';
    $groups = array();
    $new_group = array();
    foreach ($values as $form_key => $field_value) {

      foreach (array('apply_to_membership_type', 'use_relationship_type', 'group_member_profile') as $field) {
        $index = str_replace($field . '_', '', $form_key);
        if ($index == -1) {
          $new_group[$field] = $field_value;
        }
        else if (is_integer($index) && isset ($this->_settings['general'][$index])) {
          $groups[$index][$field] = $field_value;
        }
      }
    }
    // Save new value
    //dpm(array($new_group, $this->_settings['general'], $groups));
    if(!empty($new_group)) {
      $this->_settings['general'][] = $new_group;
    }
    // Update existing values
    $general = $this->_settings['general'] + $groups;
    //dpm($general);
    $this->_settings['general'] = $general;
    ca_freeform_groupmembership_savesettings($this->_settings);
    CRM_Core_Session::setStatus(ts('Settings saved.'));
    parent::postProcess();
  }

  function getGroupMemberships() {
    $options = array('' => ts('- Select -'));
    $params = array(
      'version' => 3,
      'sequential' => 0,
      'limit' => 100,
    );
    $membership_results = civicrm_api('MembershipType', 'get', $params);
    if ($membership_results['is_error'] == 0) {
      foreach ($membership_results['values'] as $id => $type) {
        // If the membership type has a relationship inheritance
        if (isset($type['relationship_type_id'])) {
          $options[$id] = $type['name'];
        }
      }
    }
    return $options;
  }

  function getUnReservedProfiles() {
    $profiles = array('' => ts('- Select -'));
    $params = array(
      'version' => 3,
      'sequential' => 0,
      'limit' => 100,
    );
    $profile_results = civicrm_api('UFGroup', 'get', $params);
    if ($profile_results['is_error'] == 0) {
      foreach ($profile_results['values'] as $id => $profile) {
        // If the membership type has a relationship inheritance
        if ($profile['is_reserved'] == '0') {
          $profiles[$id] = $profile['title'];
        }
      }
    }

    return $profiles;
  }

  function getRelationships() {
    $relation_types = array('' => ts('- Select -'));
    $params = array(
      'version' => 3,
      'sequential' => 0,
      'limit' => 100,
    );
    $type_results = civicrm_api('RelationshipType', 'get', $params);
    if ($type_results['is_error'] == 0) {
      foreach ($type_results['values'] as $id => $type) {
        foreach (array('a' => 'a_b', 'b' => 'b_a') as $direction) {
          // If the membership type has a relationship inheritance
          if ((!isset($type['is_reserved']) || $type['is_reserved'] == '0') && $type['is_active'] == '1') {
            $relation_types[$id . '_' . $direction] = $type['label_' . $direction] . " ({$direction})";
          }
        }
      }
    }

    return $relation_types;
  }
}
