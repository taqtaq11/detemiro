<?php
    add_action(array(
        'code'     => 'hello',
        'function' => 'hello_world'
    ));

    function hello_world() {
        echo 'GOOD DAY!';
    }
?>