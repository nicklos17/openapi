<?php

namespace Apiserver\Mdu\Modules;

use Apiserver\Utils\Common;

class DevicesModule extends ModuleBase
{
    public $devices;

    const NON_DEVICE = 10003;
    const ACTIVE_FAILED = 10004;
    const SUCCESS = 1;

    public function __construct()
    {
        $this->devices = $this->initModel('\Apiserver\Mdu\Models\DevicesModel');
        $this->baby = $this->initModel('\Apiserver\Mdu\Models\BabyModel');
    }

    /**
     * [根据qr从devices表获取信息]
     * @param  [type] $qr [description]
     * @return [type]     [description]
     */
    public function getDevInfoByQr($qr)
    {
        return $this->devices->getDevInfoByQr($qr);
    }

    /**
     * [获取库存信息]
     * @param  [type] $qr [description]
     * @return [type]     [description]
     */
    public function getStockInfo($qr)
    {
        return $this->devices->getDevInfoByQrInStock($qr);
    }

    /**
     * [根据qr码绑定设备]
     * @param  [type] $stockInfo [库存信息]
     * @return [type]     [description]
     */
    public function bindDev($stockInfo, $babyId, $uid, $key = '')
    {
        $this->di['db']->begin();
        //设备首次激活时，计算服务时间
        if(empty($stockInfo['expire']))
        {
            $stockInfo['expire'] = Common::expires($_SERVER['REQUEST_TIME']);
            if(!$this->devices->updateExpires($stockInfo['uuid'], $stockInfo['expire']))
            {
                $this->di['db']->rollback();
                return self::ACTIVE_FAILED;
            }
        }

         if($this->devices->addShoe($uid, $stockInfo['uuid'], $stockInfo['imei'], $stockInfo['mobi'], $stockInfo['pass'], $stockInfo['dver'], $stockInfo['expire'], $stockInfo['qr'], $stockInfo['pic'], $_SERVER['REQUEST_TIME'], $babyId))
        {
            $this->di['db']->commit();
            return array(
                'baby_id' => (string)$babyId,
                'expire' => (string)$stockInfo['expire'],
                'shoe_code' => base64_encode(Common::authcode($stockInfo['uuid'], 'ENCODE', $key))
            );
        }
        else
        {
            $this->di['db']->rollback();
            return self::ACTIVE_FAILED;
        }
    }

    /**
     * 删除童鞋
     * @param  [type] $uuid   [description]
     * @param  [type] $babyId [description]
     * @param  [type] $uid    [description]
     * @return [type]         [description]
     */
    public function delDevice($uuid, $babyId, $uid)
    {
        $this->di['db']->begin();
        if(!$this->devices->deleteShoe($uuid, $babyId, $uid))
        {
            $this->di['db']->rollback();
            return self::ACTIVE_FAILED;
        }
        else
        {
            if($this->baby->subShoeNum($babyId))
            {
                $this->di['db']->commit();
                return self::SUCCESS;
            }
            else
            {
                $this->di['db']->rollback();
                return self::ACTIVE_FAILED;
            }
        }
    }

    /**
     * [获取宝贝所有的设备信息]
     * @param  [type] $babyId [description]
     * @return [type]         [description]
     */
    public function getInfoByBabyId($babyId, $key)
    {
        $devInfo = $this->devices->getInfoByBabyId($babyId);
        if(!empty($devInfo))
        {
            foreach ($devInfo as $k => $val)
            {
                $devInfo[$k]['shoe_code'] = base64_encode(Common::authcode($val['dev_uuid'], 'ENCODE', $key));
                unset($devInfo[$k]['dev_uuid']);
            }
        }

        return $devInfo;
    }

}