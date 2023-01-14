<?php

namespace app\controllers;

use app\models\Posts;
use app\models\PostsSearch;
use app\models\Comments;
use app\models\CommentsSearch;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;

class PostsController extends \yii\web\Controller
{

    public function actionIndex()
    {
        $searchModel = new PostsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        //$commentsCount = GoalsApis::find()->indexBy('id')->select('base_url')->column();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            //'commentsCount' => $commentsCount
        ]);
    }

    public function actionView($id)
    {
        $searchModel = new CommentsSearch();

        $commentsDataProvider = $searchModel->searchByPostsId($this->request->queryParams, $id);
        $models = $commentsDataProvider->getModels();
        $dataJson = $this->formatCommentModelsToJson($models);
        return $this->render('view', [
            'dataJson' => $dataJson,
            'model' => $this->findModel($id),
            'commentsModel' => $models,
            'commentsDataProvider' => $commentsDataProvider,
        ]);
    }



    protected function findModel($id)
    {
        if (($model = Posts::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

        
    /**
     * actionGetNewJsonData
     * экшен возвращяет отсортированный одномерный массив с сохранением предыдущего порядка вложенности
     * @param  mixed $postId
     * @return void
     */
    public function actionGetNewJsonData($postId)
    {
        if ($this->request->isGet) {
            $searchModel = new CommentsSearch();
            $commentsDataProvider = $searchModel->searchByPostsId($this->request->queryParams, $postId);
            $models = $commentsDataProvider->getModels();
            $dataJson = $this->formatCommentModelsToJson($models);
            return $dataJson;
        }
    }


    /**
     * formatCommentModelsToJson
     * возвращает json объект на основе выборки моделей
     * @param  array $models
     * @return void
     */
    public function formatCommentModelsToJson(array $models)
    {
        $dataJson = ArrayHelper::buildParentChildTreeArray($models);
        $dataJson = array_map(fn ($model) => [
            'id' => $model->id,
            'parent_id' => $model->parent_id,
            'post_id' => $model->post_id,
            'user_id' => $model->user_id,
            'content' => $model->content,
            'level' => $model->level,
            'created_at' => $model->created_at,
            'current_user_id' => \Yii::$app->user->id,
            'deleted' => $model->deleted,
        ], $dataJson);

        return json_encode($dataJson, JSON_UNESCAPED_UNICODE);
    }
}
