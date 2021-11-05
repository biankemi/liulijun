<?php

namespace micro\controllers;


class SiteController extends CommonController
{

    public function actionIndexAaa()
    {
        return $this->fail("失败");
    }

    public function actionDo()
    {
        return $this->success("alalal");
    }
}
