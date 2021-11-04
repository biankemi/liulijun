<?php

namespace micro\models;

use yii\db\ActiveRecord;

class Goods extends ActiveRecord
{
    private static array $allowViewField = ["id", "name", "desc", "is_deleted", "create_time"];

    public static function tableName(): string
    {
        return '{{goods}}';
    }

    /**
     * @return array|ActiveRecord[]
     */
    public static function getList(): array
    {
        return self::find()->select(self::$allowViewField)->andWhere(['is_deleted'=>0])->asArray()->all();
    }

    /**
     * @param $goodsId
     * @return array
     */
    public static function getDetail($goodsId): array
    {
        return self::find()->select(self::$allowViewField)->where(['id'=>$goodsId])->andWhere(['is_deleted'=>0])->asArray()->all();
    }
}