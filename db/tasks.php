<?php
defined('MOODLE_INTERNAL') || die();

$tasks = [
    [
        'classname' => 'theme_remui\task\notification_pref_updater',
        'blocking' => 0,
        'minute' => '*',
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*',
    ],
    //  EXPORT CSV QUOTIDIEN - 7h du matin
    [
        'classname' => 'theme_remui\task\ffa_daily_export',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '7',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    ]
];