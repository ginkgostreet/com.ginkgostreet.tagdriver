<?php

function civicrm_api3_tagdriver_execute($params) {

  // use settings as defined in default domain
  $settings = Civi::settings(1);

  $pattern = $settings->get('tagdriver_pattern');
  $domainID = CRM_Core_Config::domainID();
  $helper = CRM_Tagdriver_Helper::singleton();
  $activities = _tagdriver_activities();

  $tags = _tagdriver_tags();
  $tags['tagdriver_tb'] = $settings->get('tagdriver_tb');

  $do_first = array();
  $do_last = array();

  do {
    $api = civicrm_api3('EntityTag', 'get', array(
      'sequential' => 1,
      'tag_id' => $tags['tagdriver_x'],
      'options' => array(
        'sort' => 'id ASC',
        'offset' => count($do_last),
        'limit' => 25,
      ),
    ));
    foreach ($api['values'] as $entity) {
      $do_last[] = $entity['entity_id'];
    }
  } while ($api['count'] > 0);

  if ($tags['tagdriver_tb'] && count($do_last) > 0) {
    do {
      $api = civicrm_api3('EntityTag', 'get', array(
        'sequential' => 1,
        'tag_id' => $tags['tagdriver_tb'],
        'entity_id' => array(
          'IN' => $do_last,
        ),
        'options' => array(
          'sort' => 'id ASC',
          'offset' => count($do_first),
          'limit' => 25,
        )
      ));
      foreach ($api['values'] as $entity) {
        $do_first[] = $entity['entity_id'];
      }
    } while ($api['count'] > 0);

    $do_last = array_diff($do_last, $do_first);
  }

  $contact_ids = $do_first + $do_last;

  $not_this_domain = array();
  foreach ($contact_ids as $contact_id) {
    if (!$helper->isContact($contact_id, $tags['tagdriver_x'])) {
      $not_this_domain[] = $contact_id;
    }
  }
  $contact_ids = array_diff($contact_ids, $not_this_domain);

  if (count($contact_ids) > 0) {
    // generate usernames
    $p = new \Civi\Token\TokenProcessor(\Civi::dispatcher(), [
      'controller' => __CLASS__,
      'smarty' => FALSE,
    ]);
    $p->addMessage('username', $pattern, 'text/plain');

    foreach ($contact_ids as $contactID) {
      $p->addRow()->context('contactId', $contactID);
    }
    $p->evaluate();

    // create the accounts
    foreach ($p->getRows() as $row) {
      $contactID = $row->context['contactId'];
      $cms_name = $row->render('username');
      $api = NULL;

      // don't bother attempting to create user account
      // when the contact is already connected to a user
      $matches = civicrm_api3('UFMatch', 'get', array(
        'sequential' => 1,
        'contact_id' => $contactID,
      ));
      if ($matches['count'] > 0) {
        foreach ($matches['values'] as $match) {
          if ($match['domain_id'] == $domainID) {
            $api = array(
              'is_error' => 1,
              'error_message' => 'Contact is already connected to a CMS user account.',
            );
            break;
          }
        }
        if (!$api) {
          civicrm_api3('UFMatch', 'create', array(
            'contact_id' => $contactID,
            'domain_id' => $domainID,
            'uf_id' => $match['uf_id'],
            'uf_name' => $match['uf_name'],
          ));
          $api = array(
            'is_error' => 1,
            'error_message' => 'Contact was connected to an existing CMS user account.',
          );
        }
      }
      else {
        try {
          $createParams = array(
            'cms_name' => $cms_name,
            'contactID' => $contactID,
            'notify' => 1,
          );
          $createParams['email'] = civicrm_api3('Email', 'getvalue', array(
            'contact_id' => $contactID,
            'is_primary' => 1,
            'return' => 'email',
          ));
          $api = civicrm_api3('User', 'create', $createParams);
        }
        catch (CiviCRM_API3_Exception $e) {
          $api = array(
            'is_error' => 1,
            'error_message' => $e->getMessage(),
          );
        }
      }

      if (empty($api['is_error'])) {
        try {
          civicrm_api3('EntityTag', 'create', array(
            'entity_table' => 'civicrm_contact',
            'entity_id' => $contactID,
            'tag_id' => $tags['tagdriver_z'],
          ));
        }
        catch (CiviCRM_API3_Exception $e) {
          // tag z already set
        }
        civicrm_api3('Activity', 'create', array(
          'source_record_id' => $contactID,
          'target_contact_id' => $contactID,
          'activity_type_id' => $activities['activity_creation'],
          'status_id' => $activities['activity_completed'],
          'subject' => "$cms_name ({$api['values']['uf_id']})",
          'check_permissions' => 0,
        ));
      }
      else {
        civicrm_api3('Activity', 'create', array(
          'source_record_id' => $contactID,
          'target_contact_id' => $contactID,
          'activity_type_id' => $activities['activity_creation'],
          'status_id' => $activities['activity_failed'],
          'subject' => "Failed to create $cms_name",
          'details' => $api['error_message'],
          'check_permissions' => 0,
        ));
      }
      civicrm_api3('EntityTag', 'delete', array(
        'entity_table' => 'civicrm_contact',
        'entity_id' => $contactID,
        'tag_id' => $tags['tagdriver_x'],
      ));
      $helper->removeContact($contactID, $tags['tagdriver_x']);
    }
  }

  // send password reset emails
  $to_reset = array();
  do {
    $api = civicrm_api3('EntityTag', 'get', array(
      'sequential' => 1,
      'tag_id' => $tags['tagdriver_y'],
      'options' => array(
        'sort' => 'id ASC',
        'offset' => count($to_reset),
        'limit' => 25,
      ),
    ));
    foreach ($api['values'] as $entity) {
      $to_reset[] = $entity['entity_id'];
    }
  } while ($api['count'] > 0);

  $config = CRM_Core_Config::singleton();
  if ($config->userSystem->is_drupal) {
    require_once DRUPAL_ROOT . '/modules/user/user.pages.inc';
  }

  foreach ($to_reset as $contactID) {

    if (!$helper->isContact($contactID, $tags['tagdriver_y'])) {
      continue;
    }

    try {
      $uf_id = civicrm_api3('UFMatch', 'getvalue', array(
        'contact_id' => $contactID,
        'domain_id' => $domainID,
        'return' => 'uf_id',
      ));

      if ($config->userSystem->is_drupal) {
        $user = user_load($uf_id);
        $form_state = array(
          'values' => array(
            'account' => $user,
          ),
        );
        user_pass_submit(NULL, $form_state);

        civicrm_api3('Activity', 'create', array(
          'source_record_id' => $contactID,
          'target_contact_id' => $contactID,
          'activity_type_id' => $activities['activity_password'],
          'status_id' => $activities['activity_completed'],
          'subject' => 'Sent',
          'check_permissions' => 0,
        ));
      }
      else {
        throw new CiviCRM_API3_Exception('Password reset not supported by the installed CMS.');
      }
    }
    catch (CiviCRM_API3_Exception $e) {
      civicrm_api3('Activity', 'create', array(
        'source_record_id' => $contactID,
        'target_contact_id' => $contactID,
        'activity_type_id' => $activities['activity_password'],
        'status_id' => $activities['activity_failed'],
        'subject' => 'Failed',
        'details' => $e->getMessage(),
        'check_permissions' => 0,
      ));
    }
    civicrm_api3('EntityTag', 'delete', array(
      'entity_table' => 'civicrm_contact',
      'entity_id' => $contactID,
      'tag_id' => $tags['tagdriver_y'],
    ));
    $helper->removeContact($contactID, $tags['tagdriver_y']);
  }
}
