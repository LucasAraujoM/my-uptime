<?php

return [
    'methods' => [
        ['id' => 0, 'name' => 'GET'],
        ['id' => 1, 'name' => 'POST'],
        ['id' => 2, 'name' => 'PUT'],
        ['id' => 3, 'name' => 'DELETE'],
    ],
    'intervals' => [
        ['id' => 0, 'name' => 'everyThirtySeconds'],
        ['id' => 1, 'name' => 'everyMinute'],
        ['id' => 2, 'name' => 'everyFiveMinutes'],
        ['id' => 3, 'name' => 'everyTenMinutes'],
        ['id' => 4, 'name' => 'everyFifteenMinutes'],
        ['id' => 5, 'name' => 'everyThirtyMinutes'],
        ['id' => 6, 'name' => 'hourly'],
        ['id' => 7, 'name' => 'everyTwoHours'],
        ['id' => 8, 'name' => 'everyThreeHours'],
        ['id' => 9, 'name' => 'everyFourHours'],
        ['id' => 10, 'name' => 'everyFiveHours'],
        ['id' => 11, 'name' => 'everySixHours'],
        ['id' => 12, 'name' => 'everyTwelveHours'],
        ['id' => 13, 'name' => 'daily'],
    ],
    'conditions' => [
        ['id' => 0, 'name' => 'No Keyword Monitoring'],
        ['id' => 1, 'name' => 'When Keyword exists'],
        ['id' => 2, 'name' => 'When Keyword not exists'],
    ],
    'timeouts' => [
        ['id' => 0, 'name' => '10 seconds'],
        ['id' => 1, 'name' => '30 seconds'],
        ['id' => 2, 'name' => '1 minute'],
        ['id' => 3, 'name' => '5 minutes'],
    ],
];