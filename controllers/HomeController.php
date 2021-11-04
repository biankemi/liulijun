<?php

namespace micro\controllers;


class HomeController extends CommonController
{
    public function actionIndex()
    {
        return 'Hello home!';
    }

    public function actionTest()
    {
        return "test";
    }
}
