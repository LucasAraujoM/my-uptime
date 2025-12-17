<?php

return [
    'methods' => [
        ['id' => 0, 'name' => 'GET'],
        ['id' => 1, 'name' => 'POST'],
        ['id' => 2, 'name' => 'PUT'],
        ['id' => 3, 'name' => 'DELETE'],
    ],
    'intervals' => [
        ['id' => 0, 'name' => 'Every 30 seconds'],
        ['id' => 1, 'name' => 'Every minute'],
        ['id' => 2, 'name' => 'Every 5 minutes'],
        ['id' => 3, 'name' => 'Every 10 minutes'],
        ['id' => 4, 'name' => 'Every 15 minutes'],
        ['id' => 5, 'name' => 'Every 30 minutes'],
        ['id' => 6, 'name' => 'Every 1 hour'],
        ['id' => 7, 'name' => 'Every 2 hours'],
        ['id' => 8, 'name' => 'Every 3 hours'],
        ['id' => 9, 'name' => 'Every 4 hours'],
        ['id' => 10, 'name' => 'Every 5 hours'],
        ['id' => 11, 'name' => 'Every 6 hours'],
        ['id' => 12, 'name' => 'Every 12 hours'],
        ['id' => 13, 'name' => 'Every 24 hours'],
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