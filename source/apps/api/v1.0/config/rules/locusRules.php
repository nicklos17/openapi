<?php
include __DIR__.'/baseRules.php';

$rules['summary'] = array(
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

$rules['index'] = array(
    '_method' => array(
        'get' => array('appid', 'sign', 'timestamp', 'baby_id', 'start', 'end')
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
    'start' => checkTimestamp(),
    'end' => array(
        'required' => 1,
        'filters' => 'trim',
        'regex' => '/^\d+$/',
        'msg' => '10006'
    ),
);

return $rules;

