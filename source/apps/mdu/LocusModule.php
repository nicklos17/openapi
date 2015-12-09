<?php

namespace Apiserver\Mdu\Modules;

class LocusModule extends ModuleBase
{
    public $babyRanks;
    public $locusInfo;

    public function __construct()
    {
        $this->babyRanks = $this->initModel('\Apiserver\Mdu\Models\BabyRanksModel');
        $this->locusInfo = $this->initModel('\Apiserver\Mdu\Models\LocusInfoModel');
    }

    /**
     * [宝贝健康数据汇总]
     * @param  [type] $babyId [description]
     * @return [type]         [description]
     */
    public function getBabySummary($babyId)
    {
        if(!$res = $this->babyRanks->getSummary($babyId))
            $res = array();

        return $res;
    }

    /**
     * [获取定位数据]
     * @param  [type] $babyId [description]
     * @param  [type] $start  [description]
     * @param  [type] $end    [description]
     * @return [type]         [description]
     */
    public function getLocate($babyId, $start, $end)
    {
        if($end == '0')
            return $this->locusInfo->locateByStart($babyId, $start);
        else
            return $this->locusInfo->locateByStartAndEnd($babyId, $start, $end);
    }
}