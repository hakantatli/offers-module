<?php

namespace app\models;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Inflector;

/**
 * This is the model class for table "{{%casino}}".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property float $rating
 * @property int $is_active
 * @property string $created_at
 * @property string|null $updated_at
 */
class Casino extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%casino}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'slugAttribute' => 'slug',
                'ensureUnique' => true,
                'preserveNonEmptyValues' => true,
            ],
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Format and sanitize manually entered slug before validation.
     */
    public function beforeValidate()
    {
        if (!empty($this->slug)) {
            $this->slug = Inflector::slug($this->slug);
        }
        return parent::beforeValidate();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'rating'], 'required'],
            [['name', 'slug'], 'trim'],
            [['rating'], 'number', 'min' => 0.0, 'max' => 5.0],
            [['is_active'], 'boolean'],
            [['is_active'], 'default', 'value' => 1],
            [['name', 'slug'], 'string', 'max' => 255],
            [['slug'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Casino Name',
            'slug' => 'Slug (URL Key)',
            'rating' => 'Rating (0.0 - 5.0)',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
