<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Groupmembership_Form_GeneralSettings extends CRM_Core_Form {

  protected $_settings;
  protected $_field_list;

  function __construct() {
    $this->_field_list =  array(
      'secondary_renewal_pages_policy' => array(
        'title' => 'Secondary Member Renewal Pages Policy',
        'options' => array(
          0 => ts('Nothing: Allow Secondary Members to Renew'),
          1 => ts('Warn Secondary Members not to Renew'),
          2 => ts('Prevent Secondary from Renewing')
        ),
        'type' => 'select'
      ),
      'secondary_renewal_pages_warning_text' => array(
        'title' => 'Secondary Members Renewal Warning Text',
        'type' => 'wysiwyg',
      ),
    );
    parent::__construct();
  }

  function buildQuickForm() {
    $defaults = array();
    $existing = array();

    require_once 'groupmembership.inc';
    $this->_settings = ca_freeform_groupmembership_getsettings();


     // Select for policy
    $name = 'secondary_renewal_pages_policy';
    $this->add( 'select', $name, $this->_field_list[$name]['title'],
      $this->_field_list[$name]['options'], true //required
    );

    $name = 'secondary_renewal_pages_warning_text';
    $this->addWysiwyg($name, $this->_field_list[$name]['title'], null, false );

    foreach ($this->_field_list as $name => $not_used) {
      $existing[] = $name;

      $defaults[$name] = isset($this->_settings['general'][$name]) ?
        $this->_settings['general'][$name] : NULL;
    }

    // TODO: Add cancel button
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // add form elements for existing settings
    $this->assign('elementNames', $existing);
    $this->setDefaults($defaults);

    parent::buildQuickForm();
  }

  function postProcess() {
    $values = $this->exportValues();
    //require_once 'groupmembership.inc';
    $new_general = array();

    foreach ($values as $form_key => $field_value) {
      if ($form_key == 'secondary_renewal_pages_warning_text') {
        // TODO: Does this need to be escaped for mysql or html cleaned up?
        $new_general['secondary_renewal_pages_warning_text'] = $field_value;

      }
      else if($form_key == 'secondary_renewal_pages_policy') {
        if (array_search($field_value, $this->_field_list['secondary_renewal_pages_policy']['options']) !== FALSE) {
          $new_general['secondary_renewal_pages_policy'] = $field_value;
        }
      }
    }

    // Update existing values
    $general = $this->_settings['general'] + $new_general;
    dpm($general);
    $this->_settings['general'] = $general;
    ca_freeform_groupmembership_savesettings($this->_settings);
    CRM_Core_Session::setStatus(ts('Settings saved.'));

    parent::postProcess();
  }
}
