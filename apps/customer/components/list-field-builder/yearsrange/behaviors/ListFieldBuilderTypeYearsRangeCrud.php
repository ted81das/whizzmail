<?php declare(strict_types=1);
if (!defined('MW_PATH')) {
    exit('No direct script access allowed');
}

/**
 * ListFieldBuilderTypeYearsRangeCrud
 *
 * The save action is running inside an active transaction.
 * For fatal errors, an exception must be thrown, otherwise the errors array must be populated.
 * If an exception is thrown, or the errors array is populated, the transaction is rolled back.
 *
 * @package MailWizz EMA
 * @author MailWizz Development Team <support@mailwizz.com>
 * @link https://www.mailwizz.com/
 * @copyright MailWizz EMA (https://www.mailwizz.com)
 * @license https://www.mailwizz.com/license/
 * @since 1.7.0
 */

/**
 * @property ListFieldBuilderTypeYearsRange $owner
 */
class ListFieldBuilderTypeYearsRangeCrud extends ListFieldBuilderTypeCrud
{
    /**
     * @param CEvent $event
     *
     * @return void
     * @throws Exception
     */
    public function _saveFields(CEvent $event)
    {
        $fieldType = $this->owner->getFieldType();
        $list      = $this->owner->getList();
        $typeName  = $fieldType->identifier;

        if (!isset($event->params['fields'][$typeName]) || !is_array($event->params['fields'][$typeName])) {
            $event->params['fields'][$typeName] = [];
        }

        /** @var array $postModels */
        $postModels = (array)request()->getPost('ListField', []);

        if (!isset($postModels[$typeName]) || !is_array($postModels[$typeName])) {
            $postModels[$typeName] = [];
        }

        /** @var ListField[] $models */
        $models = [];

        foreach ($postModels[$typeName] as $attributes) {
            /** @var ListField|null $model */
            $model = null;

            if (!empty($attributes['field_id'])) {
                /** @var ListField $model */
                $model = ListField::model()->findByAttributes([
                    'field_id'  => (int)$attributes['field_id'],
                    'type_id'   => (int)$fieldType->type_id,
                    'list_id'   => (int)$list->list_id,
                ]);
            }

            if (isset($attributes['field_id'])) {
                unset($attributes['field_id']);
            }

            if (empty($model)) {
                $model = new ListField();
            }

            $this->prepareListFieldModel($model);

            $model->attributes = $attributes;
            $model->type_id = (int)$fieldType->type_id;
            $model->list_id = (int)$list->list_id;

            $models[] = $model;
        }

        /** @var int[] $modelsToKeep */
        $modelsToKeep = [];
        foreach ($models as $model) {
            if (!$model->save()) {
                $this->owner->errors[] = [
                    'show'      => false,
                    'message'   => $model->shortErrors->getAllAsString(),
                    'errors'    => $model->shortErrors->getAll(),
                ];
            } else {
                $modelsToKeep[] = (int)$model->field_id;
            }
        }

        if (empty($this->owner->errors)) {
            $criteria = new CDbCriteria();
            $criteria->compare('list_id', $list->list_id);
            $criteria->compare('type_id', $fieldType->type_id);
            $criteria->addNotInCondition('field_id', $modelsToKeep);
            ListField::model()->deleteAll($criteria);
        }

        $fields = [];
        foreach ($models as $model) {
            $fields[] = $this->buildFieldArray($model);
        }

        $event->params['fields'][$typeName] = $fields;
    }

    /**
     * @param CEvent $event
     *
     * @return void
     * @throws CException
     */
    public function _displayFields(CEvent $event)
    {
        $fieldType = $this->owner->getFieldType();
        $list      = $this->owner->getList();
        $typeName  = $fieldType->identifier;

        /** register the add button. */
        hooks()->addAction('customer_controller_list_fields_render_buttons', [$this, '_renderAddButton']);

        /** register the javascript template. */
        hooks()->addAction('customer_controller_list_fields_after_form', [$this, '_registerJavascriptTemplate']);

        /** register the assets. */
        $assetsUrl = assetManager()->publish((string)realpath(__DIR__ . '/../assets/'), false, -1, MW_DEBUG);

        /** push the file into the queue. */
        clientScript()->registerScriptFile($assetsUrl . '/field.js');

        /** fields created in the save action. */
        if (isset($event->params['fields'][$typeName]) && is_array($event->params['fields'][$typeName])) {
            return;
        }

        if (!isset($event->params['fields'][$typeName]) || !is_array($event->params['fields'][$typeName])) {
            $event->params['fields'][$typeName] = [];
        }

        /** @var ListField[] $models */
        $models = ListField::model()->findAllByAttributes([
            'type_id' => (int)$fieldType->type_id,
            'list_id' => (int)$list->list_id,
        ]);

        $fields = [];
        foreach ($models as $model) {
            $this->prepareListFieldModel($model);
            $fields[] = $this->buildFieldArray($model);
        }

        $event->params['fields'][$typeName] = $fields;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function _registerJavascriptTemplate()
    {
        $model = new ListField();
        $this->prepareListFieldModel($model);

        $fieldType = $this->owner->getFieldType();
        $list      = $this->owner->getList();

        // since 1.9.29
        $model->list_id    = (int)$list->list_id;
        $model->visibility = (string)$list->customer->getGroupOption('lists.custom_fields_default_visibility', ListField::VISIBILITY_VISIBLE);

        /** default view file */
        $viewFile = realpath(__DIR__ . '/../views/field-tpl-js.php');

        /** and render */
        $this->owner->renderInternal($viewFile, compact('model', 'fieldType', 'list'));
    }

    /**
     * @param CEvent $event
     *
     * @return void
     */
    public function _addReadOnlyAttributes(CEvent $event)
    {
    }

    /**
     * @param CModelEvent $event
     *
     * @return void
     */
    public function _prepareListFieldModelSetRules(CModelEvent $event)
    {
        /** @var CList $rules */
        $rules = $event->params['rules'];

        $rules->add(['yearStart, yearEnd', 'length', 'is' => 4]);
        $rules->add(['yearStart, yearEnd', 'numerical', 'integerOnly' => true, 'min' => $event->sender->yearMin, 'max' => $event->sender->yearMax]);
        $rules->add(['yearStart', 'compare', 'compareAttribute' => 'yearEnd', 'operator' => '<']);
        $rules->add(['yearEnd', 'compare', 'compareAttribute' => 'yearStart', 'operator' => '>']);
        $rules->add(['yearStep', 'numerical', 'integerOnly' => true, 'min' => 1, 'max' => 100]);
    }

    /**
     * @param CModelEvent $event
     *
     * @return void
     * @throws CException
     */
    public function _prepareListFieldModelSetLabels(CModelEvent $event)
    {
        /** @var CMap $labels */
        $labels = $event->params['labels'];

        $labels->add('yearStart', t('list_fields', 'Year start'));
        $labels->add('yearEnd', t('list_fields', 'Year end'));
        $labels->add('yearStep', t('list_fields', 'Year step'));
    }

    /**
     * @param CModelEvent $event
     *
     * @return void
     * @throws CException
     */
    public function _prepareListFieldModelSetHelpTexts(CModelEvent $event)
    {
        /** @var CMap $texts */
        $texts = $event->params['texts'];

        $texts->add('yearStart', t('list_fields', 'Decides with which year to start'));
        $texts->add('yearEnd', t('list_fields', 'Decides with which year to end'));
        $texts->add('yearStep', t('list_fields', 'Decides the number of years it jump each iteration'));
    }

    /**
     * @param ListField $model
     *
     * @return array
     * @throws Exception
     */
    protected function buildFieldArray(ListField $model): array
    {
        $fieldType = $this->owner->getFieldType();
        $list      = $this->owner->getList();

        /** so that it increments properly! */
        $index = $this->owner->getIndex();

        $viewFile = realpath(__DIR__ . '/../views/field-tpl.php');
        $model->fieldDecorator->onHtmlOptionsSetup = [$this->owner, '_addInputErrorClass'];
        $model->fieldDecorator->onHtmlOptionsSetup = [$this, '_addReadOnlyAttributes'];

        return (array)hooks()->applyFilters('list_field_builder_type_yearsrange_crud_build_field_array', [
            'field'      => $model,
            'sort_order' => (int)$model->sort_order,
            'field_html' => $this->owner->renderInternal($viewFile, compact('model', 'index', 'fieldType', 'list'), true),
        ], $model);
    }

    /**
     * @param ListField $model
     *
     * @return void
     */
    protected function prepareListFieldModel(ListField $model)
    {
        $model->attachBehavior('_ListFieldBuilderTypeYearsRangeModelSettersGetters', [
            'class' => 'customer.components.list-field-builder.yearsrange.behaviors.ListFieldBuilderTypeYearsRangeModelSettersGetters',
        ]);

        $model->onRules                 = [$this, '_prepareListFieldModelSetRules'];
        $model->onAttributeLabels       = [$this, '_prepareListFieldModelSetLabels'];
        $model->onAttributeHelpTexts    = [$this, '_prepareListFieldModelSetHelpTexts'];
    }
}
