<?php declare(strict_types=1);
if (!defined('MW_PATH')) {
    exit('No direct script access allowed');
}

/**
 * TransactionalEmails24HoursPerformanceWidget
 *
 * @package MailWizz EMA
 * @author MailWizz Development Team <support@mailwizz.com>
 * @link https://www.mailwizz.com/
 * @copyright MailWizz EMA (https://www.mailwizz.com)
 * @license https://www.mailwizz.com/license/
 * @since 2.5.8
 */

class TransactionalEmails24HoursPerformanceWidget extends CWidget
{
    /**
     * @return void
     * @throws CException
     */
    public function run()
    {
        $email    = new TransactionalEmail();
        $dateFormatter = $email->dateTimeFormatter;

        $cacheKey = sha1(__METHOD__ . date('H') . time());
        if (($chartData = cache()->get($cacheKey)) === false) {
            $chartData = [];

            // total
            $query  = '
              SELECT COUNT(DISTINCT (`email_id`)) as counter, DATE_FORMAT(date_added, \'%Y-%m-%d %H:00:00\') as hr 
                FROM `{{transactional_email}}` 
                WHERE date_added >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                GROUP BY hr ORDER BY hr ASC 
                LIMIT 24
            ';

            $rows = db()->createCommand($query)->queryAll();
            $data = [];

            foreach ($rows as $row) {
                $data[] = [(int)(strtotime($dateFormatter->convertDateTime($row['hr'])) * 1000), (int)$row['counter']];
            }

            $chartData[] = [
                'label' => '&nbsp;' . t('transactional_emails', 'Total'),
                'data'  => $data,
            ];

            $params = [];
            // Sent

            $query  = '
              SELECT COUNT(DISTINCT (`email_id`)) as counter, DATE_FORMAT(date_added, \'%Y-%m-%d %H:00:00\') as hr 
                FROM `{{transactional_email}}` 
                WHERE date_added >= DATE_SUB(NOW(), INTERVAL 24 HOUR) AND status = :status
                GROUP BY hr ORDER BY hr ASC 
                LIMIT 24
            ';

            $params[':status'] = TransactionalEmail::STATUS_SENT;

            $rows = db()->createCommand($query)->queryAll(true, $params);
            $data = [];

            foreach ($rows as $row) {
                $data[] = [(int)(strtotime($dateFormatter->convertDateTime($row['hr'])) * 1000), (int)$row['counter']];
            }

            $chartData[] = [
                'label' => '&nbsp;' . t('transactional_emails', 'Sent'),
                'data'  => $data,
            ];

            $params[':status'] = TransactionalEmail::STATUS_UNSENT;

            $rows = db()->createCommand($query)->queryAll(true, $params);
            $data = [];

            foreach ($rows as $row) {
                $data[] = [(int)(strtotime($dateFormatter->convertDateTime($row['hr'])) * 1000), (int)$row['counter']];
            }

            $chartData[] = [
                'label' => '&nbsp;' . t('transactional_emails', 'Unsent'),
                'data'  => $data,
            ];

            $params[':status'] = TransactionalEmail::STATUS_FAILED;

            $rows = db()->createCommand($query)->queryAll(true, $params);
            $data = [];

            foreach ($rows as $row) {
                $data[] = [(int)(strtotime($dateFormatter->convertDateTime($row['hr'])) * 1000), (int)$row['counter']];
            }

            $chartData[] = [
                'label' => '&nbsp;' . t('transactional_emails', 'Failed'),
                'data'  => $data,
            ];

            cache()->set($cacheKey, $chartData, 3600);
        }

        $hasRecords = false;
        foreach ($chartData as $data) {
            if (!empty($data['data'])) {
                $hasRecords = true;
                break;
            }
        }

        if (!$hasRecords) {
            return;
        }

        clientScript()->registerScriptFile(apps()->getBaseUrl('assets/js/campaign-24hours-performance.js'));

        $this->render('24hours-performance', compact('chartData'));
    }
}
