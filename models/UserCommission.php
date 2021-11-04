<?php

namespace micro\models;

use yii\db\ActiveRecord;
use yii\db\Exception;

class UserCommission extends ActiveRecord
{
    private static array $allowViewField = ["id", "level_id", "superior_level_id", "superior_percent"];

    public static function tableName(): string
    {
        return '{{user_commission}}';
    }


    /**
     * @param $levelId
     * @return array
     */
    public static function getList($levelId): array
    {
        return self::find()->select(self::$allowViewField)->andWhere(['level_id'=>$levelId])->asArray()->all();
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

    /**
     * @param $id
     * @param $data
     * @return bool
     */
    public static function edit($id,$data): bool
    {
        try {
            \Yii::$app->db->createCommand()->update(self::tableName(), $data, 'id = ' . $id)->execute();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}