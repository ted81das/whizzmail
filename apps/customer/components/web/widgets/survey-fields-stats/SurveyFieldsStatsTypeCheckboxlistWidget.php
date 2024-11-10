<?php declare(strict_types=1);
if (!defined('MW_PATH')) {
    exit('No direct script access allowed');
}

if (!class_exists('SurveyFieldsStatsTypeBaseWidget', false)) {
    require_once __DIR__ . '/SurveyFieldsStatsTypeBaseWidget.php';
}
/**
 * SurveyFieldsStatsTypeCheckboxlistWidget
 *
 * @package MailWizz EMA
 * @author MailWizz Development Team <support@mailwizz.com>
 * @link https://www.mailwizz.com/
 * @copyright MailWizz EMA (https://www.mailwizz.com)
 * @license https://www.mailwizz.com/license/
 * @since 1.7.8
 */

class SurveyFieldsStatsTypeCheckboxlistWidget extends SurveyFieldsStatsTypeBaseWidget
{
    /**
     * @return array
     */
    protected function getData(): array
    {
        $data = [];

        $field  = $this->field;
        $survey = $this->survey;

        if (empty($survey) || empty($field)) {
            return $data;
        }

        $respondersCount = SurveyResponder::model()->countByAttributes([
            'survey_id' => $survey->survey_id,
        ]);

        if (empty($respondersCount)) {
            return $data;
        }

        $options = [];

        if (!empty($field->options)) {
            foreach ($field->options as $option) {
                $options[$option->value] = $option->name;
            }
        }

        $criteria = new CDbCriteria();
        $criteria->select = 'value, COUNT(value) AS counter';
        $criteria->compare('field_id', $field->field_id);
        $criteria->group = 'value';
        $results = SurveyFieldValue::model()->findAll($criteria);

        $responsesCount = 0;
        foreach ($results as $result) {
            if (!isset($options[$result->value])) {
                continue;
            }

            $data[] = [
                'label'           => $options[$result->value],
                'data'            => $result->counter,
                'count'           => $result->counter,
                'count_formatted' => $result->counter,
            ];

            $responsesCount += $result->counter;
        }

        $emptyResponsesCount = $respondersCount - $responsesCount;

        $data[] = [
            'label'           => t('surveys', 'Without response'),
            'data'            => $emptyResponsesCount,
            'count'           => $emptyResponsesCount,
            'count_formatted' => $emptyResponsesCount,
        ];

        return $data;
    }
}
