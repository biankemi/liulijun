<?php

namespace micro\controllers;

use EasyWeChat\Kernel\Exceptions\DecryptException;
use micro\models\User;
use micro\services\JwtAuth;
use micro\services\Wechat;
use Yii;
use yii\base\InvalidConfigException;


class UserController extends  CommonController {


    public function actionSession()
    {
        $code = $this->request->get('code');
        $wechatService = new Wechat();
        $sessionInfo = $wechatService->getSession($code);
        if($sessionInfo['errcode'] > 0){
            return $this->fail($sessionInfo['errmsg']);
        }
        $info = User::getInfoByOpenId($sessionInfo['openid']);
        if(empty($info)){
            Yii::$app->cache->set($sessionInfo['openid'],$sessionInfo['session_key']);
            return $this->fail("请先注册");
        }
        if ($info['is_deny'] == 1){
            return $this->fail("账号异常");
        }
        $jwtAuth = new JwtAuth();
        $token = $jwtAuth->encode($info['id']);
        return $this->success(['token'=>$token]);
    }

    /**
     * 登录
     * @return array|\yii\db\ActiveRecord|null
     */
    public function actionLogin()
    {
        $params = $this->request->post();

        if(empty($params['iv']) || empty($params['encrypted_data'] || empty($params['open_id']))){
            return $this->fail("缺少请求参数");
        }

        $wechatService = new Wechat();
        $openId = $params['open_id'];
        // 获取session_key
        $sessionKey = Yii::$app->cache->get($openId);
        if (empty($sessionKey)){
            return $this->fail("请重新授权",-2);
        }

        try {
            $dInfo = $wechatService->decrypt($sessionKey, $params['iv'], $params['encrypted_data']);
            // 写入用户表
            $userModel = new User();
            $userModel->avatar = $dInfo['avatar'];
            $userModel->nickname = $dInfo['nickName'];
            $userModel->open_id = $openId;
            $userModel->save();

            $id = Yii::$app->db->getLastInsertID();
            $jwtAuth = new JwtAuth();
            $token = $jwtAuth->encode($id);

            return $this->success(['token'=>$token]);
        } catch (DecryptException $e) {
            return $this->fail($e->getMessage());
        }
    }


    /**
     * 申请合作
     * @return int[]|\int[][]|mixed[]|object|string[]|\string[][]
     * @throws \yii\base\InvalidConfigException
     */
    public function actionJoin()
    {
        $params = $this->request->post();

        if(empty($params['tel']) || empty($params['id_card_positive'] || empty($params['id_card_reverse']))){
            return $this->fail("缺少请求参数");
        }

        $model = User::findOne($this->userId);
        $model->tel = $params['tel'];
        $model->id_card_positive = $params['id_card_positive'];
        $model->id_card_reverse = $params['id_card_reverse'];
        if($model->save()){
            return $this->success([]);
        }
        return $this->fail("提交失败，请重试");
    }

    /**
     * 用户信息查询
     * @return int[]|\int[][]|mixed[]|object|string[]|\string[][]
     * @throws InvalidConfigException
     */
    public function actionInfo()
    {
        $info = User::getInfo($this->userId);
        try {
            return $this->success($info);
        } catch (InvalidConfigException $e) {
            return $this->fail("查询失败");
        }
    }

    /**
     * 推广码
     */
   public function actionPromoteCode()
   {
        $wechatService = new Wechat();
        $path = $this->request->get("path","pages/user/index");
        $sense = 'promote_id='.$this->userId;
        $imgFile = 'uploads/'.Yii::$app->getSecurity()->generateRandomString().'.png';
        $res = $wechatService->qrCode($path,$sense,$imgFile);
        if(empty($res)){
            return $this->fail("生成失败");
        }
       return $this->success(['file'=>Yii::$app->request->hostInfo.'/'.$imgFile]);
   }
}