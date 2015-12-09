<?php
namespace Apiserver\v1\Controllers;

use Apiserver\Mdu\Modules\LocusModule as Locus;

class LocusController extends ControllerBase
{

    private $locus;

    const SUCCESS = 1;
    const OUT_OF_TIME = 10009;

    public function initialize()
    {
        //查看宝贝和厂商的关系
        $this->_oauthrity($this->di['providerInfo'][$this->_sanReq['appid']]['u_id'], $this->_sanReq['baby_id']);

        $this->locus = new Locus;
    }

    /**
     * [宝贝信息汇总]
     * @return [type] [description]
     */
    public function summaryAction()
    {
        $this->_returnResult(
            array(
                'flag' => (string)self::SUCCESS,
                'data' =>$this->locus->getBabySummary($this->_sanReq['baby_id'])
                )
            );
    }

    /**
     * [获取定位数据]
     * @return [type] [description]
     */
    public function indexAction()
    {
        $start = $this->_sanReq['start'];
        $end = $this->_sanReq['end'];
        //查询限制在最近3个月,start和end的距离不能超过72小时
        if($_SERVER['REQUEST_TIME'] - $start >= $this->di['sysConfig']['locateLastime'] || ($end - $start > 0 && $end - $start > $this->di['sysConfig']['locateTimesRange']))
            $this->_showMsg(self::OUT_OF_TIME, $this->di['flagmsg'][self::OUT_OF_TIME]);

        $this->_returnResult(
            array(
                'flag' => (string)self::SUCCESS,
                'data' =>$this->locus->getLocate($this->_sanReq['baby_id'], $start, $end)
                )
            );
    }
}