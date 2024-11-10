<?php declare(strict_types=1);
if (!defined('MW_PATH')) {
    exit('No direct script access allowed');
}

/**
 * CampaignOverviewCounterBoxesWidget
 *
 * @package MailWizz EMA
 * @author MailWizz Development Team <support@mailwizz.com>
 * @link https://www.mailwizz.com/
 * @copyright MailWizz EMA (https://www.mailwizz.com)
 * @license https://www.mailwizz.com/license/
 * @since 2.5.8
 */
class TransactionalEmailsDashboardCounterBoxesWidget extends CWidget
{
    /**
     * @return void
     * @throws CException
     */
    public function run()
    {
        $totalUrl  = createUrl('transactional_emails/index');
        $sentUrl   = createUrl('transactional_emails/sent');
        $unsentUrl = createUrl('transactional_emails/unsent');
        $failedUrl = createUrl('transactional_emails/failed');

        $total  = TransactionalEmail::model()->count();
        $sent   = TransactionalEmail::model()->countByAttributes(['status' => TransactionalEmail::STATUS_SENT]);
        $unsent = TransactionalEmail::model()->countByAttributes(['status' => TransactionalEmail::STATUS_UNSENT]);
        $failed = TransactionalEmail::model()->countByAttributes(['status' => TransactionalEmail::STATUS_FAILED]);

        $totalLink  = CHtml::link($total, $totalUrl);
        $sentLink   = CHtml::link($sent, $sentUrl);
        $unsentLink = CHtml::link($unsent, $unsentUrl);
        $failedLink = CHtml::link($failed, $failedUrl);

        $this->render('counter-boxes', compact(
            'totalLink',
            'sentLink',
            'unsentLink',
            'failedLink'
        ));
    }
}
