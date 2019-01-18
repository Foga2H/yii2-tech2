<?php

use yii2tech\crontab\CronJob;
use yii2tech\crontab\CronTab;

// Try childbirth every 2 hours
$childbirthJob = new CronJob();
$childbirthJob->setLine('0 */2 * * * php /app/yii game/try-childbirth');

// Give bonus hearts every 1 month depends on animal count
$bonusHeartsJob = new CronJob();
$bonusHeartsJob->setLine('0 0 1 * * php /app/yii game/give-bonus-hearts');

// Animal oldness every 1 hour (substract 1 hour)
$animalOldness = new CronJob();
$animalOldness->setLine('0 * * * * php /app/yii game/animal-oldness');

$cronTab = new CronTab();
$cronTab->setJobs([
    $childbirthJob,
    $bonusHeartsJob,
    $animalOldness
]);
$cronTab->apply();