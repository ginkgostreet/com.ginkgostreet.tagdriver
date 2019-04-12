<?php

return array(
  array(
    'name' => 'tagdriver.job',
    'entity' => 'Job',
    'params' => array(
      'run_frequency' => 'Always',
      'name' => 'Tag Driver',
      'description' => 'Automatic CMS user account creation and password reset emails.',
      'api_entity' => 'tagdriver',
      'api_action' => 'execute',
      'is_active' => 1,
    ),
    'update' => 'never',
  ),
);
