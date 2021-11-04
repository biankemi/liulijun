<?php

namespace micro\models;

use yii\db\ActiveRecord;
use yii\db\Exception;

class UserCommissionLog extends ActiveRecord
{
    private static array $allowViewField = ["id", "user_id", "level_id", "superior_user_id", "superior_level_id", "amount", "create_time"];

    public static function tableName(): string
    {
        return '{{user_commission_log}}';
    }


    /**
     * @param $levelId
     * @return array
     */
    public static function getList($levelId): array
    {
        return self::find()->select(self::$allowViewField)->andWhere(['level_id' => $levelId])->asArray()->all();
    }

    /**
     * @param $data
     * @return int|string
     */
    public static function create($data)
    {
        try {
            \Yii::$app->db->createCommand()->insert(self::tableName(), $data)->execute();
        } catch (Exception $e) {
            return 0;
        }
        return \Yii::$app->db->getLastInsertID();
    }
}