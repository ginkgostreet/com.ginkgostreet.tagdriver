<?php

use CRM_Tagdriver_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Tagdriver_Form_Settings extends CRM_Core_Form {

  public function buildQuickForm() {
    $tags = array('' => '--- select ---');
    $api = civicrm_api3('Tag', 'get', array(
      'sequential' => 1,
      'is_selectable' => 1,
      'is_tagset' => 0,
      'used_for' => 'civicrm_contact',
      'return' => 'id,name',
    ));
    foreach ($api['values'] as $tag) {
      $tags[$tag['id']] = $tag['name'];
    }

    $this->add('select', 'tagdriver_tb', 'Process these first', $tags);
    $this->add('text', 'tagdriver_pattern', 'Username pattern', array('size' => 60), TRUE);

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Save'),
        'isDefault' => TRUE,
      ),
    ));

    // use settings as defined in default domain
    $settings = Civi::settings(1);

    $this->setDefaults(array(
      'tagdriver_tb' => $settings->get('tagdriver_tb'),
      'tagdriver_pattern' => $settings->get('tagdriver_pattern'),
    ));

    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();

    // use settings as defined in default domain
    $settings = Civi::settings(1);

    foreach ($values as $k => $v) {
      if (strpos($k, 'tagdriver_') === 0) {
        $settings->set($k, $v);
      }
    }

    CRM_Core_Session::setStatus('The settings have been saved.', 'Success', 'success');

    parent::postProcess();
  }
}
