<?php

/**
 * Register application modules
 */
$application->registerModules(array(
    'api_1.0' => array(
        'className' => 'Apiserver\v1\Module',
        'path' => __DIR__ . '/../apps/api/v1.0/Module.php'
    )
));