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
 * @since 2.5.8
 */

/** @var Controller $controller */
$controller = controller();

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
if ($viewCollection->itemAt('renderContent')) { ?>
    <div id="transactional-emails-dashboard-counter-boxes-wrapper" data-url="<?php echo createUrl('transactional_emails_dashboard_widgets/counter_boxes'); ?>">
        <div class="box borderless">
            <div class="box-body">
                <div class="row">
                    <div class="ph-item">
                        <div class="ph-col-12">
                            <div class="ph-row">
                                <div class="ph-col-2 big"></div>
                                <div class="ph-col-10 empty big"></div>
                            </div>
                        </div>
                        <div class="ph-col-3">
                            <div class="ph-picture"></div>
                        </div>
                        <div class="ph-col-3">
                            <div class="ph-picture"></div>
                        </div>
                        <div class="ph-col-3">
                            <div class="ph-picture"></div>
                        </div>
                        <div class="ph-col-3">
                            <div class="ph-picture"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="transactional-emails-dashboard-daily-performance-wrapper" data-url="<?php echo createUrl('transactional_emails_dashboard_widgets/daily_performance'); ?>">
        <div class="box borderless">
            <div class="box-body">
                <div class="row">
                    <div class="ph-item">
                        <div class="ph-col-12">
                            <div class="ph-row">
                                <div class="ph-col-2 big"></div>
                                <div class="ph-col-10 empty big"></div>
                            </div>
                        </div>
                        <div class="ph-col-12">
                            <div class="ph-picture"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="transactional-emails-dashboard-weekly-activity-wrapper" data-url="<?php echo createUrl('transactional_emails_dashboard_widgets/weekly_activity'); ?>">
        <div class="box borderless">
            <div class="box-body">
                <div class="row">
                    <div class="ph-item">
                        <div class="ph-col-12">
                            <div class="ph-row">
                                <div class="ph-col-2 big"></div>
                                <div class="ph-col-10 empty big"></div>
                            </div>
                        </div>
                        <div class="ph-col-12">
                            <div class="ph-picture"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="transactional-emails-dashboard-cron-history-wrapper" data-url="<?php echo createUrl('transactional_emails_dashboard_widgets/cron_history'); ?>">
        <div class="box borderless">
            <div class="box-body">
                <div class="row">
                    <div class="ph-item">
                        <div class="ph-col-12">
                            <div class="ph-row">
                                <div class="ph-col-2 big"></div>
                                <div class="ph-col-10 empty big"></div>
                            </div>
                        </div>
                        <div class="ph-col-12">
                            <div class="ph-picture"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
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
