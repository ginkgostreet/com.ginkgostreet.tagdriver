<?php

return array(
  array(
    'name' => 'tagdriver.activity.create',
    'entity' => 'OptionValue',
    'params' => array(
      'option_group_id' => 'activity_type',
      'name' => 'User Account Creation',
      'description' => 'Tag driver automatic CMS user account created.',
      'is_active' => 1,
    )
  ),
  array(
    'name' => 'tagdriver.activity.password',
    'entity' => 'OptionValue',
    'params' => array(
      'option_group_id' => 'activity_type',
      'name' => 'User Account Password Reset',
      'description' => 'Tag driver sent a password reset email.',
      'is_active' => 1,
    )
  ),
  array(
    'name' => 'tagdriver.activity.failed',
    'entity' => 'OptionValue',
    'params' => array(
      'option_group_id' => 'activity_status',
      'name' => 'Failed',
      'description' => 'Activity failed.',
      'is_active' => 1,
    )
  ),
);
