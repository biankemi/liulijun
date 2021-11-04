<?php

namespace micro\models;

use yii\db\ActiveRecord;
use yii\db\Exception;

class Order extends ActiveRecord
{
    private static array $allowViewField = ["id", "order_sn", "user_id", "order_amount","pay_time","order_status","pay_status","pay_res","create_time"];


    public static function tableName(): string
    {
        return '{{order}}';
    }

    /**
     * @return array|ActiveRecord[]
     */
    public static function getList(): array
    {
        return self::find()->select(self::$allowViewField)->asArray()->all();
    }

    /**
     * @param $orderId
     * @return array
     */
    public static function getDetail($orderId): array
    {
        return self::find()->select(self::$allowViewField)->where(['id'=>$orderId])->one();
    }

    /**
     * @param $orderSn
     * @return array
     */
    public static function getDetailBySn($orderSn): array
    {
        return self::find()->select(self::$allowViewField)->where(['order_sn'=>$orderSn])->one();
    }

    /**
     * @param $userid
     * @return array|ActiveRecord[]
     */
    public static function getMyOrders($userid): array
    {
        return self::find()->select(self::$allowViewField)->where(['user_id'=>$userid])->asArray()->all();
    }


    /**
     * @param $data
     * @return int|string
     */
    public static function create($data)
    {
        $data['order_sn'] = date("YmdHis").mt_rand(1000,9999);
        $data['create_time'] = time();
        try {
            \Yii::$app->db->createCommand()->insert('order', $data)->execute();
        } catch (Exception $e) {
            return 0;
        }
        return \Yii::$app->db->getLastInsertID();
    }

    /**
     * @param $orderId
     * @param $data
     * @return bool
     */
    public static function edit($orderId,$data): bool
    {
        try {
            \Yii::$app->db->createCommand()->update('order', $data, 'order_id = ' . $orderId)->execute();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}