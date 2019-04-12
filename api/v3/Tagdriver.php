<?php

function civicrm_api3_tagdriver_execute($params) {

  $tags = _tagdriver_tags();
  $tags['tagdriver_tb'] = Civi::settings()->get('tagdriver_tb');

  $activities = _tagdriver_activities();

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

  if ($tags['tagdriver_tb']) {
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

  if (count($contact_ids) > 0) {
    // generate usernames
    $p = new \Civi\Token\TokenProcessor(\Civi::dispatcher(), [
      'controller' => __CLASS__,
      'smarty' => FALSE,
    ]);
    $p->addMessage('username', Civi::settings()->get('tagdriver_pattern'), 'text/plain');

    foreach ($contact_ids as $contactID) {
      $p->addRow()->context('contactId', $contactID);
    }
    $p->evaluate();

    // create the accounts
    foreach ($p->getRows() as $row) {
      try {
        $createParams = array(
          'cms_name' => $row->render('username'),
          'contactID' => $row->context['contactId'],
          'notify' => 1,
        );
        $createParams['email'] = civicrm_api3('Email', 'getvalue', array(
          'contact_id' => $row->context['contactId'],
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

      if (empty($api['is_error'])) {
        civicrm_api3('EntityTag', 'create', array(
          'entity_table' => 'civicrm_contact',
          'entity_id' => $createParams['contactID'],
          'tag_id' => $tags['tagdriver_z'],
        ));
        civicrm_api3('Activity', 'create', array(
          'source_record_id' => $createParams['contactID'],
          'target_contact_id' => $createParams['contactID'],
          'activity_type_id' => $activities['activity_creation'],
          'status_id' => $activities['activity_completed'],
          'subject' => "{$createParams['cms_name']} ({$api['values']['uf_id']})",
          'check_permissions' => 0,
        ));
      }
      else {
        civicrm_api3('Activity', 'create', array(
          'source_record_id' => $createParams['contactID'],
          'target_contact_id' => $createParams['contactID'],
          'activity_type_id' => $activities['activity_creation'],
          'status_id' => $activities['activity_failed'],
          'subject' => "Failed to create {$createParams['cms_name']}",
          'details' => $api['error_message'],
          'check_permissions' => 0,
        ));
      }
      civicrm_api3('EntityTag', 'delete', array(
        'entity_table' => 'civicrm_contact',
        'entity_id' => $createParams['contactID'],
        'tag_id' => $tags['tagdriver_x'],
      ));
    }
  }



}
