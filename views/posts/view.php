<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\NestedCommentsListView;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Posts $model */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Пост ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Posts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="posts-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'content',
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return date('d-m-Y H:i:s', $model->created_at);
                },
                'format' => 'raw',
            ],
        ],
    ]) ?>

    <?php
    $id = Yii::$app->user->id;
    if ($id) { ?>
        <form class="submit-comment" id="main-comment" parent_id=0 post_id="<?= $model->id ?>">
            <div class="mb-3">
                <label for="mainCommentField" class="form-label">Comment</label>
                <input type="text" class="form-control" required="required" maxlength="280" id="mainCommentField">
                <div class="form-text">Ваш комментарий (макс 280)</div>
            </div>
            <button type="submit" class="btn btn-primary submit-btn">Submit</button>
        </form>
    <?php } ?>

    <div class="mb-1" id="comments_container">
        <?= $dataJson; ?>
    </div>


</div>

<?php
$script = <<< JS
   $(document).ready(function() {
        console.log('loaded');
        getAjaxComments();      
    });


    function rebuildCommentsHtml(data){
        $('#comments_container').empty();
        var collapseBranchId = '';
        var collapseBranches = [];
        $.each(data, function(index, element) {

            var markSpanO = "<span style=" + '"color:green"' + ">"; 
            var markSpanC = "</span>"; 
            var parentCheck = (element.parent_id  == '0') ? '' : ' к комментарию ' + markSpanO+  element.parent_id + markSpanC;
            var timestamp = element.created_at;
            var dateFormat = new Date(timestamp*1000);
            var currentTimestamp = Date.now() / 1000 ;
            var timeDiff = currentTimestamp - timestamp;
            var date = dateFormat.getDate()+
            "-"+(dateFormat.getMonth()+1)+
            "-"+dateFormat.getFullYear()+
            " "+dateFormat.getHours()+
            ":"+dateFormat.getMinutes()+
            ":"+dateFormat.getSeconds();

            var level = 1;
            var lastNestLevel = 4;
            var collapseClass = '';
            
            if(element.level == lastNestLevel  ){
                if (!collapseBranches.includes(element.parent_id)){
                    collapseBranches.push(element.parent_id);
                    
                    collapseBranchId = "collapse-branch-" + element.parent_id;
                    $('#comments_container').append(
                        $('<div>', {
                            addClass: 'text-justify ps-' + level * lastNestLevel + ' mt-3 float-right',
                        }).append(
                            $('<button>', {
                            html:  'скрыть/отобразить ветку',
                            addClass: 'btn btn-secondary btn-sm',                     
                            }).attr(
                                'data-bs-target', "#" + collapseBranchId
                            ).attr(
                                'data-bs-toggle', "collapse"
                            )
                        )
                    );
                }
            }
           
            if(element.level < lastNestLevel ){
                level= level * element.level;
                collapseBranchId = '';
                collapseClass = '';
            }else{
                collapseClass = ' collapse ';
                level = level * lastNestLevel;
            }
            
            var text = "-";
            var repeat = text.repeat(element.level);
            
            
            $('#comments_container').append(
                $('<div>', {
                addClass: 'text-justify ms-' + level + ' py-1 mt-3 float-right comment-body' + collapseClass,
                }).append(
                    $('<h6>', {
                        html: repeat + ' Комментарий: ' + markSpanO + element.id + markSpanC + ' от пользователя ' + 
                        element.user_id +  parentCheck                        
                    })
                ).append(
                    $('<span>', {
                        html:  date,                        
                    })
                ).append(
                    $('<p>', {
                        html:  element.content,                        
                    }).attr('class', (element.deleted ? 'p-deleted' : ''))
                    )
                    .append( 
                        (element.deleted == 0 && element.current_user_id == element.user_id ?                   
                        '<button style="cursor:pointer" class="create-reply-form btn btn-outline-primary btn-sm" parent_id="' + element.id
                        + '"' + 'post_id="'+ element.post_id +'" ' + 
                        ' id="' + element.id +'">Отетить</button> ' +
                            (timeDiff < 3600 ?
                                '<button style="cursor:pointer" class="update-reply-form btn btn-outline-primary btn-sm" element_id="' + element.id
                                + '"' +  ' id="' + element.id +'">Изменить</button> ' +
                                '<button style="cursor:pointer" class="delete-reply btn btn-outline-primary btn-sm" element_id="' + element.id
                                + '"' +  ' id="' + element.id +'">Удалить</button> '                                
                            :
                                ''
                            )    
                        :      
                            ''        
                        ) 
                )
                .attr('id', collapseBranchId)
                .attr('comment_id', element.id)

            )
        });
    }


    $(document).on('click',".create-reply-form",function(){

        var comment_id = $( this ).attr('id');
        var post_id = $( this ).attr('post_id');
        var content = '';
        var id = 'reply-comment';
        buildForm(comment_id, post_id, content, id);
    });

    $(document).on('click',".update-reply-form",function(){

        var comment_id = $( this ).attr('id');
        var post_id = $( this ).attr('post_id');
        var content = $('[comment_id = '+ comment_id +']>p').text();
        var id = 'update-comment';
        buildForm(comment_id, post_id, content, id);

    });

    
    $(document).on('click',".delete-reply",function(){

        var comment_id = $( this ).attr('id');
        var content = '';
        ajaxDelete(comment_id, content, 'delete');

    });

    function buildForm(comment_id, post_id, content, id){
        $("[comment_id="+comment_id+"]").append(
                '<form class="submit-comment" id="'+id+'" parent_id=' + comment_id + ' post_id=' + post_id +'>'
                    +'<div class="my-1">'
                    +'<input type="text" class="form-control" value="'+ content + '" required="required" maxlength="280" id="replyCommentField' + comment_id + '">'

                    +'</div>'+
                    '<button type="submit" class="btn btn-primary">Submit</button></form>'
            );

    }

    $(document).on('submit','#update-comment', function(e){
        e.preventDefault(); // avoid to execute the actual submit of the form.
        var form = $(this);
        var comment_id = form.attr('parent_id');        
        var post_id = form.attr('post_id');        
        var comment = $('#replyCommentField'+comment_id).val();
        ajaxUpdate(comment_id, comment, 'update');  

    });  

    $(document).on('submit','#reply-comment', function(e){
        e.preventDefault(); // avoid to execute the actual submit of the form.
        var form = $(this);
        var parent_id = form.attr('parent_id');
        
        var post_id = form.attr('post_id');
        
        var comment = $('#replyCommentField'+parent_id).val();
        ajaxCreate(parent_id, comment, post_id);  

    });  

    function getAjaxComments(){
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const post_id = urlParams.get('id')

        $.ajax({
            url: '/gjbeta/web/posts/get-new-json-data?postId='+post_id,
            type: 'GET',
            dataType: 'json',
                success: function(response) {
                    rebuildCommentsHtml(response);  
                    console.log(response);              
                },

                error: function(response) {
                    console.log('Failed...'.response);
                }
        });
    }
    
    $('.submit-comment').submit(function(e){


        e.preventDefault(); // avoid to execute the actual submit of the form.
        var form = $(this);            
        var parent_id = form.attr('parent_id');        
        var post_id = form.attr('post_id');        
        var comment = $('#mainCommentField').val();
        ajaxCreate(parent_id, comment, post_id);   
    });  

    function ajaxCreate(parent_id, content, post_id){
        $.ajax({
            url: '/gjbeta/web/comments/create',
            type: 'POST',
            data: {
                'parent_id': parent_id,
                'content': content,
                'post_id': post_id,
                },

                success: function(response) {
                    getAjaxComments();
                    // console.log(response);
                    alert('Отправлено');  
                },

                error: function(response) {
                    //console.log('Failed...'.response);
                    alert('Что-то пошло не так');  
                }
        });
    }
    function ajaxUpdate(comment_id, content, type='update'){
        $.ajax({
            url: '/gjbeta/web/comments/update?id='+comment_id + '&type='+type,
            type: 'POST',
            data: {
                'id': comment_id,
                'content': content,
                },

                success: function(response) {
                    getAjaxComments();
                    // console.log(response);
                    alert('Отправлено');  
                },

                error: function(response) {
                    //console.log('Failed...'.response);
                    alert('Что-то пошло не так');  
                }
        });
    }
    function ajaxDelete(comment_id, content, type){
        $.ajax({
            url: '/gjbeta/web/comments/update?id='+comment_id + '&type='+type,
            type: 'POST',
            data: {
                'id': comment_id,
                },

                success: function(response) {
                    getAjaxComments();
                    // console.log(response);
                    alert('Отправлено');  
                },

                error: function(response) {
                    //console.log('Failed...'.response);
                    alert('Что-то пошло не так');  
                }
        });
    }
JS;

$this->registerJs($script);
?>