<?php

require_once 'tagdriver.civix.php';
use CRM_Tagdriver_ExtensionUtil as E;

define('TAG_DRIVER_X', 'Tag Driver: Create CMS Account');
define('TAG_DRIVER_Z', 'Tag Driver: User Account');
define('TAG_DRIVER_Y', 'Tag Driver: Reset CMS Password');

function _tagdriver_activities() {
  static $activities;

  if (!$activities) {
    $activities = array(
      'activity_creation' => civicrm_api3('OptionValue', 'getvalue', array(
        'option_group_id' => 'activity_type',
        'name' => 'User Account Creation',
        'return' => 'value',
      )),
      'activity_password' => civicrm_api3('OptionValue', 'getvalue', array(
        'option_group_id' => 'activity_type',
        'name' => 'User Account Password Reset',
        'return' => 'value',
      )),
      'activity_failed' => civicrm_api3('OptionValue', 'getvalue', array(
        'option_group_id' => 'activity_status',
        'name' => 'Failed',
        'return' => 'value',
      )),
      'activity_completed' => civicrm_api3('OptionValue', 'getvalue', array(
        'option_group_id' => 'activity_status',
        'name' => 'Completed',
        'return' => 'value',
      )),
    );
  }
  return $activities;
}

function _tagdriver_tags() {
  static $tags;

  if (!$tags) {
    $tags = array(
      'tagdriver_x' => civicrm_api3('Tag', 'getvalue', array(
        'name' => TAG_DRIVER_X,
        'return' => 'id',
      )),
      'tagdriver_z' => civicrm_api3('Tag', 'getvalue', array(
        'name' => TAG_DRIVER_Z,
        'return' => 'id',
      )),
      'tagdriver_y' => civicrm_api3('Tag', 'getvalue', array(
        'name' => TAG_DRIVER_Y,
        'return' => 'id',
      )),
    );
  }
  return $tags;
}

/**
 * implements hook_civicrm_post().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_post/
 */
function tagdriver_civicrm_post($op, $objectName, $objectId, &$objectRef) {

  if ($objectName == 'EntityTag' && ($op == 'create' || $op == 'delete')) {
    $tags = _tagdriver_tags();

    if ($objectId == $tags['tagdriver_x'] || $objectId == $tags['tagdriver_y']) {
      $helper = CRM_Tagdriver_Helper::singleton();

      foreach ($objectRef as $something) {
        if (is_array($something)) {
          foreach ($something as $contact_id) {
            if ($op == 'create') {
              $helper->addContact($contact_id, $objectId);
            }
            else {
              $helper->removecontact($contact_id, $objectId);
            }
          }
        }
      }
    }
  }
}

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

  if (!empty($tokens['contact'])) {
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

  civicrm_api3('Tag', 'create', array(
    'name' => TAG_DRIVER_X,
    'description' => 'Contacts assigned this tag will have a CMS user account created for them automatically.',
    'used_for' => 'civicrm_contact',
  ));
  civicrm_api3('Tag', 'create', array(
    'name' => TAG_DRIVER_Z,
    'description' => 'Contacts that have had a CMS user account created for them will have this tag assigned to them.',
    'used_for' => 'civicrm_contact',
  ));
  civicrm_api3('Tag', 'create', array(
    'name' => TAG_DRIVER_Y,
    'description' => 'Contacts assigned this tag will have a password reset email sent to them automatically.',
    'used_for' => 'civicrm_contact',
  ));

  $params = array(
    'run_frequency' => 'Always',
    'name' => 'Tag Driver',
    'description' => 'Automatic CMS user account creation and password reset emails.',
    'api_entity' => 'tagdriver',
    'api_action' => 'execute',
    'is_active' => 1,
  );

  $domains = civicrm_api3('Domain', 'get', array(
    'sequential' => 1,
    'return' => array('id'),
  ));
  foreach ($domains['values'] as $domain) {
    $params['domain_id'] = $domain['id'];
    civicrm_api3('Job', 'create', $params);
  }
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

  $tags = _tagdriver_tags();
  foreach ($tags as $id) {
    civicrm_api3('Tag', 'delete', array(
      'id' => $id,
    ));
  }

  $jobs = civicrm_api3('Job', 'get', array(
    'sequential' => 1,
    'api_entity' => 'tagdriver',
    'return' => array('id'),
  ));
  foreach ($jobs['values'] as $job) {
    civicrm_api3('Job', 'delete', array(
      'id' => $job['id'],
    ));
  }
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
