<?php declare(strict_types=1);
if (!defined('MW_PATH')) {
    exit('No direct script access allowed');
}

if (!class_exists('SurveyFieldsStatsTypeCountryWidget', false)) {
    require_once __DIR__ . '/SurveyFieldsStatsTypeCountryWidget.php';
}

/**
 * SurveyFieldsStatsTypeGeocityWidget
 *
 * @package MailWizz EMA
 * @author MailWizz Development Team <support@mailwizz.com>
 * @link https://www.mailwizz.com/
 * @copyright MailWizz EMA (https://www.mailwizz.com)
 * @license https://www.mailwizz.com/license/
 * @since 1.7.8
 */

class SurveyFieldsStatsTypeGeocityWidget extends SurveyFieldsStatsTypeCountryWidget
{
}
