<?php

//验证appid
function checkAppid()
{
    return array(
        'required' => 1,
        'length' => 8,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '10002'
    );
}

//检查时间戳
function checkTimestamp()
{
    return array(
        'required' => 1,
        'regex' => '/^\d+$/',
        'filters' => 'trim',
        'msg' => '10006'
    );
}

//检查时间戳
function checkSign()
{
    return array(
        'required' => 1,
        'filters' => 'trim',
        'msg' => '10006'
    );
}