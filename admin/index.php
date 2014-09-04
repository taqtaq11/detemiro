<?php
    ob_start();

    define('INCLUDES', '/includes');
    define('ABSPATH', realpath(dirname(__FILE__) . '/../'));

    require(ABSPATH . INCLUDES . '/basic-load.php');

    if(!$BLOCK) {
        require(ABSPATH . '/' . ADMIN . INCLUDES . '/admin-load.php'); 
        require(ABSPATH . INCLUDES . '/basic-doit.php');
    }
    else {
        redirect(BASE_URL);
    }

    $out = ob_get_clean();

    echo str_insert('output-messages">', get_output_result_messages(), $out);
?>