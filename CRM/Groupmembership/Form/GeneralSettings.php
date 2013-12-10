<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Groupmembership_Form_GeneralSettings extends CRM_Core_Form {
  protected $_settings;

  function preProcess() {
    require_once 'groupmembership.inc';
    $this->_settings = ca_freeform_groupmembership_getsettings();
  }

  function buildQuickForm() {
    $defaults = array();
    $membership_options = $this->getGroupMemberships();
    $profile_options = $this->getUnReservedProfiles();

    foreach ($this->_settings['general'] as $index => $setting_group) {
      // add form elements for existing settings
      $this->add(
        'select',
        "apply_to_membership_type_$index",
        'Apply to Membership Type',
        $membership_options,
        true // is required
      );
      $this->add(
        'select', // field type
        "group_member_profile_$index",
        'Group Member Profile', // field label
        $profile_options, // list of options
        true // is required
      );
      $defaults["apply_to_membership_type[$index]"] = isset($this->_settings['general'][$index]) ?
        $this->_settings['general'][$index]['apply_to_membership_type'] : NULL;
      $defaults["group_member_profile[$index]"] = isset($this->_settings['general'][$index]) ?
        $this->_settings['general'][$index]['group_member_profile'] : NULL;

    }

    // add form elements for new settings
    $this->add(
      'select', // field type
      "apply_to_membership_type_-1",
      'Apply to Membership Type', // field label
      $membership_options, // list of options
      true // is required
    );
    $this->add(
      'select', // field type
      "group_member_profile_-1",
      'Group Member Profile', // field label
      $profile_options, // list of options
      true // is required
    );

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));
    $list = $this->getRenderableElementNames();
    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    $this->setDefaults($defaults);
    parent::buildQuickForm();
  }

  function postProcess() {
    $values = $this->exportValues();
    CRM_Core_Session::setStatus(ts('Settings saved.'));
    parent::postProcess();
  }


  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

  function getGroupMemberships() {
    $options = array('' => ts('- Select -'));
    $params = array(
      'version' => 3,
      'sequential' => 0,
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


}
