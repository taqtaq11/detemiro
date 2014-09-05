<?php
    class page_selector {
        public $code;
        public $title;
        public $messages = array();
        public $content  = array(
            'pagi'    => false,
            'offset'  => null,
            'limit'   => 15,
            'handler' => null,
            'body'    => '',
            'all'     => null,
            'current' => null
        );
    }

    $PAGE = new page_selector();

    //Заголовок страницы
    function page_title($main = '') {
        global $PAGE;

        $paged = (isset($PAGE->content['pagi']) && $PAGE->content['pagi']) ? get_pagination_number() : null;

        $head = ($main == '') ? get_option('site_name') : $main; 

        echo $head . ' - ' . $PAGE->title;
        if($paged) echo " (Страница $paged)";
    }

    //Шапка
    function page_header() {
        echo '<meta charset="' . LANG_CHARSET . '" />' . "\n";
        scripts_zone('header');
    }

    //Низ страницы
    function page_footer() {
        scripts_zone('footer');
    }

    //Подмена страницы
    function replace_page($code) {
        global $PAGE, $APAGES;

        if(isset($APAGES[$code])) {
            $PAGE->code  = $code;
            $PAGE->title = $APAGES[$code]->title;
        }
        else {
            $PAGE->code  = '404_error';
            $PAGE->title = $APAGES['404_error']->title;
        }
    }

    //Задать контент
    function set_glob_content($par) {
        global $PAGE, $DETDB;

        $obj = ($par && is_object($par)) ? true : false;
        $par = take_good_array($par);

        $custom = &$PAGE->content;
        $custom = set_merge($custom, $par);

        if($custom['pagi']) {
            if($custom['current'] === null) {
                $custom['current'] = get_pagination_number();
            }
            if($custom['offset'] === null) {
                $custom['offset'] = $custom['limit'] * ($custom['current'] - 1);
            }
        }
        if($custom['handler']) {
            $pre = null;
            if(is_string($custom['handler'])) {
                $pre = make_action($custom['handler'], $custom);
            }
            elseif(is_callable($custom['handler'])) {
                $pre = call_user_func($custom['handler'], $custom);
            }
            if($pre && (is_object($pre) || is_array($pre))) {
                $custom = set_merge($custom, $pre, true);
            }
        }

        if($custom['pagi'] && $custom['all'] === null) {
            $custom['all'] = (isset($par['table'])) ? $DETDB->count($par['table']) : 1;
        }

        if($custom['body'] == '' && isset($par['table'])) {
            if($custom['pagi']) {
                $par['offset'] = $custom['offset'];
                $par['limit']  = $custom['limit'];
            }
            $custom['body'] = $DETDB->select($par);
        }

        if($custom['pagi'] && ($custom['all'] === null || ($custom['all'] && $custom['limit'] && ceil($custom['all'] / $custom['limit']) < $custom['current']))) {
            redirect(get_current_key(), true);
        }

        if(!isset($par['pagi']) && !isset($par['body']) && !isset($par['table']) && !isset($par['handler'])) {
            $custom['body'] = (($obj) ? (object) $par : $par);
        }
    }

    function get_pagi_struct() {
        global $PAGE;

        return $PAGE->content;
    }

    //Получить контент
    function get_glob_content() {
        global $PAGE;

        return $PAGE->content['body'];
    }

    //Генерация страниц
    function get_pagination_number() {
        global $PAGE;

        $current = 1;

        if(isset($_GET['paged'])) {
            $current = $_GET['paged'];
        }

        if(!is_numeric($current) && $current < 1) {
            $current = 1;
        }

        return $current;
    }

    //Показать навигацию разбиения контента
    function pagination_show($disable = false, $class = '') {
        global $DETDB, $PAGE;

        if($PAGE->content['pagi'] && $PAGE->content['all'] && ($PAGE->content['all'] > $PAGE->content['limit'] || $disable)) {
            $all  = ceil($PAGE->content['all'] / $PAGE->content['limit']);

            $out = '';

            $class = ($class) ? ' ' . $class : $class;
            $out .= '<div class="pagination-block"><ul class="pagination' . $class . '">';

            if($PAGE->content['current'] <= 1) {
                $out .= '<li class="disabled"><span>&laquo;</span></li>';
            }
            else {
                $out .= '<li><a href="' . get_page_link() . '&paged=' . ($PAGE->content['current'] - 1) . '">&laquo;</a></li>';
            }

            for($i=1; $i<=$all; $i++) {
                $out .= '<li';
                if($i == $PAGE->content['current']) {
                    $out .= ' class="active"';
                }
                $out .= '>';

                    if($i == $PAGE->content['current']) {
                        $out .= '<span class="current">' . $i . '</span>';
                    }
                    else {
                        $out .= '<a href="' . get_page_link() . '&paged=' . $i .'">' . $i . '</a>';
                    }

                $out .= '</li>';
            }

            if($PAGE->content['current'] >= $all) {
                $out .= '<li class="disabled"><span>&raquo;</span></li>';
            }
            else {
                $out .= '<li><a href="' . get_page_link() . '&paged=' . ($PAGE->content['current'] + 1) .'">&raquo;</a></li>';
            }

            $out .= '</ul></div>';

            echo $out;
        }
    }
?>