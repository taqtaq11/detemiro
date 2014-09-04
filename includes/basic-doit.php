<?php
    //Подключение модулей
    require_once(ABSPATH . INCLUDES . '/basic-modules-doit.php');

    //Проверка сообщений
    if(isset($_GET['message'])) {
        $message = json_decode($_GET['message'], true);
        if(!is_array($message)) {
            $message = array('warning', $message, 'Внимание!');
        }
        if($message[1]) {
            if($message[0] == 'reload') {
                $message[0] = 'success';
            }
            if(!isset($message[2])) {
                $message[2] = 'Внимание!';
            }
            push_output_message(array(
                'title' => $message[2],
                'type'  => $message[0],
                'text'  => $message[1],
                'class' => "alert alert-{$message[0]}"
            ));
        }
        unset($message);
    }

    actions_zone('before_form_page');

    //Формирование страницы
    if(is_ajax()) {
        $PAGE->code  = 'ajax_call';
    }
    elseif(!$PAGE->code) {
        $key = get_current_key();

        $PAGE->title = $APAGES[$key]->title;
        $PAGE->code  = $key;
    }
    
    uasort($APAGES, 'collector_sort');
    foreach($APAGES as $item) {
        if(count($item->childs)>1) usort($item->childs, 'apage_sort_child');
    }
    uasort($SCRIPTS, 'collector_sort');
    uasort($ACTIONS, 'collector_sort');

    //Загрузка шаблонов
    if(!is_ajax()) {
        actions_zone('before_template');

        if($APAGES[$PAGE->code]->skelet) {
            get_template('header.php');
        }

        show_apage($PAGE->code);

        if($APAGES[$PAGE->code]->skelet) {
            get_template('footer.php');
        }

        actions_zone('after_template');
    }
?>