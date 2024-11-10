<?php declare(strict_types=1);
if (!defined('MW_PATH')) {
    exit('No direct script access allowed');
}

/**
 * ListFieldValue
 *
 * @package MailWizz EMA
 * @author MailWizz Development Team <support@mailwizz.com>
 * @link https://www.mailwizz.com/
 * @copyright MailWizz EMA (https://www.mailwizz.com)
 * @license https://www.mailwizz.com/license/
 * @since 1.0
 */

/**
 * This is the model class for table "list_field_value".
 *
 * The followings are the available columns in table 'list_field_value':
 * @property integer $value_id
 * @property integer $field_id
 * @property integer $subscriber_id
 * @property string $value
 * @property string|CDbExpression $date_added
 * @property string|CDbExpression $last_updated
 *
 * The followings are the available model relations:
 * @property ListField $field
 * @property ListSubscriber $subscriber
 */
class ListFieldValue extends ActiveRecord
{
    /**
     * @return string
     */
    public function tableName()
    {
        return '{{list_field_value}}';
    }

    /**
     * @return array
     */
    public function rules()
    {
        /**
         * @since 2.5.6
         * Before hooking into this filter, you need to make sure the database table column can fit the new size.
         * This means the table must be altered before.
         */
        $maxLength = (int) hooks()->applyFilters('listfieldvalue_model_value_max_length', 255);

        $rules = [
            ['value', 'length', 'max' => $maxLength],
        ];

        return CMap::mergeArray($rules, parent::rules());
    }

    /**
     * @return array
     */
    public function relations()
    {
        $relations = [
            'field'      => [self::BELONGS_TO, ListField::class, 'field_id'],
            'subscriber' => [self::BELONGS_TO, ListSubscriber::class, 'subscriber_id'],
        ];

        return CMap::mergeArray($relations, parent::relations());
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        $labels = [
            'value_id'      => t('list_fields', 'Value'),
            'field_id'      => t('list_fields', 'Field'),
            'subscriber_id' => t('list_fields', 'Subscriber'),
            'value'         => t('list_fields', 'Value'),
        ];

        return CMap::mergeArray($labels, parent::attributeLabels());
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     * @throws CException
     */
    public function search()
    {
        $criteria = new CDbCriteria();

        return new CActiveDataProvider(get_class($this), [
            'criteria'      => $criteria,
            'pagination'    => [
                'pageSize'  => $this->paginationOptions->getPageSize(),
                'pageVar'   => 'page',
            ],
            'sort'  => [
                'defaultOrder'  => [
                    'value_id'  => CSort::SORT_DESC,
                ],
            ],
        ]);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ListFieldValue the static model class
     */
    public static function model($className=self::class)
    {
        /** @var ListFieldValue $model */
        $model = parent::model($className);

        return $model;
    }

    /**
     * @return array
     */
    public function attributeHelpTexts()
    {
        $texts = [
            'value' => null,
        ];

        return CMap::mergeArray($texts, parent::attributeHelpTexts());
    }

    /**
     * @return void
     */
    protected function afterSave()
    {
        parent::afterSave();

        // since 1.3.6.2 - this forces cache refresh
        if (
            (defined('MW_PERF_LVL') && MW_PERF_LVL) &&
            defined('MW_PERF_LVL_ENABLE_SUBSCRIBER_FIELD_CACHE') &&
            MW_PERF_LVL & MW_PERF_LVL_ENABLE_SUBSCRIBER_FIELD_CACHE
        ) {
            $this->subscriber->getAllCustomFieldsWithValues(true);
        }
    }
}
