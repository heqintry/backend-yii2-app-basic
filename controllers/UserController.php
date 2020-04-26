<?php

namespace app\controllers;

use app\models\User;
use hqt\admin\components\Helper;

class UserController extends Controller
{
    public function actionLogin()
    {
        $username = \Yii::$app->request->post('username');
        $password = \Yii::$app->request->post('password');

        /* @var User $user */
        $user = User::findOne(['username' => $username]);
        if (empty($user)) {
            return ['code' => -1, 'message' => 'username do not exists'];
        }

        if (!$user->validatePassword($password)) {
            return ['code' => -1, 'message' => 'password do not match'];
        }

        $user->generateAuthKey();
        $user->save();

        return ['code' => 20000, 'data' => [
            'token' => $user->auth_key,
        ]];
    }

    public function actionInfo()
    {
        $routeApis = \Yii::$app->request->post('routeApis', []);
        foreach ($routeApis as $index => $route) {
            if (!Helper::checkRoute($route)) {
                unset($routeApis[$index]);
            }
        }

        $roles = \Yii::$app->authManager->getRolesByUser(\Yii::$app->user->id);
        $roles = array_column($roles, 'name');

        return ['code' => 20000, 'data' => [
            'name' => \Yii::$app->user->identity->username,
            'avatar' => 'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif',
            'roles' => $roles,
            'routeApis' => array_values($routeApis),
        ]];
    }

    public function actionLogout()
    {
        /* @var User $userModel */
        $userModel = \Yii::$app->user->identity;
        $userModel->auth_key = '';
        $userModel->save();

        return ['code' => 20000];
    }
}