<?php declare(strict_types=1);
if (!defined('MW_PATH')) {
    exit('No direct script access allowed');
}

/**
 * This file is part of the MailWizz EMA application.
 *
 * @package MailWizz EMA
 * @author MailWizz Development Team <support@mailwizz.com>
 * @link https://www.mailwizz.com/
 * @copyright MailWizz EMA (https://www.mailwizz.com)
 * @license https://www.mailwizz.com/license/
 * @since 1.3.5.9
 */

/** @var Controller $controller */
$controller = controller();

/** @var string $pageHeading */
$pageHeading = (string)$controller->getData('pageHeading');

/** @var CustomerMessage $message */
$message = $controller->getData('message');

/**
 * This hook gives a chance to prepend content or to replace the default view content with a custom content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->getData()}
 * In case the content is replaced, make sure to set {@CAttributeCollection $collection->add('renderContent', false)}
 * in order to stop rendering the default content.
 * @since 1.3.3.1
 */
hooks()->doAction('before_view_file_content', $viewCollection = new CAttributeCollection([
    'controller'    => $controller,
    'renderContent' => true,
]));

// and render if allowed
if ($viewCollection->itemAt('renderContent')) {
    /**
     * This hook gives a chance to prepend content before the active form or to replace the default active form entirely.
     * Please note that from inside the action callback you can access all the controller view variables
     * via {@CAttributeCollection $collection->controller->getData()}
     * In case the form is replaced, make sure to set {@CAttributeCollection $collection->add('renderForm', false)}
     * in order to stop rendering the default content.
     * @since 1.3.3.1
     */
    hooks()->doAction('before_active_form', $collection = new CAttributeCollection([
        'controller'    => $controller,
        'renderForm'    => true,
    ]));

    // and render if allowed
    if ($collection->itemAt('renderForm')) {
        /** @var CActiveForm $form */
        $form = $controller->beginWidget('CActiveForm'); ?>
        <div class="box box-primary borderless">
            <div class="box-header">
                <div class="pull-left">
                    <?php BoxHeaderContent::make(BoxHeaderContent::LEFT)
                        ->add('<h3 class="box-title">' . IconHelper::make('envelope') . html_encode((string)$pageHeading) . '</h3>')
                        ->render(); ?>
                </div>
                <div class="pull-right">
                    <?php BoxHeaderContent::make(BoxHeaderContent::RIGHT)
                        ->addIf(HtmlHelper::accessLink(IconHelper::make('create') . t('app', 'Create new'), ['customer_messages/create'], ['class' => 'btn btn-primary btn-flat', 'title' => t('app', 'Create new')]), !$message->getIsNewRecord())
                        ->add(HtmlHelper::accessLink(IconHelper::make('cancel') . t('app', 'Cancel'), ['customer_messages/index'], ['class' => 'btn btn-primary btn-flat', 'title' => t('app', 'Cancel')]))
                        ->render(); ?>
                </div>
                <div class="clearfix"><!-- --></div>
            </div>
            <div class="box-body">
                <?php
                /**
                 * This hook gives a chance to prepend content before the active form fields.
                 * Please note that from inside the action callback you can access all the controller view variables
                 * via {@CAttributeCollection $collection->controller->getData()}
                 * @since 1.3.3.1
                 */
                hooks()->doAction('before_active_form_fields', new CAttributeCollection([
                    'controller'    => $controller,
                    'form'          => $form,
                ])); ?>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <?php echo $form->labelEx($message, 'title'); ?>
                            <?php echo $form->textField($message, 'title', $message->fieldDecorator->getHtmlOptions('title')); ?>
                            <?php echo $form->error($message, 'title'); ?>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <?php echo $form->labelEx($message, 'customer_id'); ?>
                            <?php echo $form->hiddenField($message, 'customer_id', $message->fieldDecorator->getHtmlOptions('customer_id')); ?>
                            <?php
                            $controller->widget('zii.widgets.jui.CJuiAutoComplete', [
                                'name'          => 'customer',
                                'value'         => !empty($message->customer) ? ($message->customer->getFullName() ?: $message->customer->email) : null,
                                'source'        => createUrl('customers/autocomplete'),
                                'cssFile'       => false,
                                'options'       => [
                                    'minLength' => '2',
                                    'select'    => 'js:function(event, ui) {
                                $("#' . CHtml::activeId($message, 'customer_id') . '").val(ui.item.customer_id);
                            }',
                                    'search'    => 'js:function(event, ui) {
                                $("#' . CHtml::activeId($message, 'customer_id') . '").val("");
                            }',
                                    'change'    => 'js:function(event, ui) {
                                if (!ui.item) {
                                    $("#' . CHtml::activeId($message, 'customer_id') . '").val("");
                                }
                            }',
                                ],
                                'htmlOptions'   => $message->fieldDecorator->getHtmlOptions('customer_id'),
                            ]); ?>
                            <?php echo $form->error($message, 'customer_id'); ?>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <?php echo $form->labelEx($message, 'status'); ?>
                            <?php echo $form->dropDownList($message, 'status', $message->getStatusesList(), $message->fieldDecorator->getHtmlOptions('status')); ?>
                            <?php echo $form->error($message, 'status'); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <?php echo $form->labelEx($message, 'message'); ?>
                            <?php echo $form->textArea($message, 'message', $message->fieldDecorator->getHtmlOptions('message', ['rows' => 15])); ?>
                            <?php echo $form->error($message, 'message'); ?>
                        </div>
                    </div>
                </div>
                <?php
                /**
                 * This hook gives a chance to append content after the active form fields.
                 * Please note that from inside the action callback you can access all the controller view variables
                 * via {@CAttributeCollection $collection->controller->getData()}
                 * @since 1.3.3.1
                 */
                hooks()->doAction('after_active_form_fields', new CAttributeCollection([
                    'controller'    => $controller,
                    'form'          => $form,
                ])); ?>
                <div class="clearfix"><!-- --></div>
            </div>
            <div class="box-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary btn-flat"><?php echo IconHelper::make('save') . t('app', 'Save changes'); ?></button>
                </div>
                <div class="clearfix"><!-- --></div>
            </div>
        </div>
        <?php
        $controller->endWidget();
    }
    /**
     * This hook gives a chance to append content after the active form.
     * Please note that from inside the action callback you can access all the controller view variables
     * via {@CAttributeCollection $collection->controller->getData()}
     * @since 1.3.3.1
     */
    hooks()->doAction('after_active_form', new CAttributeCollection([
        'controller'      => $controller,
        'renderedForm'    => $collection->itemAt('renderForm'),
    ]));
}
/**
 * This hook gives a chance to append content after the view file default content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->getData()}
 * @since 1.3.3.1
 */
hooks()->doAction('after_view_file_content', new CAttributeCollection([
    'controller'        => $controller,
    'renderedContent'   => $viewCollection->itemAt('renderContent'),
]));
