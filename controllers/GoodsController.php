<?php
namespace micro\controllers;

use micro\models\Goods;
use micro\models\GoodsImg;
use micro\models\GoodsSpec;
use micro\models\User;

class GoodsController extends CommonController{

    public function actionList()
    {
        $list = Goods::getList();
        if (empty($list)){
            return $this->success([]);
        }
        $goodsIdColumn = array_column($list,"id");
        $goodsSpec = GoodsSpec::getList($goodsIdColumn);
        $goodsImg = GoodsImg::getList($goodsIdColumn);
        foreach ($list as &$goods){
            if (array_key_exists($goods['id'],$goodsSpec)){
                $goods['spec'][] = $goodsSpec[$goods['goods_id']];
            }
            if (array_key_exists($goods['id'],$goodsImg)){
                $goods['img'][] = $goodsImg[$goods['goods_id']];
            }
        }
        return $this->success($list);
    }

    public function actionDetail()
    {
        $goodsId = $this->request->get("goods_id");
        if (empty($goodsId)){
            return $this->fail("访问页面不存在");
        }
        $info = Goods::getDetail($goodsId);
        if (empty($info)){
            return $this->fail("商品不存在!");
        }
        return $this->success($info);
    }

}