<?php

namespace app\modules\cms\models;

use Yii;

/**
 * This is the model class for table "file_ref".
 *
 * @property int $id
 * @property int $file_id
 * @property int $object_id
 * @property string $object_type
 */
class FileRef extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file_ref';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file_id', 'object_id'], 'default', 'value' => null],
            [['file_id', 'object_id'], 'integer'],
            [['object_type'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file_id' => 'File ID',
            'object_id' => 'Object ID',
            'object_type' => 'Object Type',
        ];
    }
}
