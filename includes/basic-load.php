<?php
    if(!(isset($IS_AJAX) && $IS_AJAX)) {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    $BLOCK = false;

    //Получаю все важные данные для ядра
    require_once(ABSPATH . INCLUDES . '/basic-config.php');

    //Подсоединяем БД
    require_once(ABSPATH . INCLUDES . '/basic-db.php');

    //Подключать дополнительные функции
    require_once(ABSPATH . INCLUDES . '/basic-functions.php');

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