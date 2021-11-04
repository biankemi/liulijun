<?php
namespace micro\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class UserLevel extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{user_level}}';
    }

    private static $allowViewField = ["id","name","child_id","status","min_num","create_time"];



    /**
     * @return array
     */
   public static function getMap(): array
   {
       $list = self::find()->select(['id','name'])->column();
       return ArrayHelper::map($list,"id","name");
   }
}