<?php
    //Объект с инфой о странице
    class page_collector {
        public $parent   = '';
        public $title    = '';
        public $code     = '';
        public $function = '';
        public $rule     = 'public';
        public $priority = 30;
        public $category = 'public';
        public $skelet   = true;
        public $childs   = array();
        function __construct($array) {
            if(is_merged($array)) {
                set_ref_merge($this, $array);
            }
        }
        public function doit() {
            if(check_rule($this->rule) && ($this->function != null && ((is_string($this->function) && function_exists($this->function)) || is_callable($this->function)))) {
                actions_zone("before_page_{$this->code}");

                call_user_func($this->function);

                actions_zone("after_page_{$this->code}");
            }
        }
    }
    $APAGES = array();

    //Добавить страницу в APAGES
    function add_apage($arr) {
        global $APAGES;
        $custom = (array) new page_collector($arr);

        $custom = set_merge($custom, $arr);

        $key = $custom['code'] = canone_code($custom['code']);

        if(validate_code($key) && !isset($APAGES[$key]) && ($key == 'index' || $custom['category'] == $APAGES['index']->category || $custom['category'] == 'all')) {
            $APAGES[$key] = new page_collector($custom);

            if($APAGES[$key]->parent != '' && isset($APAGES[$APAGES[$key]->parent]) && $APAGES[$key]->priority >= 0) {
                $APAGES[$APAGES[$key]->parent]->childs[]= $key;
            }
        }
    }

    function show_apage($code) {
        global $APAGES;
        if(isset($APAGES[$code])) {
            $APAGES[$code]->doit();
        }
        elseif(isset($APAGES['404_error'])) {
            $APAGES['404_error']->doit();
        }
    }

    function get_apage($code) {
        global $APAGES;
        return (isset($APAGES[$code])) ? $APAGES[$code]: null;
    }

    function delete_apage($code) {
        global $APAGES;

        if(isset($APAGES[$code])) {
            unset($APAGES[$code]);
            
            return true;
        }
        else return false;
    }

    function update_apage($par) {
        if(isset($par['code']) && delete_apage($par['code'])) {
            add_apage($par);
        }
    }

    //Аналогичная сортировка подстраниц
    function apage_sort_child($a, $b) {
        global $APAGES;
        if($APAGES[$a]->priority == $APAGES[$b]->priority) return 0;
        elseif($APAGES[$a]->priority > $APAGES[$b]->priority) return 1;
        else return -1;
    }

    //Получаю текущий ключ через GET
    function get_real_key() {
        global $APAGES;

        $key = (isset($_GET['page'])) ? $_GET['page'] : 'index';

        return canone_code($key);
    }

    //Безопасный ключ
    function get_current_key() {
        global $APAGES;

        $key = get_real_key();

        if(!isset($APAGES[$key])) {
            $key = '404_error';
        }

        if(check_rule($APAGES[$key]->rule)) {
            return $key;
        }
        else {
            return '404_error';
        }
    }

    //Крошки
    function breadcrumbs($code=null, $r=' &rarr;') {
        global $PAGE, $APAGES;

        $key = (!$code) ? $PAGE->code : $code;

        $path = array();

        if(isset($APAGES[$key])) {
            $path[] = $key;

            while($APAGES[$key]->parent != '') {
                $key = $APAGES[$key]->parent;
                $path[] = $key;
            }

            $path = array_reverse($path);

            if(count($path)>=1) {
                echo '<div class="breadcrumbs">';
                echo '<a class="start-crumb" href="' . get_page_link('index') . '">';
                echo $APAGES['index']->title;
                echo '</a>';
                if(count($path) != 1 || $path[0] != 'index') {
                    echo $r;
                    foreach($path as $k=>$code) {
                        if(!isset($path[$k+1])) {
                            echo ' <span>' . $PAGE->title . '</span>';
                        }
                        else {
                            echo ' <a href="' . get_page_link($code) . '">'. $APAGES[$code]->title . "</a>$r";
                        }
                    }
                }
                echo '</div>';
            }
        }
    }

    //Рекурсивная навигация
    function apage_navigation($params = array()) {
        $custom = array(
            'parent'     => '',
            'depth'      => 3,
            'current'    => 0,
            'class'      => '',
            'double_max' => null
        );

        if(is_array($params)) $custom = set_merge($custom, $params);

        apage_navigation_child($custom);
    }

    function apage_navigation_child($params) {
        global $APAGES, $PAGE;

        echo '<ul' . (($params['class']) ? ' class="' .  $params['class'] . '"' : '') . ' data-level="' . $params['current'] . '">';

        if($params['double_max'] !== null && $params['double_max'] >= $params['current'] && $params['parent'] && count($APAGES[$params['parent']]->childs) > 0) {
            $key = $params['parent'];
            $item = $APAGES[$key];

            echo '<li class="item-' . $key . (($PAGE->code == $key) ? ' active' : '') . '">';
            echo '<a href="' . get_page_link($key) .'">' . $item->title . '</a>';
            echo '</li>';
        }

        foreach($APAGES as $key=>$item) {
            if($item->parent == $params['parent'] && $item->priority >= 0 && check_rule($item->rule)) {
                echo '<li class="item-' . $key . (($PAGE->code == $key) ? ' active' : '') . '">';
                echo '<a href="' . get_page_link($key) .'">' . $item->title . '</a>';
                if(count($item->childs) > 0 && $params['current'] <= $params['depth']) {
                    apage_navigation_child(array(
                        'current'    => $params['current'] + 1,
                        'depth'      => $params['depth'],
                        'parent'     => $key,
                        'class'      => 'subnav',
                        'double_max' => $params['double_max']
                    ));
                }
                echo '</li>';
            }
        }

        echo '</ul>';
    }

    //Получить код родителя от элемента с заданной глубиной (последний родитель на уровне 0)
    function apage_parent($code = null, $lvl = 0) {
        global $PAGE, $APAGES;

        $key = (!$code) ? $PAGE->code : $code;
        $path = array();

        if(isset($APAGES[$key])) {
            $path[] = $key;

            while($APAGES[$key]->parent != '') {
                $key = $APAGES[$key]->parent;
                $path[] = $key;
            }

            $path = array_reverse($path);
            if(isset($path[$lvl])) {
                return $path[$lvl];
            }
            else {
                $last = key(array_slice($last, -1, 1, true));
                return $path[$last];
            }
        }

        return null;
    }
?>