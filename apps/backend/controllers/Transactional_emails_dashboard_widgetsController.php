<?php declare(strict_types=1);
if (!defined('MW_PATH')) {
    exit('No direct script access allowed');
}

/**
 * Transactional_emails_dashboard_widgetsController
 *
 * Handles the actions that fetch the widgets for the transactional emails dashboard page
 *
 * @package MailWizz EMA
 * @author MailWizz Development Team <support@mailwizz.com>
 * @link https://www.mailwizz.com/
 * @copyright MailWizz EMA (https://www.mailwizz.com)
 * @license https://www.mailwizz.com/license/
 * @since 2.5.8
 */

class Transactional_emails_dashboard_widgetsController extends Controller
{
    /**
     * @return void
     * @throws CException
     */
    public function init()
    {
        clientScript()->registerScriptFile(apps()->getBaseUrl('assets/js/flot/jquery.flot.min.js'));
        clientScript()->registerScriptFile(apps()->getBaseUrl('assets/js/flot/jquery.flot.resize.min.js'));
        clientScript()->registerScriptFile(apps()->getBaseUrl('assets/js/flot/jquery.flot.crosshair.min.js'));
        clientScript()->registerScriptFile(apps()->getBaseUrl('assets/js/flot/jquery.flot.time.min.js'));
        clientScript()->registerScriptFile(apps()->getBaseUrl('assets/js/flot/jquery.flot.categories.min.js'));
        clientScript()->registerScriptFile(apps()->getBaseUrl('assets/js/flot/jquery.flot.pie.min.js'));

        parent::init();
    }

    /**
     * Get the transactional emails html for the "counter boxes" widget
     *
     * @return void
     * @throws CException
     */
    public function actionCounter_boxes()
    {
        $this->renderJson([
            'html' => $this->widget('backend.components.web.widgets.transactional-emails.TransactionalEmailsDashboardCounterBoxesWidget', [], true),
        ]);
    }

    /**
     * Get the campaigns overview html for the "daily performance graph" widget
     *
     * @return void
     * @throws CException
     */
    public function actionDaily_performance()
    {
        $this->renderJson([
            'html' => $this->renderPartial('backend.views.transactional_emails._daily-performance', [], true, true),
        ]);
    }

    /**
     * Get the list overview html for the "TransactionalEmails7DaysActivityWidget" widget
     *
     * @return void
     * @throws CException
     */
    public function actionWeekly_activity()
    {
        $this->renderJson([
            'html'  => $this->renderPartial('backend.views.transactional_emails._7days_activity', [], true, true),
        ]);
    }

    /**
     * @return void
     * @throws CException
     */
    public function actionCron_history()
    {
        $command = ConsoleCommandList::model()->findByAttributes(['command' => 'send-transactional-emails']);

        if (empty($command)) {
            $this->renderJson([
                'html' => '',
            ]);
            return;
        }

        $model = new ConsoleCommandListHistory('search');
        $model->unsetAttributes();
        $model->command_id = $command->command_id;

        $this->renderJson([
            'html' => $this->renderPartial('backend.views.transactional_emails._cron-history', [
                'model' => $model,
            ], true, true),
        ]);
    }

    /**
     * @param CAction $action
     *
     * @return bool
     * @throws CException
     */
    protected function beforeAction($action)
    {
        if (!request()->getIsAjaxRequest()) {
            $this->redirect(['dashboard/index']);
            return false;
        }

        return parent::beforeAction($action);
    }
}
