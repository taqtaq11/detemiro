<?php
    //Безопасное подключение файлов шаблона и скриптов
    function get_template($file) {
        global $PAGE, $SYSTEM;
        if(file_exists(ABSPATH . '/' . ADMIN . '/skelet/' . $file)) {
            require(ABSPATH . '/' . ADMIN . '/skelet/' . $file);
        }
    }

    function get_file($file) {
        if(file_exists(ABSPATH . '/' . ADMIN . '/skelet/' . $file)) {
            return BASE_URL . '/' . ADMIN . '/skelet/' . $file;
        }
    }

    //Ссылка
    function get_page_link($code=null, $public=false) {
        global $APAGES, $PAGE;
        if(!$code) $code = $PAGE->code;

        if($public) {
            return BASE_URL . '/index.php?page=' . $code;
        }
        else {
            $res = (isset($APAGES[$code])) ? $code : 'index';
            return BASE_URL . '/' . ADMIN . '/index.php?page=' . $res;
        }
    }

    //Получаю список шаблонов
    function get_templates() {
        $res = array();

        foreach(scandir(ABSPATH . '/public/templates') as $dir) {
            $path = ABSPATH . '/public/templates/' . $dir;

            if(is_dir($path) && file_exists($path . '/main.php')) {
                $custom = array(
                    'name'        => $dir,
                    'description' => '',
                    'version'     => 'Unknown',
                    'author'      => 'Unnamed',
                    'url'         => ''
                );

                if(file_exists($path . '/info.json')) {
                    $custom = set_merge($custom, read_json($path . '/info.json', true));
                }

                $custom['path'] = $dir;

                $res[] = $custom;
            }
        }

        return $res;
    }
?>