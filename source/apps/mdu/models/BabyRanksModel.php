<?php

namespace Apiserver\Mdu\Models;

class BabyRanksModel extends ModelBase
{

    /**
     * 获取宝贝行程汇总数据
     */
    public function getSummary($babyId)
    {
        $query = $this->db->query('SELECT br_mileages as miles, br_guards as guards, br_steps as steps FROM cloud_baby_ranks WHERE baby_id = ? LIMIT 1',
            array($babyId)
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }
}