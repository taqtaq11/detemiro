<?php
    if(!isset($CONNECT)) {
        error_reporting(E_ALL);
        ini_set('display_errors', 'On');
    }
    else {
        error_reporting(0);
        ini_set('display_errors', 'Off');
    }

    $BLOCK = false;

    //Получаю все важные данные для ядра
    require_once(ABSPATH . INCLUDES . '/basic-config.php');

    //Подсоединяем БД
    require_once(ABSPATH . INCLUDES . '/basic-db.php');

    //Подключать дополнительные функции
    require_once(ABSPATH . INCLUDES . '/basic-functions.php');

    //Получаю информацию о движке

    $SYSTEM = new stdClass();
    $SYSTEM->name = 'Detemiro Engine';
    $SYSTEM->version = '1.0';
    $SYSTEM->nick    = 'Basic';
    if(file_exists(ABSPATH . INCLUDES . '/log/system.json')) {
        $content = read_json(ABSPATH . INCLUDES . '/log/system.json', false);
        if($content && is_object($content)) {
            $SYSTEM->version = (isset($content->version) && $content->version) ? $content->version : '1.0';
            $SYSTEM->nick    = (isset($content->nick) && $content->nick) ? $content->nick : 'custom';
        }
    }

    //Работа с опциями
    require_once(ABSPATH . INCLUDES . '/basic-options.php');

    //Безопасность
    require_once(ABSPATH . INCLUDES . '/basic-secure.php');
    
    //Работа с пользователями
    require_once(ABSPATH . INCLUDES . '/basic-users.php');

    //Работа с системными сообщениями
    require_once(ABSPATH . INCLUDES . '/basic-messages.php');

    //Работа с модулями
    require_once(ABSPATH . INCLUDES . '/basic-modules.php');

    //DETBLOCKS
    require_once(ABSPATH . INCLUDES . '/basic-detblocks.php');

    //Коллекторы, основы основ
    require_once(ABSPATH . INCLUDES . '/basic-page-collector.php');
    require_once(ABSPATH . INCLUDES . '/basic-script-collector.php');
    require_once(ABSPATH . INCLUDES . '/basic-action-collector.php');

    //Работа по шаблонам
    require_once(ABSPATH . INCLUDES . '/basic-page.php');
?>