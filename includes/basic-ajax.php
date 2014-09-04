<?php
    if(isset($_POST['action'])) {
        define('INCLUDES', '/includes');
        define('ABSPATH', realpath(dirname(__FILE__) . '/../'));

        $IS_AJAX = true;

        require_once(ABSPATH . INCLUDES . '/basic-load.php');
        global $BLOCK;

        if(!$BLOCK) {
            if($_POST['category'] == 'admin') {
                require_once(ABSPATH . '/' . ADMIN . INCLUDES . '/admin-load.php');
            }
            else {
                require_once(ABSPATH . INCLUDES . '/public/public-load.php');
            }
            require_once(ABSPATH . INCLUDES . '/basic-doit.php');

            actions_zone('ajax_before_action');

            $key = canone_code($_POST['action']);

            if(!preg_match('/^(ajax_)/i', $key)) {
                $key = 'ajax_' . $key;
            }

            if(isset($_POST['params'])) {
                $params = $_POST['params'];
                if(check_json($params)) {
                    $params = json_decode($params, true);
                }
                make_action($key, $params);
            }
            else {
                make_action($key);
            }

            actions_zone('ajax_after_action');
        }
    }
?>