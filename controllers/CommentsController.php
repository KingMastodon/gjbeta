<?php

namespace app\controllers;

use app\models\Comments;
use app\models\CommentsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CommentsController implements the CRUD actions for Comments model.
 */
class CommentsController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                        'create' => ['POST'],
                        'update' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Comments models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CommentsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Comments model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Comments model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Comments();

        if ($this->request->isPost) {
            $id = \Yii::$app->user->id;
            if ($id) {
                $model->setUserId($id);
                $model->setCreatedAt(time());
                $post = $this->request->post();

                if ($model->load($post, '')) {

                    if ($model->validate()) {
                        echo 'Validation retuned true';
                    }

                    if ($model->save()) {
                        return 'success';
                    } else {
                        var_dump($model->getErrors());
                    }
                }
            }

            return 'error';
        } else {
            $model->loadDefaultValues();
        }
    }


    /**
     * actionUpdate
     * Обновляет запись в бд, type для разделения на стандартное обновление
     * и запись псевдоудаления
     * @param  mixed $id
     * @param  mixed $type
     * @return void
     */
    public function actionUpdate(int $id, string $type = 'update')
    {
        $model = $this->findModel($id);

        if ($this->request->isPost) {
            $id = \Yii::$app->user->id;
            if ($id) {
                $model->setUserId($id);
                $model->setCreatedAt(time());
                $post = $this->request->post();

                if ($type == 'delete') {
                    $model->setContent('..deleted..');
                    $model->setDeleted(1);
                } else {
                    $model->setContent($post['content']);
                }

                if (!$model->validate()) {
                    return 'error';
                }

                if ($model->save()) {
                    return 'success';
                } else {
                    var_dump($model->getErrors());
                }
            } else {
                return 'error';
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }


    /**
     * Finds the Comments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Comments the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Comments::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
