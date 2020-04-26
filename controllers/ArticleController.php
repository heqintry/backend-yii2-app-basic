<?php

namespace app\controllers;

use app\models\Article;
use yii\web\NotFoundHttpException;

class ArticleController extends Controller
{
    public function actionIndex()
    {
        $articles = Article::find()->asArray()->all();
        return ['code' => 20000, 'data' => [
            'articles' => $articles,
        ]];
    }

    public function actionUpdate($id)
    {
        $article = $this->findModel($id);
        $article->title = \Yii::$app->request->post('title');
        $article->content = \Yii::$app->request->post('content');
        if ($article->save()) {
            return ['code' => 20000];
        } else {
            return ['code' => -1, 'message' => $article->getFirstErrors()];
        }
    }

    public function actionDelete($id)
    {
        $article = $this->findModel($id);
        $article->status = -1;
        if ($article->save()) {
            return ['code' => 20000];
        } else {
            return ['code' => -1, 'message' => $article->getFirstErrors()];
        }
    }

    public function actionPublish($id)
    {
        $article = $this->findModel($id);
        $article->status = 1;
        if ($article->save()) {
            return ['code' => 20000];
        } else {
            return ['code' => -1, 'message' => $article->getFirstErrors()];
        }
    }

    public function actionDraft($id)
    {
        $article = $this->findModel($id);
        $article->status = 0;
        if ($article->save()) {
            return ['code' => 20000];
        } else {
            return ['code' => -1, 'message' => $article->getFirstErrors()];
        }
    }

    /**
     * @param $id
     * @return null|Article
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = Article::findOne($id);
        if ($model) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}