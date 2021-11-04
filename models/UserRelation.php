<?php
namespace micro\models;

use yii\db\ActiveRecord;
use yii\db\Expression;

class UserRelation extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{user_relation}}';
    }

    private static array $allowViewField = ["id","user_id","parent_id","node","create_time"];

    public static function getNode($userId): array
    {
        return self::find()
            ->select(['user_id'])
            ->where(new Expression('FIND_IN_SET(:node, node)'))
            ->addParams([':node' => $userId])
            ->column();
    }

    public static function getUserIdColumnByParentId($parentId): array
    {
        return self::find()->select(['user_id'])->where(['=','parent_id',$parentId])->column();
    }
}