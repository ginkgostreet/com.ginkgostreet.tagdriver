<?php

function civicrm_api3_user_create($params) {

  civicrm_api3_verify_mandatory($params, NULL, array('cms_name', 'email'));

  if (!empty($params['contactID'])) {
    try {
      $user = CRM_Core_Config::singleton()->userSystem->getUser($params['contactID']);
      $user['contactID'] = $params['contactID'];

      return civicrm_api3_create_error('CMS user account already exists for this contact.', $user);
    }
    catch (CiviCRM_API3_Exception $e) {
      // user account doesn't exist, just fall through to rest of code
    }
  }

  if (empty($params['cms_pass'])) {
    $params['cms_pass'] = md5(print_r($_SERVER, TRUE));
  }
  $params['notify'] = empty($params['notify']) ? 0 : 1;

  if ($uf_id = CRM_Core_BAO_CMSUser::create($params, 'email')) {
    return civicrm_api3_create_success(array('uf_id' => $uf_id), $params);
  }

  return civicrm_api3_create_error('Failed to create CMS user account', $params);
}

function _civicrm_api3_user_create_spec(&$params) {
  $params['cms_name'] = array(
    'api.required' => 1,
    'title' => 'CMS Username',
    'type' => CRM_Utils_Type::T_STRING,
  );
  $params['email'] = array(
    'api.required' => 1,
    'title' => 'Email Address',
    'type' => CRM_Utils_Type::T_EMAIL,
  );
  $params['cms_pass'] = array(
    'title' => 'CMS Password',
    'type' => CRM_Utils_Type::T_STRING,
  );
  $params['notify'] = array(
    'title' => 'Notify User',
    'description' => 'Whether an email should be sent to the user to notify them of account creation.',
    'type' => CRM_Utils_Type::T_BOOLEAN,
  );
  $params['contactID'] = array(
    'title' => 'Contact ID',
    'description' => 'CiviCRM contact ID',
    'type' => CRM_Utils_Type::T_INT,
  );
}
