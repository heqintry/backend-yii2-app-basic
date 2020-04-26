<?php

use yii\db\Migration;

/**
 * Class m200426_212008_create_test_data
 */
class m200426_212008_create_test_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $authManager = Yii::$app->authManager;
        if (!$authManager instanceof \yii\rbac\DbManager) {
            throw new \yii\base\InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        //user
        $this->batchInsert('user', ['id', 'username', 'password_hash', 'password_reset_token', 'email', 'status'], [
            [1, 'admin', Yii::$app->security->generatePasswordHash('password'), null, 'admin@email.com', 10],
            [2, 'user1', Yii::$app->security->generatePasswordHash('password'), null, 'user1@email.com', 10],
            [3, 'user2', Yii::$app->security->generatePasswordHash('password'), null, 'user2@email.com', 10],
        ]);

        //article
        $this->createTable('article', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'content' => $this->string(255)->notNull(),
            'author' => $this->string()->notNull(),
            'status' => $this->smallInteger()
        ], $tableOptions);
        $this->batchInsert('article', ['title', 'content', 'author', 'status'], [
            ['admin title 1', 'admin content 1', 'admin', 1],
            ['admin title 2', 'admin content 2', 'admin', 0],
            ['user1 title 1', 'user1 content 1', 'user1', 1],
            ['user1 title 2', 'user1 content 2', 'user1', 0],
            ['user1 title 3', 'user1 content 3', 'user1', 1],
            ['user1 title 4', 'user1 content 4', 'user1', 0],
            ['user2 title 1', 'user2 content 1', 'user2', 1],
            ['user2 title 2', 'user2 content 2', 'user2', 0],
            ['user2 title 3', 'user2 content 3', 'user2', 1],
            ['user2 title 4', 'user2 content 4', 'user2', 0],
        ]);

        //route
        $model = new \hqt\admin\models\Route();
        $model->addNew([
            '/admin/*',
            '/user/*',
            '/article/index',
            '/article/update',
            '/article/delete',
            '/article/publish',
            '/article/draft',
        ]);

        //rule
        $model = new \hqt\admin\models\BizRule(null);
        $model->name = 'ArticleEditRule';
        $model->className = 'app\models\ArticleEditRule';
        $model->save();

        $model = new \hqt\admin\models\BizRule(null);
        $model->name = 'ArticlePublishRule';
        $model->className = 'app\models\ArticlePublishRule';
        $model->save();

        $model = new \hqt\admin\models\BizRule(null);
        $model->name = 'IpRule';
        $model->className = 'app\models\IpRule';
        $model->save();

        //permission
        $model = new \hqt\admin\models\AuthItem(null);
        $model->type = \yii\rbac\Item::TYPE_PERMISSION;
        $model->name = 'ArticleIndexPermission';
        $model->description = 'route[index]';
        $model->ruleName = '';
        $model->save();
        $model->addChildren(['/article/index']);

        $model = new \hqt\admin\models\AuthItem(null);
        $model->type = \yii\rbac\Item::TYPE_PERMISSION;
        $model->name = 'ArticleEditPermission';
        $model->description = 'route[edit,delete]';
        $model->ruleName = 'ArticleEditRule';
        $model->save();
        $model->addChildren(['/article/update', '/article/delete']);

        $model = new \hqt\admin\models\AuthItem(null);
        $model->type = \yii\rbac\Item::TYPE_PERMISSION;
        $model->name = 'ArticlePublishPermission';
        $model->description = 'permission[ArticleEditPermission], route[publish,draft]';
        $model->ruleName = 'ArticlePublishRule';
        $model->save();
        $model->addChildren(['ArticleEditPermission', '/article/update', '/article/delete']);

        //role
        $model = new \hqt\admin\models\AuthItem(null);
        $model->type = \yii\rbac\Item::TYPE_ROLE;
        $model->name = 'AdminRole';
        $model->description = 'permission[ArticlePublishPermission], route[admin/*]';
        $model->ruleName = 'IpRule';
        $model->save();
        $model->addChildren(['ArticlePublishPermission', '/admin/*', '/user/*']);

        $model = new \hqt\admin\models\AuthItem(null);
        $model->type = \yii\rbac\Item::TYPE_ROLE;
        $model->name = 'UserRole';
        $model->description = 'permission[ArticleEditPermission]';
        $model->ruleName = 'IpRule';
        $model->save();
        $model->addChildren(['ArticleEditPermission', '/user/*']);

        //assign
        $model = new \hqt\admin\models\Assignment(1);
        $model->assign(['AdminRole', 'ArticleIndexPermission']);

        $model = new \hqt\admin\models\Assignment(2);
        $model->assign(['UserRole', 'ArticleIndexPermission']);

        $model = new \hqt\admin\models\Assignment(3);
        $model->assign(['UserRole', 'ArticleIndexPermission']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200426_212008_create_test_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200426_032008_create_test_data cannot be reverted.\n";

        return false;
    }
    */
}
