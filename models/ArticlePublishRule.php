<?php
namespace app\models;

use yii\rbac\Rule;

class ArticlePublishRule extends Rule
{
    public function execute($user, $item, $params)
    {
        return true;
    }
}