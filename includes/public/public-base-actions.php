<?php
    add_action(array(
        'code'     => 'remote_hello',
        'rule'     => 'public',
        'function' => function() {
            echo 'HELLO WORLD';
        }
    ));
?>