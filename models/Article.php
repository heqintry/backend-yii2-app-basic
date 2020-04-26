<?php
namespace app\models;

use yii\db\ActiveRecord;

/**
 * Class Article
 * @package app\models
 *
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property string $author
 * @property integer $status
 */
class Article extends ActiveRecord
{

}