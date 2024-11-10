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

?>

<div class="box box-primary borderless">
    <div class="box-header">
        <div class="pull-left">
            <h3 class="box-title"><i class="fa fa-bar-chart-o" aria-hidden="true"></i><?php echo t('transactional_emails', 'Transactional emails dashboard'); ?></h3>
        </div>
    </div>
    <div class="box-body">
        <div class="row boxes-mw-wrapper">
            <div class="col-lg-3 col-xs-6">
                <div class="small-box">
                    <div class="inner">
                        <div class="middle">
                            <h3><?php echo !empty($totalLink) ? $totalLink : ''; ?></h3>
                            <p><?php echo t('transactional_emails', 'Total'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box">
                    <div class="inner">
                        <div class="middle">
                            <h3><?php echo !empty($sentLink) ? $sentLink : ''; ?></h3>
                            <p><?php echo t('transactional_emails', 'Sent'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box">
                    <div class="inner">
                        <div class="middle">
                            <h3><?php echo !empty($unsentLink) ? $unsentLink : ''; ?></h3>
                            <p><?php echo t('transactional_emails', 'Unsent'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box">
                    <div class="inner">
                        <div class="middle">
                            <h3><?php echo !empty($failedLink) ? $failedLink : ''; ?></h3>
                            <p><?php echo t('transactional_emails', 'Failed'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
