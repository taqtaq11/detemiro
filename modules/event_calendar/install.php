<?php
    global $DETDB;
    add_rule(array(
        'code' => 'calendar_event_admin',
        'desc' => 'Возможность управления событиями'
    ));

    give_rule('calendar_event_admin', 'admin');

    $DETDB->create_table('calendar_events', array(
        'ID'          => 'int NOT NULL AUTO_INCREMENT PRIMARY KEY',
        'date_start'  => 'DATETIME',
        'date_end'    => 'DATETIME',
        'name'        => 'TEXT',
        'place'       => 'VARCHAR(255)',
        'worker'      => 'VARCHAR(255)',
        'date_params' => 'VARCHAR(14)'
    ));

    add_option(array(
        'code'  => 'calendar_events_range',
        'name'  => 'Поиск пересечений',
        'value' => 1
    ));
?>