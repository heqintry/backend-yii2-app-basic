<?php
namespace app\models;

use Yii;
use yii\rbac\Rule;

class ArticleEditRule extends Rule
{
    public function execute($user, $item, $params)
    {
        $id = isset($params['id']) ? $params['id'] : null;
        if (!$id) {
            return false;
        }

        $model = Article::findOne($id);
        if (!$model) {
            return false;
        }

        $username = Yii::$app->user->identity->username;
        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id);
        $roles = array_column($roles, 'name');

        if (in_array('AdminRole', $roles) || $username == $model->author) {
            return true;
        }

        return false;
    }
}