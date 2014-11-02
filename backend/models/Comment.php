<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%comment}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property integer $to_uid
 * @property integer $job_id
 * @property string $create_time
 * @property integer $createByUserId
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%comment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['to_uid', 'job_id', 'createByUserId'], 'integer'],
            [['create_time'], 'safe'],
            [['title', 'content'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'content' => Yii::t('app', 'Content'),
            'to_uid' => Yii::t('app', 'To Uid'),
            'job_id' => Yii::t('app', 'Job ID'),
            'create_time' => Yii::t('app', 'Create Time'),
            'createByUserId' => Yii::t('app', 'Create By User ID'),
        ];
    }
}
