<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "interactive".
 *
 * @property int $id
 * @property string $object_type
 * @property int $object_id
 * @property int $is_like
 * @property int $rating
 * @property string $comment
 * @property string $image_review
 * @property int $created_by
 * @property string $created_at
 */
class Interactive extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'interactive';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['object_id', 'is_like', 'rating', 'created_by'], 'default', 'value' => null],
            [['object_id', 'is_like', 'rating', 'created_by'], 'integer'],
            [['comment'], 'string'],
            [['created_at'], 'safe'],
            [['object_type', 'image_review'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'object_type' => 'Object Type',
            'object_id' => 'Object ID',
            'is_like' => 'Is Like',
            'rating' => 'Rating',
            'comment' => 'Comment',
            'image_review' => 'Image Review',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
        ];
    }
}
