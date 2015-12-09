<?php

namespace Apiserver\Mdu\Models;

class LocusInfoModel extends ModelBase
{

    /**
     * 定位
     * 根据时间段
     * @param int $uid 用户id
     * @param int $babyId
     * @param string $starttime
     * @param str $endtime
     * @return array
     */
    public function locateByStartAndEnd($babyId, $starttime, $endtime)
    {
        $res = $this->db->query('SELECT li_id as liid, li_coordinates as coor, li_start as start,'.
        ' li_title as place, li_battery as battery, li_end as end, li_steps as steps, li_runs as runs FROM '.
        'cloud_locus_info WHERE baby_id = ? AND li_start > ? AND li_start <= ?  ORDER BY li_start ASC',
            array($babyId, $starttime, $endtime)
        );
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetchAll();
    }

    /**
     * 定位
     * 从开始点到截至目前的时间内
     * @param  [type] $babyId    [description]
     * @param  [type] $starttime [description]
     * @return [type]            [description]
     */
    public function locateByStart($babyId, $starttime)
    {
        $res = $this->db->query('SELECT li_id as liid, li_coordinates as coor, li_start as start,'.
        ' li_title as place, li_battery as battery, li_end as end, li_steps as steps, li_runs as runs FROM '.
        'cloud_locus_info WHERE baby_id = ? AND li_start > ? ORDER BY li_start ASC',
            array($babyId, $starttime)
        );
        $res->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $res->fetchAll();
    }
}