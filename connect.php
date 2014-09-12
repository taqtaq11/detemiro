<?php
    if(isset($_POST['action']) && $_POST['action']) {
        define('INCLUDES', '/includes');
        define('ABSPATH', realpath(dirname(__FILE__)));

        $CONNECT = (object) array(
            'action'   => $_POST['action'],
            'params'   => ((isset($_POST['params'])) ? $_POST['params'] : null),
            'category' => ((isset($_POST['category']) && $_POST['category']) ? $_POST['category'] : 'public'),
            'key'      => ((isset($_POST['key']) && $_POST['key']) ? $_POST['key'] : null),
            'host'     => (($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) : null),
            'type'     => null
        );

        if($CONNECT->key) {
            $CONNECT->type = 'remote';
        }
        elseif($CONNECT->host == $_SERVER['HTTP_HOST']) {
            $CONNECT->type = 'ajax';
        }

        if($CONNECT->type) {
            require_once(ABSPATH . INCLUDES . '/basic-load.php');
            global $BLOCK;

            if(!$BLOCK) {
                if($CONNECT->type == 'remote' && (!check_remote_key($CONNECT->key) || $_SERVER['HTTP_USER_AGENT'] != 'DETWorker')) {
                    echo 'Wrong key';
                }
                else {
                    $CONNECT->action = canone_code($CONNECT->action);
                    if(!preg_match('/^(' . $CONNECT->type . '_)/i', $CONNECT->action)) {
                        $CONNECT->action = $CONNECT->type . '_' . $CONNECT->action;
                    }
                    if($CONNECT->params && check_json($CONNECT->params)) {
                        $CONNECT->params = json_decode($CONNECT->params, true);
                    }

                    if($CONNECT->category == 'admin') {
                        require_once(ABSPATH . '/' . ADMIN . INCLUDES . '/admin-load.php');
                    }
                    else {
                        require_once(ABSPATH . INCLUDES . '/public/public-load.php');
                    }
                    require_once(ABSPATH . INCLUDES . '/basic-doit.php');

                    actions_zone($CONNECT->type . '_before_action', $CONNECT->params);
                    
                    make_action($CONNECT->action, $CONNECT->params);

                    actions_zone($CONNECT->type . '_after_action', $CONNECT->params);
                }
            }
        }
    }
?>