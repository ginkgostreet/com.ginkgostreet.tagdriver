<?php

/**
 * Manages the tagdriver_contacts setting, which holds all the
 * contact IDs that have been tagged for processing.
 *
 * It prevents a get/set for each contact when contacts get tagged
 * in bulk, and provides the check to make sure a contact is processed
 * only by the domain that tagged it.
 */
class CRM_Tagdriver_Helper {

  private static $singleton;

  private $contacts;

  private function __construct() {
    $contacts = Civi::settings()->get('tagdriver_contacts');
    $this->contacts = $contacts ? (array) json_decode($contacts) : array();

    // <SUP-1860>
    foreach ($this->contacts as $tag_id => &$contact_ids) {
      if (!is_array($contact_ids)) {
        $contact_ids = (array) $contact_ids;
      }
    }
    // </SUP-1860>

    register_shutdown_function(array($this, 'shutdown'));
  }

  public static function singleton() {
    if (!self::$singleton) {
      self::$singleton = new CRM_Tagdriver_Helper();
    }
    return self::$singleton;
  }

  public function addContact($contact_id, $tag_id) {
    if (!isset($this->contacts[$tag_id])) {
      $this->contacts[$tag_id] = array();
    }
    if (!in_array($contact_id, $this->contacts[$tag_id])) {
      $this->contacts[$tag_id][] = $contact_id;
    }
  }

  public function isContact($contact_id, $tag_id) {
    return isset($this->contacts[$tag_id]) && in_array($contact_id, $this->contacts[$tag_id]);
  }

  public function removeContact($contact_id, $tag_id) {
    if (isset($this->contacts[$tag_id])) {
      $this->contacts[$tag_id] = array_diff($this->contacts[$tag_id], array($contact_id));
    }
  }

  public function shutdown() {
    Civi::settings()->set('tagdriver_contacts', json_encode($this->contacts));
  }
}
