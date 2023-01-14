<?php

/** @var yii\web\View $this */

use app\models\Posts;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
?>
<h1>posts/index</h1>


<div class="posts-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [

            'id',
            [
                'attribute' => 'content',
                'value' => function ($model) {
                    return Html::a(
                        $model->content,
                        ['view', 'id' => $model->id],
                    );
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return date('d-m-Y H:i:s', $model->created_at);
                },
                'format' => 'raw',
            ],

        ],

    ]);
    ?>



</div>