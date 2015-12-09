<?php

namespace Apiserver\Utils;

class Common
{

    /**
     * 获取服务期的期限
     * @param str $timestamp  开始服务的时间戳
     * @param str $base   服务期基数： 3个月 6个月..
     */
    public static function expires($timestamp, $base = '3')
    {
        $arr = getdate($timestamp);
        $res = self::monthToDay($arr['year'], $arr['mon']);
        //获得服务开始月份的第一天
        $firstday = $res['0'];
        //获得服务开始月份的最后一天
        $lastday = $res['1'];
        
        //计算到期的年月
        if($arr['mon'] == '10' || $arr['mon'] == '11' || $arr['mon'] == '12')
        {
            $month = $arr['mon'] - (12 -$base);
            $year = $arr['year'] + 1;
        }
        else
        {
            $month = $arr['mon'] + $base;
            $year = $arr['year'];
        }
        
        //获取服务到期月份的最后一天
        $expires = self::monthToDay($year, $month);
        
        //如果到期月份是2月并且是平年
        if($month == '2' && (date('d', $expires[1]) == 28))
        {
            //如果服务开始的时间日期是29号，或者30号，则2月份到期的日期为2月份最后一天
            if($arr['mday'] == '29' || $arr['mday'] == '30' || $arr['mday'] == '31')
                $expiresDate = $expires[1];
            else
                $expiresDate = mktime(23,59,59, $month, $arr['mday']-1, $year);
        }
        //如果到期月份是2月并且是闰年
        elseif($month == '2' && (date('d', $expires[1]) == 29))
        {
            //如果服务开始的时间日期是30号或者31号，则2月份到期的日期为2月份最后一天
            if($arr['mday'] == '30' || $arr['mday'] == '31')
                $expiresDate = $expires[1];
            else
                $expiresDate = mktime(23,59,59, $month, $arr['mday']-1, $year);
        }
        else
        {
            //如果服务开始日期为当月第一天，那么服务到期时间为到期月份的最后一天
            if($arr['mday'] === date('d', $firstday))
                $expiresDate = $expires[1];
            else
                $expiresDate = mktime(23,59,59, $month, $arr['mday']-1, $year);
        }

        return (string)$expiresDate;
    }

    /**
     * 构造可解密的密文
     * $string 明文或密文
     * $operation 加密ENCODE或解密DECODE
     * $key 密钥
     * $expiry 密钥有效期
     */ 
    public static function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
    {
        // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
        // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
        // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
        // 当此值为 0 时，则不产生随机密钥
        $ckey_length = 0;
      
        // 密匙a会参与加解密
        $keya = md5(substr($key, 0, 16));
        // 密匙b会用来做数据完整性验证
        $keyb = md5(substr($key, 16, 16));
        // 密匙c用于变化生成的密文
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
        // 参与运算的密匙
        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
        // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        // 产生密匙簿
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上并不会增加密文的强度
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 核心加解密部分
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            // 从密匙簿得出密匙进行异或，再转成字符
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if($operation == 'DECODE') {
            // substr($result, 0, 10) == 0 验证数据有效性
            // substr($result, 0, 10) - time() > 0 验证数据有效性
            // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
            // 验证数据有效性，请看未加密明文的格式
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }

    /**
     * 返回指定月份的第一天和最后一天
     * @param string $y   年份
     * @param string $m   月份
     * @return array
     */
    public static function monthToDay($y = '', $m = '')
    {
        if($y == '')
        {
            $y = date('Y');
        }
        if($m == '')
        {
            $m = date('m');
        }
        $m = sprintf("%02d", intval($m));
        $y = str_pad(intval($y), 4, "0", STR_PAD_RIGHT);
        $m>12||$m<1 ? $m = 1: $m =$m;
        $firstday = strtotime($y . $m . '01');
        $firstdaystr = date('Y-m-01', $firstday);
        $endday = strtotime(date('Y-m-d 23:59:59', strtotime("$firstdaystr +1 month -1 day")));
        return array($firstday, $endday);
    }
}