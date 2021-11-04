<?php

namespace micro\controllers;

use EasyWeChat\Kernel\Exceptions\Exception;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use GuzzleHttp\Exception\GuzzleException;
use micro\models\Order;
use micro\models\OrderGoods;
use micro\models\User;
use micro\services\WechatPay;

class OrderController extends CommonController
{

    public function actionAdd()
    {

        $post = $this->request->post();
        $orderInsData = [
            'user_id' => $this->userId,
            'order_amount' => $post['order_amount'],
        ];
        $orderId = Order::create($orderInsData);
        if (empty($orderId)) {
            return $this->fail("创建订单失败");
        }
        $orderGoodsInsData = [
            "order_id" => $orderId,
            "goods_id" => $post['goods_id'],
            "goods_spec_id" => $post['goods_spec_id'],
            "goods_name" => $post['goods_name'],
            "good_num" => $post['good_num'],
            "final_price" => $post['final_price'],
        ];
        $insRes = OrderGoods::create($orderGoodsInsData);
        if (empty($insRes)) {
            return $this->fail("写入订单失败");
        }
        return $this->success("创建成功");
    }

    public function actionPay()
    {
        $orderId = $this->request->get("order_id");
        $orderInfo = Order::getDetail($orderId);
        if (empty($orderInfo)) {
            return $this->fail("订单信息不存在");
        }
        $userInfo = User::getInfo($this->userId);
        $payData = [
            "out_trade_no" => $orderInfo['out_trade_no'],
            "total_fee" => $orderInfo['order_amount'],
            "openid" => $userInfo['open_id'],
            "body" => "商品",
            "notify_url"=>\Yii::$app->request->hostInfo."/order/notify",
        ];
        $payService = new WechatPay();
        try {
            $result = $payService->Pay($payData);
            if ($result['return_code'] !== 'SUCCESS') {
                return $this->fail($result["return_msg"]);
            }
            if ($result['result_code'] != 'SUCCESS') {
                return $this->fail($result["return_msg"]);
            }
            $config = $payService->app->jssdk->sdkConfig($result['prepay_id']);
            return $this->success(
                ['jssdk' => $payService->app->jssdk, // $app通过上面的获取实例来获取
                'config' => $config
                ]
            );
        } catch (InvalidArgumentException $e) {
            return $this->fail($e->getMessage());
        } catch (InvalidConfigException $e) {
            return $this->fail($e->getMessage());
        } catch (GuzzleException $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function actionNotify()
    {
        $message = $this->request->rawBody;
        $payService = new WechatPay();
        $response = $payService->app->handlePaidNotify(function ($message, $fail) {
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $orderInfo = Order::getDetailBySn($message['out_trade_no']);
            // 如果订单不存在 或者 订单已经支付过了
            if (empty($orderInfo) || $orderInfo["pay_status"] == 1) {
                return true;// 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                $orderUptData["pay_res"] = json_encode($message);
                if (array_get($message, 'result_code') === 'SUCCESS') {
                    $orderUptData['pay_time'] = time(); // 更新支付时间为当前时间
                    $orderUptData['pay_status'] = 1;
                    // 用户支付失败
                } elseif (array_get($message, 'result_code') === 'FAIL') {
                    $orderUptData['pay_status'] = 2;
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            Order::edit($orderInfo['id'], $orderUptData); // 保存订单

            return true; // 返回处理完成
        });

        $response->send(); // return $response;
    }

    public function actionUserOrderList()
    {
        $list = Order::getMyOrders($this->userId);
        return $this->success($list);
    }

    public function actionOrderList()
    {
        $list = Order::getList();
        return $this->success($list);
    }

    public function actionGetDetail()
    {
        $orderId = $this->request->get("order_id",0);
        $detail  = Order::getDetail($orderId);
        return $this->success($detail);
    }
}