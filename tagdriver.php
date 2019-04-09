<?php

require_once 'tagdriver.civix.php';
use CRM_Tagdriver_ExtensionUtil as E;

/**
 * implements hook_civicrm_tokens().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_tokens/
 */
function tagdriver_civicrm_tokens(&$tokens) {

  $tokens['contact'] = array(
    'contact.first_initial' => ts('Contact First Initial'),
    'contact.last_initial' => ts('Contact Last Initial'),
  );
}

/**
 * implements hook_civicrm_tokenValues().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_tokenValues/
 */
function tagdriver_civicrm_tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {

  if (!empty($tokens['contact']['first_initial']) || !empty($tokens['contact']['last_initial'])) {
    $contacts = implode(',', $cids);

    $dao = CRM_Core_DAO::executeQuery("SELECT id, SUBSTRING(first_name, 1, 1) AS first_initial, SUBSTRING(last_name, 1, 1) AS last_initial
      FROM civicrm_contact
      WHERE id IN ($contacts)
    ");
    while ($dao->fetch()) {
      $values[$dao->id]['contact.first_initial'] = $dao->first_initial;
      $values[$dao->id]['contact.last_initial'] = $dao->last_initial;
    }
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function tagdriver_civicrm_config(&$config) {
  _tagdriver_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function tagdriver_civicrm_xmlMenu(&$files) {
  _tagdriver_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function tagdriver_civicrm_install() {
  _tagdriver_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function tagdriver_civicrm_postInstall() {
  _tagdriver_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function tagdriver_civicrm_uninstall() {
  _tagdriver_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function tagdriver_civicrm_enable() {
  _tagdriver_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function tagdriver_civicrm_disable() {
  _tagdriver_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function tagdriver_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _tagdriver_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function tagdriver_civicrm_managed(&$entities) {
  _tagdriver_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function tagdriver_civicrm_caseTypes(&$caseTypes) {
  _tagdriver_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function tagdriver_civicrm_angularModules(&$angularModules) {
  _tagdriver_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function tagdriver_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _tagdriver_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function tagdriver_civicrm_entityTypes(&$entityTypes) {
  _tagdriver_civix_civicrm_entityTypes($entityTypes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function tagdriver_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function tagdriver_civicrm_navigationMenu(&$menu) {
  _tagdriver_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _tagdriver_civix_navigationMenu($menu);
} // */
