<?php

namespace micro\models;

use yii\db\ActiveRecord;

class User extends ActiveRecord
{
    private static array $allowViewField = ["id", "nickname", "tel", "avatar", "real_name", "id_card_no", "referrer_user_id", "open_id", "level_id", "is_deny", "total_amount", "balance"];

    public static function tableName(): string
    {
        return '{{user}}';
    }

    public static function getInfoByOpenId($openId)
    {
        return User::find()->select(self::$allowViewField)->where(['open_id' => $openId])->one();
    }

    /**
     * 获取用户信息
     * @param $userId
     * @return array|ActiveRecord|null
     */
    public static function getInfo($userId)
    {
        return self::find()->select(self::$allowViewField)->where(['id' => $userId])->one();
    }

    public static function list($where): array
    {
        return self::find()->select(self::$allowViewField)->where($where)->asArray()->all();
    }

    public static function groupList($where): array
    {
        $fields = array_merge(self::$allowViewField, ["count(1) as num"]);
        return self::find()->select($fields)->where($where)->groupBy(["level_id"])->asArray()->all();
    }

    /**
     * 获取下级用户列表
     * @param $userId
     * @param $levelId
     * @return array
     */
    public static function getChildList($userId, $levelId): array
    {
        $sql = "select u.id,u.avatar,u.level_id,u.nickname,count(1) as child_count from user u left join user_relation r on u.id = r.user_id where find_in_set(:node,r.node) and level_id = :level_id and u.is_deny = 0 group by u.id";
        return self::findBySql($sql, [':node' => $userId, ':level_id' => $levelId])->asArray()->all();
    }
}