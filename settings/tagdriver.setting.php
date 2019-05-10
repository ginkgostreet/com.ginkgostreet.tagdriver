<?php

use CRM_Tagdriver_ExtensionUtil as E;

return array(
  'tagdriver_tb' => array(
    'name' => 'tagdriver_tb',
    'type' => 'Integer',
    'title' => E::ts('Tie Breaker'),
    'description' => E::ts('If multiple contacts with the same email address are tagged for automatic user account creation, the one with this tag wins.'),
    'is_contact' => 0,
    'is_domain' => 0,
  ),
  'tagdriver_pattern' => array(
    'name' => 'tagdriver_pattern',
    'type' => 'String',
    'title' => E::ts('Username Pattern'),
    'description' => E::ts('Pattern for username selection. All CiviCRM tokens are supported.'),
    'is_contact' => 0,
    'is_domain' => 0,
  ),
);
