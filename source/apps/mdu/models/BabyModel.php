<?php

namespace Apiserver\Mdu\Models;

class BabyModel extends ModelBase
{
    /**
     * 将宝贝信息添加到数据库
     */
    public function add($name, $addtime, $devNum)
    {
        if($this->db->execute('INSERT INTO `cloud_babys` (`baby_nick`, `baby_addtime`, `baby_devs`) VALUES (?, ?, ?)',
            array(
                $name,
                $addtime,
                $devNum
            )
        ))
            return $this->db->lastInsertId();
        else
            return false;
    }

    /**
     * 完成童鞋绑定时，宝贝的装备数量+1
     * @param str $babyId
     * @return int
     */
    public function addShoeNum($babyId)
    {
        $this->db->execute('UPDATE `cloud_babys` SET `baby_devs` = `baby_devs` +1 ' .
        'WHERE `baby_id` = ?',
            array(
                $babyId
            )
        );
        return $this->db->affectedRows();
    }

    /**
     * 完成童鞋解绑时，宝贝的装备数量-1
     * @param str $babyId
     * @return int
     */
    public function subShoeNum($babyId)
    {
        $this->db->execute('UPDATE `cloud_babys` SET `baby_devs` = `baby_devs` -1 ' .
        'WHERE `baby_id` = ?',
            array(
                $babyId
            )
        );
        return $this->db->affectedRows();
    }
}