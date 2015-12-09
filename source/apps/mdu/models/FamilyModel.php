<?php

namespace Apiserver\Mdu\Models;

class FamilyModel extends ModelBase
{
    /**
     * 添加亲人
     * @param str $babyId
     * @param str $rel 用户与宝贝的关系
     * @param str $uid
     * @param str $type
     * @param str $addtime
     */
    public function addRel($babyId, $uid, $addtime, $relation)
    {
        $this->db->execute('INSERT INTO `cloud_family` (`baby_id`, `u_id`, `family_addtime`, `family_relation`) VALUES (?, ?, ?, ?)',
                array(
                    $babyId,
                    $uid,
                    $addtime,
                    $relation
                )
            );
        return $this->db->affectedRows();
    }

    /**
     * 获取宝贝与用户的关系
     * @param unknown $uid
     * @param unknown $babyId
     */
    public function getRelationByUidBabyId($uid, $babyId)
    {
        $query = $this->db->query('SELECT `u_id`, `family_relation`, `family_rolename` FROM ' .
        '`cloud_family` WHERE `u_id` = ? AND `baby_id` = ? AND `family_status` = 1 limit 1',
            array(
                $uid,
                $babyId
            )
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }
}