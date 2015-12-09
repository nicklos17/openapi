<?php

namespace Apiserver\Mdu\Models;

class DevicesModel extends ModelBase
{

    /**
     * [根据qr码获取库存设备信息]
     * @param  [type] $qr [description]
     * @return [type]     [description]
     */
    public function getDevInfoByQrInStock($qr)
    {
        $query = $this->db->query('SELECT `devstock_uuid` as uuid, `devstock_imei` as imei, ' .
        '`devstock_mobi` as mobi, `devstock_pic` as pic, `devstock_pass` as pass, `devstock_ver` ' .
        ' as dver, `devstock_expires` as expire, `devstock_qr` as qr FROM `cloud_dev_stock` ' .
        ' WHERE `devstock_qr` = ? LIMIT 1',
            array($qr)
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    /**
     * 添加童鞋
     */
    public function addShoe($uid, $uuid, $imei, $mobi, $pass, $dver, $expire, $qr, $pic, $addtime, $babyId = '')
    {
        $this->db->execute('INSERT INTO `cloud_devices`(`u_id`, `dev_uuid`, `dev_imei`, ' .
        '`dev_mobi`, `dev_pass`, `dev_hard_ver`, `dev_expires`, `dev_qr`, `dev_pic`, `dev_actime`, `baby_id`) ' .
        'VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            array(
                $uid,
                $uuid,
                $imei,
                $mobi,
                $pass,
                $dver,
                $expire,
                $qr,
                $pic,
                $addtime,
                $babyId
            )
        );
        return $this->db->lastInsertId();
    }


    /**
     * 从设备表获取信息
     * @return [type] [description]
     */
    public function getDevInfoByQr($qr)
    {
        $query = $this->db->query('SELECT `dev_id` FROM `cloud_devices` WHERE `dev_qr` = ? LIMIT 1',
            array($qr)
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetch();
    }

    /**
     * 从设备库存表更新童鞋的服务期 cloud_dev_stock
     * @param unknown $uuid
     * @param unknown $expires
     */
    public function updateExpires($uuid, $expires)
    {
        return $this->db->execute('UPDATE `cloud_dev_stock` SET `devstock_expires` = ? WHERE ' .
        '`devstock_uuid` = ?',
            array(
                $expires,
                $uuid
            )
        );
    }

    /**
     * 删除童鞋
     */
    public function deleteShoe($uuid, $babyId, $uid)
    {
        $this->db->execute('DELETE FROM `cloud_devices` WHERE `dev_uuid` = ? AND `baby_id` = ? AND `u_id` = ? LIMIT 1',
            array(
                $uuid,
                $babyId,
                $uid
            )
        );
        return $this->db->affectedRows();
    }

    /**
     * [获取宝贝所有设备的信息]
     * @param  [type] $babyId [description]
     * @return [type]         [description]
     */
    public function getInfoByBabyId($babyId)
    {
        $query = $this->db->query('SELECT `dev_uuid`, `dev_battery` FROM `cloud_devices` WHERE `baby_id` = ?',
            array($babyId)
        );
        $query->setFetchMode(\Phalcon\Db::FETCH_ASSOC);
        return $query->fetchAll();
    }

}