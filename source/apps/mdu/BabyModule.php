<?php

namespace Apiserver\Mdu\Modules;

class BabyModule extends ModuleBase
{
    public $baby;
    public $family;

    public function __construct()
    {
        $this->baby = $this->initModel('\Apiserver\Mdu\Models\BabyModel');
        $this->family = $this->initModel('\Apiserver\Mdu\Models\FamilyModel');
    }

    /**
     * [添加宝贝]
     * @param [type] $name    [description]
     * @param [type] $addtime [description]
     */
    public function addBaby($uid, $name, $addtime, $devNum)
    {
        $this->di['db']->begin();
        if($babyId = $this->baby->add($name, $addtime, $devNum))
        {
            if($this->family->addRel($babyId, $uid, $addtime, 1))
            {
                $this->di['db']->commit();
                return array('baby_id' => $babyId);
            }
            else
            {
                $this->di['db']->rollback();
                return FALSE;
            }
        }
        else
            return FALSE;
    }

    /*
    *增加宝贝设备个数
     */
    public function addShoeNum($babyId)
    {
        return $this->baby->addShoeNum($babyId);
    }
}