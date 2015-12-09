<?php
namespace Apiserver\Utils;

class GateWay
{
    const CTRL_ERROR = 10010;
    const HEAD_ERROR = 10010;

    public function __construct($di)
    {
        $this->di = $di;
    }

    public function check($ctrl, $act)
    {
        $verConfig = $this->di['verConfig'];
        //获取头部的接口版本协议
        if(isset($_SERVER['HTTP_APIVER']))
            $ver = $_SERVER['HTTP_APIVER'];
        else
        {
            $this->errCode = self::HEAD_ERROR;
            return false;
        }

        //判断该接口是否废弃
        if(isset($verConfig['exclude'][$ver]) && in_array($ctrl . ':' . $act, $verConfig['exclude'][$ver]))
        {
            $this->errCode = self::CTRL_ERROR;
            return false;
        }

        if(!$vers = $verConfig['map'][$ctrl . ':' . $act])
        {
            $this->errCode = self::CTRL_ERROR;
            return false;
        }
        $this->ver = in_array($ver, $vers) ? $ver : max($vers);
        return true;
    }

    public function getVer()
    {
        return $this->ver;
    }

    public function errMsg()
    {
        $err = array(
            '10010' => '请求头部错误',
            '10011' => '接口url错误'
        );
        return array('flag' =>(string)$this->errCode, 'msg' => $err[$this->errCode]);
    }
}