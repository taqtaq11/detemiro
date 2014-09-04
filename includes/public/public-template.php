<?php
    $TEMPLATE = get_option('current_template');

    //Безопасное подключение файлов шаблона и скриптов
    function get_template($file, $base=false) {
        global $TEMPLATE, $PAGE;
        if(file_exists(ABSPATH . '/public/templates/' . $TEMPLATE . '/' . $file) && !$base) {
            require(ABSPATH . '/public/templates/' . $TEMPLATE . '/' . $file);
        }
        elseif(file_exists(ABSPATH . '/public/templates/base/' . $file)) {
            require(ABSPATH . '/public/templates/base/' . $file);
        }
    }

    function get_file($file, $base=false) {
        global $TEMPLATE;
        if(file_exists(ABSPATH . '/public/templates/' . $TEMPLATE . '/' . $file) && !$base) {
            return BASE_URL . '/public/templates/' . $TEMPLATE . '/' . $file;
        }
        elseif(file_exists(ABSPATH . '/public/templates/base/' . $file)) {
            return BASE_URL . '/public/templates/base/' . $file;
        }
    }
    
    //Ссылка
    function get_page_link($code=null) {
        global $APAGES, $PAGE;
        if(!$code) $code = $PAGE->code;

        $res = (isset($APAGES[$code])) ? $code : 'index';

        return BASE_URL . '/index.php?page=' . $res;
    }
?>