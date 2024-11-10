<?php declare(strict_types=1);
if (!defined('MW_PATH')) {
    exit('No direct script access allowed');
}

/**
 * TransactionalEmails7DaysActivityWidget
 *
 * @package MailWizz EMA
 * @author MailWizz Development Team <support@mailwizz.com>
 * @link https://www.mailwizz.com/
 * @copyright MailWizz EMA (https://www.mailwizz.com)
 * @license https://www.mailwizz.com/license/
 * @since 2.5.8
 */

class TransactionalEmails7DaysActivityWidget extends CWidget
{
    /**
     * @return void
     * @throws CException
     */
    public function run()
    {
        $cacheKey = sha1(__METHOD__ . date('H'));
        if (($chartData = cache()->get($cacheKey)) === false) {
            $chartData = [
                'sent' => [
                    'label' => '&nbsp;' . t('list_subscribers', 'Sent'),
                    'data'  => [],
                ],
                'unsent' => [
                    'label' => '&nbsp;' . t('list_subscribers', 'Unsent'),
                    'data'  => [],
                ],
                'failed' => [
                    'label' => '&nbsp;' . t('list_subscribers', 'Failed'),
                    'data'  => [],
                ],
            ];

            for ($i = 0; $i < 7; $i++) {
                $timestamp = (int)strtotime(sprintf('-%d days', $i));

                // sent
                $count = TransactionalEmail::model()->count([
                    'condition' => '`status` = :st AND DATE(date_added) = :date',
                    'params'    => [
                        ':st'   => TransactionalEmail::STATUS_SENT,
                        ':date' => date('Y-m-d', $timestamp),
                    ],
                ]);
                $chartData['sent']['data'][] = [$timestamp * 1000, (int)$count];

                // unsent
                $count = TransactionalEmail::model()->count([
                    'condition' => '`status` = :st AND DATE(date_added) = :date',
                    'params'    => [
                        ':st'   => TransactionalEmail::STATUS_UNSENT,
                        ':date' => date('Y-m-d', $timestamp),
                    ],
                ]);
                $chartData['unsent']['data'][] = [$timestamp * 1000, (int)$count];

                // failed
                $count = TransactionalEmail::model()->count([
                    'condition' => '`status` = :st AND DATE(last_updated) = :date',
                    'params'    => [
                        ':st'   => TransactionalEmail::STATUS_FAILED,
                        ':date' => date('Y-m-d', $timestamp),
                    ],
                ]);
                $chartData['failed']['data'][] = [$timestamp * 1000, (int)$count];
            }

            $chartData = array_values($chartData);
            cache()->set($cacheKey, $chartData, 3600);
        }

        clientScript()->registerScriptFile(apps()->getBaseUrl('assets/js/flot/jquery.flot.min.js'));
        clientScript()->registerScriptFile(apps()->getBaseUrl('assets/js/flot/jquery.flot.resize.min.js'));
        clientScript()->registerScriptFile(apps()->getBaseUrl('assets/js/flot/jquery.flot.crosshair.min.js'));
        clientScript()->registerScriptFile(apps()->getBaseUrl('assets/js/flot/jquery.flot.time.min.js'));
        clientScript()->registerScriptFile(apps()->getBaseUrl('assets/js/strftime/strftime-min.js'));
        clientScript()->registerScriptFile(apps()->getBaseUrl('assets/js/7days-activity-chart.js'));

        $this->render('7days-activity', compact('chartData'));
    }
}
