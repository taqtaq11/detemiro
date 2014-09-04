<?php
    define('INCLUDES', '/includes');
    define('ABSPATH', realpath(dirname(__FILE__)));

    ob_start();

    require_once(ABSPATH . INCLUDES . '/basic-load.php');
    global $BLOCK;

    if(!$BLOCK) {
        require_once(ABSPATH . INCLUDES . '/public/public-load.php');
        require_once(ABSPATH . INCLUDES . '/basic-doit.php');
    }
    
    $out = ob_get_clean();

    echo str_insert('output-messages">', get_output_result_messages(), $out);
?>