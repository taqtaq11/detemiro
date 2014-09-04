<?php
    //Добавить сообщение
    function push_output_message($params) {
        global $PAGE;

        $custom = array(
            'title'  => 'Информация', // заголовок сообщения
            'text'   => '', // текст
            'type'   => 'info', // тип, стандартно есть info, warning, error, success
            'page'   => '', // код страницы, на которой выводить уведомление
            'closed' => true,
            'class'  => 'alert alert-info'  // класс блока, в котором выводиться сообщения
        );

        if(is_array($params)) {
            $custom = set_merge($custom, $params);
        }
        else {
            $custom['text'] = $params;
        }
        
        $PAGE->messages[] = (object) $custom;
    }

    //Получить сообщения по типу, если пусто, то все сообщения (сообщения получаются по ходу выполнения интерпритатора)
    function get_output_messages($type = '') {
        global $PAGE;

        $res = array();

        foreach($PAGE->messages as $item) {
            if($type == '' || $item->type == $type) $res[] = $item;
        }

        usort($res, function($a, $b) {
            return strnatcasecmp($a->type, $b->type);
        });

        return $res;
    }

    //Отображаю итоговые сообщения после прохода интерпритатора
    function get_output_result_messages($type = '') {
        $ID = 'output-messages';

        $messages = get_output_messages($type);
        $out = '';

        foreach($messages as $key => $item) {
            $out .= '<div class="message-block ' . $item->class . '" data-message="' . $key . '">';
            if($item->closed) $out .= '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span></button>';
            if($item->title)   $out .= '<h4 class="message-title">' . $item->title . '</h4>';
            if($item->text)    $out .= '<div class="message-body">' . $item->text . '</div>';
            $out .= '</div>';
        }

        return $out;
    }

    function show_output_result_messages() {
        $ID = 'output-messages';

        echo '<div id="' . $ID . '"></div>';
    }
?>