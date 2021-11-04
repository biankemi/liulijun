<?php

namespace micro\controllers;

use micro\services\JwtAuth;
use yii\web\Controller;
use Yii;

class CommonController extends Controller{

    protected $userId = 0;

    private $except = [
        'user/login',
        'user/get-session',
        'user/test',
        'order/notify'
    ];

    public function init()
    {
        parent::init();
        $action = Yii::$app->requestedRoute;
        if(!in_array($action,$this->except)){
            $headers = Yii::$app->request->headers;
            if ($headers->has('authorization')) {
                $besic = $headers->get('authorization');
                $token = trim(substr($besic,6));
                $jwtAuth = new JwtAuth();
                $decoded = $jwtAuth->decode($token);

                $this->userId = $decoded->uid;
            }

//            if(empty($this->userId)){
//                exit(json_encode(['code'=>-2,'msg'=>"请先登录"]));
//            }
        }
    }

    /**
     * @param $data
     * @return int[]|object|string[]|\string[][]
     * @throws \yii\base\InvalidConfigException
     */
    protected function success($data)
    {
        return \Yii::createObject([
            'class' => 'yii\web\Response',
            'format' => \yii\web\Response::FORMAT_JSON,
            'data' => [
                "result"=>$data,
                'message' => "success",
                'code' => 200,
            ],
        ]);
    }

    /**
     * @param $msg
     * @param int $code
     * @return \int[][]|mixed[]|object|string[]
     * @throws \yii\base\InvalidConfigException
     */
    protected function fail($msg,$code = -1)
    {
        return \Yii::createObject([
            'class' => 'yii\web\Response',
            'format' => \yii\web\Response::FORMAT_JSON,
            'data' => [
                'message' => $msg,
                'code' => $code,
                'result'=>[],
            ],
        ]);
    }
}