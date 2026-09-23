<?php

namespace app\models;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Inflector;

/**
 * This is the model class for table "{{%offer}}".
 *
 * @property int $id
 * @property int $casino_id
 * @property string $title
 * @property string $slug
 * @property string $type
 * @property float $amount
 * @property string $terms
 * @property string|null $expires_at
 * @property string $status
 * @property string $created_at
 * @property string|null $updated_at
 *
 * @property Casino $casino
 */
class Offer extends ActiveRecord
{
    const TYPE_WELCOME = 'welcome';
    const TYPE_NO_DEPOSIT = 'no_deposit';
    const TYPE_FREE_SPINS = 'free_spins';

    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%offer}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title',
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
     * Clean and slugify manually entered slug before validation.
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
            [['casino_id', 'title', 'type', 'amount', 'terms', 'status'], 'required'],
            [['casino_id'], 'integer'],
            [['amount'], 'number', 'min' => 0],
            [['terms'], 'string'],
            [['title', 'slug'], 'string', 'max' => 255],
            [['title', 'slug'], 'trim'],
            [['slug'], 'unique'],
            ['type', 'in', 'range' => array_keys(self::getTypes())],
            ['status', 'in', 'range' => array_keys(self::getStatuses())],
            [['expires_at'], 'safe'],
            ['expires_at', 'validateExpiresAt'],
            [
                ['casino_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Casino::class,
                'targetAttribute' => ['casino_id' => 'id'],
            ],
        ];
    }

    /**
     * Validates that expiration date is in the future.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateExpiresAt($attribute, $params)
    {
        if (!empty($this->$attribute)) {
            $timestamp = strtotime($this->$attribute);
            if ($timestamp === false) {
                $this->addError($attribute, 'Expiration date format is invalid.');
                return;
            }

            // Only enforce future date validation when creating new record or modifying expires_at
            if (($this->isNewRecord || $this->isAttributeChanged($attribute)) && $timestamp <= time()) {
                $this->addError($attribute, 'Expiration date must be in the future.');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'casino_id' => 'Casino Operator',
            'title' => 'Offer Title',
            'slug' => 'Slug (URL Key)',
            'type' => 'Offer Type',
            'amount' => 'Amount / Value (€)',
            'terms' => 'Terms & Conditions',
            'expires_at' => 'Expires At',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Casino]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCasino()
    {
        return $this->hasOne(Casino::class, ['id' => 'casino_id']);
    }

    /**
     * Returns key-value array of supported offer types.
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_WELCOME => 'Welcome Bonus',
            self::TYPE_NO_DEPOSIT => 'No Deposit Bonus',
            self::TYPE_FREE_SPINS => 'Free Spins',
        ];
    }

    /**
     * Returns key-value array of supported offer statuses.
     *
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_EXPIRED => 'Expired',
        ];
    }

    /**
     * Returns badge CSS class for offer type.
     *
     * @return string
     */
    public function getTypeBadgeClass()
    {
        return match ($this->type) {
            self::TYPE_WELCOME => 'bg-primary',
            self::TYPE_NO_DEPOSIT => 'bg-success',
            self::TYPE_FREE_SPINS => 'bg-info text-dark',
            default => 'bg-secondary',
        };
    }

    /**
     * Returns badge CSS class for offer status.
     *
     * @return string
     */
    public function getStatusBadgeClass()
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'bg-success',
            self::STATUS_DRAFT => 'bg-warning text-dark',
            self::STATUS_EXPIRED => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}
