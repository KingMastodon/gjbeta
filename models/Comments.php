<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comments".
 *
 * @property int $id
 * @property string $content
 * @property int $user_id
 * @property int $post_id
 * @property int $parent_id
 * @property int $created_at
 */
class Comments extends \yii\db\ActiveRecord
{
    public int $level;
    public array $children;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content', 'user_id', 'post_id', 'parent_id', 'created_at'], 'required'],
            [['user_id', 'post_id', 'parent_id', 'created_at'], 'integer'],
            [['deleted'], 'default', 'value' => 0],
            [['content'], 'string', 'max' => 280],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => 'Content',
            'user_id' => 'User ID',
            'post_id' => 'Post ID',
            'parent_id' => 'Parent ID',
            'created_at' => 'Created at',
            'deleted' => 'Deleted'
        ];
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    public function setCreatedAt(int $time): void
    {
        $this->created_at = $time;
    }

    public function setUserId(int $userId): void
    {
        $this->user_id = $userId;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function setDeleted(int $deleted): void
    {
        $this->deleted = $deleted;
    }
}
