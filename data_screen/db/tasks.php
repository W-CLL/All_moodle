<?php

defined('MOODLE_INTERNAL') || die();

$tasks = array(
    array(
        'classname' => 'block_data_screen\task\cron_task_day',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '0',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    ),
    array(
        'classname' => 'block_data_screen\task\cron_task_hour',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '0,6,13,18',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    ),
);