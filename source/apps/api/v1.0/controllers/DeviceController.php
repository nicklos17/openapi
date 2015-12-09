<?php
namespace Apiserver\v1\Controllers;

use Apiserver\Mdu\Modules\DevicesModule as Devices,
    Apiserver\Mdu\Modules\BabyModule as Baby,
    Apiserver\Mdu\Modules\FamilyModule as Family,
    Apiserver\Utils\Common;

class DeviceController extends ControllerBase
{

    private $devices;
    private $baby;

    const SUCCESS = 1;
    const ACTIVE_FAILED = 10004;
    const HAVE_ACTIVED = 10007;
    const NON_EXISTS_DEVICE = 10003;
    const FAILED_UNBIND = 10008;

    public function initialize()
    {
        $this->devices = new Devices;
        $this->baby = new Baby;
        $this->family = new Family;
    }

    /**
     * [设备激活]
     * @return [type] [description]
     */
    public function activeAction()
    {
        $qr = substr(explode('@', $this->_sanReq['dev_qr'])[0], 2);
        //一双设备在已绑定的情况下，不能再次绑定
        if(!empty($this->devices->getDevInfoByQr($qr)))
            $this->_showMsg(self::HAVE_ACTIVED, $this->di['flagmsg'][self::HAVE_ACTIVED]);

        //todo:设备只能绑定在特定的app上
        if(empty($stockInfo = $this->devices->getStockInfo($qr)))
            $this->_showMsg(self::NON_EXISTS_DEVICE, $this->di['flagmsg'][self::NON_EXISTS_DEVICE]);

        $uid = $this->di['providerInfo'][$this->_sanReq['appid']]['u_id'];

        //根据是否上传babyId,激活设备
        if(!isset($this->_sanReq['baby_id']))
        {
            //创建一个宝贝
            if(!is_array($baby = $this->baby->addBaby(
                $uid,
                $this->di['providerInfo'][$this->_sanReq['appid']]['app_name'],
                $_SERVER['REQUEST_TIME'],
                1
            )))
                $this->_showMsg(self::ACTIVE_FAILED, $this->di['flagmsg'][self::ACTIVE_FAILED]);
            else
                $babyId = $baby['baby_id'];
        }
        else
        {
            $babyId = $this->_sanReq['baby_id'];
            //查看宝贝和厂商的关系
            $this->_oauthrity($uid, $babyId);

            //绑定给宝贝的设备个数+1
            $this->baby->addShoeNum($babyId);
        }

        if(is_array(($res = $this->devices->bindDev($stockInfo, $babyId, $uid,$this->di['providerInfo'][$this->_sanReq['appid']]['encode_key']))))
        {
            if(isset($this->_sanReq['baby_id']))
            {
                unset($res['baby_id']);
                $this->_returnResult(array('flag' => (string)self::SUCCESS, 'data' => $res));
            }
            else
                $this->_returnResult(array('flag' => (string)self::SUCCESS, 'data' => $res));
        }
        else
            $this->_showMsg($res, $this->di['flagmsg'][$res]);
    }

    /**
     * [设备解绑]
     * @return [type] [description]
     */
    public function unbindAction()
    {
        $uid = $this->di['providerInfo'][$this->_sanReq['appid']]['u_id'];

        //查看宝贝和厂商的关系
        $this->_oauthrity($uid, $this->_sanReq['baby_id']);

        if($this->devices->delDevice(
                Common::authcode(base64_decode($this->_sanReq['shoe_code']), 'DECODE', $this->di['providerInfo'][$this->_sanReq['appid']]['encode_key']),
                $this->_sanReq['baby_id'],
                $uid) === 1)
            $this->_showMsg(self::SUCCESS);
        else
            $this->_showMsg(self::FAILED_UNBIND, $this->di['flagmsg'][self::FAILED_UNBIND]);
    }

    /**
     * [获取宝贝的所有设备信息]
     * @return [type] [description]
     */
    public function infoAction()
    {
        //查看宝贝和厂商的关系
        $this->_oauthrity($this->di['providerInfo'][$this->_sanReq['appid']]['u_id'], $this->_sanReq['baby_id']);
        $this->_returnResult(
            array(
                'flag' => (string)self::SUCCESS,
                'data' =>$this->devices->getInfoByBabyId($this->_sanReq['baby_id'], $this->di['providerInfo'][$this->_sanReq['appid']]['encode_key'])
                )
            );
    }

}