<?php

namespace micro\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class GoodsImg extends ActiveRecord
{
    private static array $allowViewField = ["id", "goods_id", "img_path", "is_deleted"];

    public static function tableName(): string
    {
        return '{{goods_img}}';
    }

    /**
     * @return array|ActiveRecord[]
     */
    public static function getList($goodsIds): array
    {
        is_array($goodsIds) || $goodsIds = [$goodsIds];
        $list = self::find()->select(["id","goods_id","img_path"])->where(['in','goods_id',$goodsIds])->andWhere(['is_deleted'=>0])->all();
        if (empty($list)){
            return $list;
        }
        return ArrayHelper::index($list,"null","goods_id");
    }
}