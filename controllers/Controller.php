<?php
namespace app\controllers;

use Yii;
use app\models\User;

class Controller extends \hqt\admin\components\Controller
{
    public function init()
    {
        parent::init();

        $x_token = Yii::$app->request->getHeaders()->get('X-Token');
        if ($x_token) {
            $userModel = User::findOne(['auth_key' => $x_token, 'status' => User::STATUS_ACTIVE]);
            if ($userModel) {
                Yii::$app->user->login($userModel);
            }
        }
    }
}