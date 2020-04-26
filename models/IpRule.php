<?php
namespace app\models;

use yii\rbac\Rule;

class IpRule extends Rule
{
    public function execute($user, $item, $params)
    {
        return true;
        //return \Yii::$app->request->userIP == 'xxx.xxx.xxx.xxx';
    }
}