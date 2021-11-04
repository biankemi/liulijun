<?php
namespace micro\services;

use EasyWeChat\Factory;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use GuzzleHttp\Exception\GuzzleException;
use micro\models\Order;


class WechatPay{

    private $config = [
        // 必要配置
        'app_id'             => 'xxxx',
        'mch_id'             => 'your-mch-id',
        'key'                => 'key-for-signature',   // API 密钥

        // 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
        'cert_path'          => 'path/to/your/cert.pem', // XXX: 绝对路径！！！！
        'key_path'           => 'path/to/your/key',      // XXX: 绝对路径！！！！

        'notify_url'         => '默认的订单回调地址',     // 你也可以在下单时单独设置来想覆盖它
    ];

    public \EasyWeChat\Payment\Application $app;

    public function __construct()
    {
        $this->app = Factory::payment($this->config);
    }


    /**
     * 统一支付
     * @param $data
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws GuzzleException
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public function Pay($data)
    {
        return $this->app->order->unify([
            'body' => $data['body'],
            'out_trade_no' => $data['out_trade_no'],
            'total_fee' => $data['total_fee'],
            'spbill_create_ip' => "127.0.0.1", // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
            'notify_url' => $data['notify_url'] ?? $this->config['notify_url'], // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            'openid' => $data['openid'],
        ]);
        // $result:
        //{
        //    "return_code": "SUCCESS",
        //    "return_msg": "OK",
        //    "appid": "wx2421b1c4390ec4sb",
        //    "mch_id": "10000100",
        //    "nonce_str": "IITRi8Iabbblz1J",
        //    "openid": "oUpF8uMuAJO_M2pxb1Q9zNjWeSs6o",
        //    "sign": "7921E432F65EB8ED0CE9755F0E86D72F2",
        //    "result_code": "SUCCESS",
        //    "prepay_id": "wx201411102639507cbf6ffd8b0779950874",
        //    "trade_type": "JSAPI"
        //}
    }


    /**
     * 根据商户订单号查询
     * @param $outTradeNo
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public function getOrderByTrade($outTradeNo)
    {
        return $this->app->order->queryByOutTradeNumber($outTradeNo);
    }


    /**
     *
     * 根据微信订单号查询
     * @param $transactionId
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public function getOrderByWx($transactionId)
    {
        return $this->app->order->queryByTransactionId($transactionId);
    }


    /**
     * 关闭订单
     * @param $outTradeNo
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws GuzzleException
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public function close($outTradeNo)
    {
        return $this->app->order->close($outTradeNo);
    }
}