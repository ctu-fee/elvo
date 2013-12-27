<?php

return array(
    
    'application' => array(
        'name' => 'Elvo CLI'
    ),
    
    'db:init' => array(
        'init_script' => __DIR__ . '/../../module/Elvo/db/sqlite/init_tables.sql',
        'clear_script' => __DIR__ . '/../../module/Elvo/db/sqlite/clear_tables.sql'
    )
);