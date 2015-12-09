<?php
$loader = new Phalcon\Loader();

$loader->registerNamespaces(array(
    'Apiserver\Mdu\Models' => __DIR__ . '/../apps/mdu/models',
    'Apiserver\Mdu\Modules' => __DIR__ . '/../apps/mdu/',
    'Apiserver\Utils' => __DIR__ . '/../apps/utils',
    'Apiserver\Utils\MyValidator' => __DIR__ . '/../apps/utils/validator',
));
$loader->register();