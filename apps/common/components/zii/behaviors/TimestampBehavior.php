<?php declare(strict_types=1);
if (!defined('MW_PATH')) {
    exit('No direct script access allowed');
}

/**
 * TimestampBehavior
 *
 * @package MailWizz EMA
 * @author MailWizz Development Team <support@mailwizz.com>
 * @link https://www.mailwizz.com/
 * @copyright MailWizz EMA (https://www.mailwizz.com)
 * @license https://www.mailwizz.com/license/
 * @since 2.5.6
 *
 */

Yii::import('zii.behaviors.CTimestampBehavior', true);

class TimestampBehavior extends CTimestampBehavior
{
    /**
     * @param string $columnType
     *
     * @return CDbExpression|mixed
     */
    protected function getTimestampByColumnType($columnType)
    {
        if (
            stripos($columnType, '/*') !== false &&
            stripos($columnType, '*/') !== false &&
            preg_match('/^(\w+)(?:\(([^\)]+)\))?/', $columnType, $matches)
        ) {
            $columnType = trim((string) $matches[0]);
        }
        // @phpstan-ignore-next-line
        return parent::getTimestampByColumnType($columnType);
    }
}
