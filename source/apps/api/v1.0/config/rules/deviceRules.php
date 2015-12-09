<?php
include __DIR__.'/baseRules.php';

$rules['active'] = array(
    '_method' => array(
        'post' => array('appid', 'sign', 'timestamp', 'dev_qr', 'baby_id')
    ),
    'appid' => checkAppid(),
    'sign' => checkSign(),
    'timestamp' => checkTimestamp(),
    'dev_qr' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '10006'
    ),
    'baby_id' => array(
        'required' => 0,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '10006'
    )
);

$rules['unbind'] = array(
    '_method' => array(
        'post' => array('appid', 'sign', 'timestamp', 'baby_id', 'shoe_code')
    ),
    'appid' => checkAppid(),
    'sign' => checkSign(),
    'timestamp' => checkTimestamp(),
    'baby_id' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '10006'
    ),
    'shoe_code' => array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '10006'
    )
);

$rules['info'] = array(
    '_method' => array(
        'get' => array('appid', 'sign', 'timestamp', 'baby_id')
    ),
    'appid' => checkAppid(),
    'sign' => checkSign(),
    'timestamp' => checkTimestamp(),
    'baby_id' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '10006'
    )
);

return $rules;

