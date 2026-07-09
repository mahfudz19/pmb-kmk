<?php

return array (
  'GET' => 
  array (
    'build/assets/(.*)' => 
    array (
      'handler' => 
      array (
        0 => 'App\\Core\\Controllers\\AssetController',
        1 => 'serve',
      ),
      'middlewares' => 
      array (
      ),
    ),
    'build/js/(.*)' => 
    array (
      'handler' => 
      array (
        0 => 'App\\Core\\Controllers\\AssetController',
        1 => 'serve',
      ),
      'middlewares' => 
      array (
      ),
    ),
  ),
);
