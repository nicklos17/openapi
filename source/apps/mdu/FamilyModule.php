<?php

namespace Apiserver\Mdu\Modules;

class FamilyModule extends ModuleBase
{
    public $family;

    public function __construct()
    {
        $this->family = $this->initModel('\Apiserver\Mdu\Models\FamilyModel');
    }

    /**
     * [设置宝贝和亲人的关系]
     * @param [type] $uid    [用户id]
     * @param [type] $babyId [宝贝id]
     * @param [type] $rel    [关系：1-3-5]
     */
    public function setRelation($uid, $babyId, $rel)
    {
        return $this->family->addRel($babyId, $uid, $_SERVER['REQUEST_TIME'], $rel);
    }


    /**
     * 检查用户与宝贝的关系
     * @param str $uid 用户id
     * @param str $babyId 宝贝id
     * @return boolean|json 
     */
    public function checkRelation($uid, $babyId)
    {
        return $this->family->getRelationByUidBabyId($uid, $babyId);
    }

}