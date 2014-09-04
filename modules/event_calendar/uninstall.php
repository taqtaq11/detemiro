<?php
    global $DETDB;

    delete_rule('calendar_event_admin');

    take_rule('calendar_event_admin', 'admin');

    $DETDB->delete_table('calendar_events');

    delete_option('calendar_events_range');
?>