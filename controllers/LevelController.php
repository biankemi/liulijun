<?php

namespace micro\controllers;

use micro\models\User;
use micro\models\UserLevel;
use micro\models\UserRelation;

class LevelController extends CommonController
{

    /**
     * 获取下级分组数量等级
     * @return int[]|object|string[]|\string[][]
     * @throws \yii\base\InvalidConfigException
     */
    public function actionGroupList()
    {
        // 获取用户node
        $userIdColumn = UserRelation::getUserIdColumnByParentId($this->userId);
        if (empty($userIdColumn)) {
            return $this->success([]);
        }
        $where = ['in', 'id', $userIdColumn];
        $list = User::groupList($where);
        if (empty($list)){
            return $this->success([]);
        }
        $levelMap = UserLevel::getMap();
        foreach ($list as &$info){
            $info['level_name'] =$levelMap[$info['level_id']];
        }
        return $this->success($list);
    }

    // 用户列表
    public function actionList()
    {
        // 获取用户node
        $levelId = $this->request->get("level_id");
        $list = User::getChildList($this->userId,$levelId);
        return $this->success($list);
    }
}
