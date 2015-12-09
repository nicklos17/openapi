<?php

namespace Apiserver\Utils;

class CheckSign
{
    /**
     * 检查签名
     * @param  [type] $params [description]
     * @param  [type] $sign   [description]
     * @return [type]         [description]
     */
    public static function checkSign($params, $sign)
    {
        if(self::createSHA1Sign($params) === $sign)
            return true;
        else
            return false;
    }

    /**
     * 创建签名SHA1
     * @param  [type] $packageParams [description]
     * @return [type]                [description]
     */
    public static function createSHA1Sign($packageParams)
    {
        $signPars = '';
        ksort($packageParams);
        foreach($packageParams as $k=> $v) {
            if($signPars == ''){
                $signPars = $k. '=' .$v;
            }else{
                $signPars =$signPars. '&' .$k. '=' .$v;
            }
        }
        $sign = SHA1($signPars);
        return $sign;
    }
}