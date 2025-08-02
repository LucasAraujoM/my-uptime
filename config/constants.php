<?php

return [
    'methods' => [
        ['id' => 0, 'name' => 'GET'],
        ['id' => 1, 'name' => 'POST'], 
        ['id' => 2, 'name' => 'PUT'],
        ['id' => 3, 'name' => 'DELETE'],
    ],
    'intervals' => [
        ['id' => 0, 'name' => 'every 60 seconds'],
        ['id' => 1, 'name' => 'every 10 minutes'],
        ['id' => 2, 'name' => 'every 30 minutes'], 
        ['id' => 3, 'name' => 'every 1 hour'],
        ['id' => 4, 'name' => 'every 2 hours'],
        ['id' => 5, 'name' => 'every 3 hours'],
        ['id' => 6, 'name' => 'every 4 hours'],
        ['id' => 7, 'name' => 'every 5 hours'],
        ['id' => 8, 'name' => 'every 6 hours'],
        ['id' => 9, 'name' => 'every 12 hours'],
        ['id' => 10, 'name' => 'every 24 hours'],
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