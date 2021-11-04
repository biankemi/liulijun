<?php

namespace micro\models;

use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

class OrderGoods extends ActiveRecord
{
    private static array $allowViewField = ["id", "order_id", "goods_id", "goods_spec_id", "goods_name","good_num","final_price"];

    public static function tableName(): string
    {
        return '{{order_goods}}';
    }

    /**
     * @return array|ActiveRecord[]
     */
    public static function getList($orderIds): array
    {
        is_array($orderIds) || $orderIds = [$orderIds];
        $list = self::find()->select(self::$allowViewField)->where(['in', 'order_id', $orderIds])->asArray()->all();
        if (empty($list)) {
            return $list;
        }
        return ArrayHelper::index($list, "null", "order_id");
    }

    /**
     * @param $fields
     * @param $data
     * @return bool
     */
    public static function create($fields,$data): bool
    {
        try {
            \Yii::$app->db->createCommand()->batchInsert('order_goods', $fields, $data)->execute();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}