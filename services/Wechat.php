<?php
namespace micro\services;

use EasyWeChat\Factory;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;

// 微信操作
class Wechat{

    private array $config = [
        'app_id' => 'wx3cf0f39249eb0exx',
        'secret' => 'f1c242f4f28f735d4687abb469072axx',

        // 下面为可选项
        // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
        'response_type' => 'array',

        'log' => [
            'level' => 'debug',
            'file' => '@micro/wechat.log',
        ],
    ];

    public $app = NULL;

    public function __construct()
    {
        $this->app = Factory::miniProgram($this->config);
    }

    /**
     * 小程序码
     */
    public function qrCode($path,$scene,$savePath)
    {
        try {
            $response = $this->app->app_code->getUnlimit($scene, [
                'page'  => $path,
                'width' => 600,
            ]);
        }catch (\HttpException $e){
            return "";
        }
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            try {
                return $response->save($savePath);
            } catch (InvalidArgumentException | RuntimeException $e) {
                return "";
            }
        }
        return "";
    }

    /**
     * 获取用户session 信息
     * @param $code
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getSession($code): array
    {
        return $this->app->auth->session($code);
    }


    /**
     * 解密
     * @param $session
     * @param $iv
     * @param $encryptedData
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     */
    public function decrypt($session,$iv,$encryptedData): array
    {
        return $this->app->encryptor->decryptData($session, $iv, $encryptedData);

    }
}