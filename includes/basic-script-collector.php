<?php
    //Объект с инфой о странице
    class scripts_collector {
        public $type;
        public $code;
        public $link;
        public $function;
        public $zone;
        public $priority;
        public $category;
        public $auto;
        function __construct($array) {
            if(is_merged($array)) {
                set_ref_merge($this, $array);
            }
        }
        public function doit() {
            global $PAGE;

            if(!$this->auto || check_auto($PAGE->code, $this->auto)) {
                if($this->link && url_exists($this->link)) {
                    echo "    ";
                    if($this->type == 'script') {
                        echo '<script src="' . $this->link . '"></script>';
                    }
                    elseif($this->type == 'style') {
                        echo '<link rel="stylesheet" href="' . $this->link . '" />';
                    }
                    else {
                        echo $this->link;
                    }
                    echo "\n";
                }
                if($this->function != null && ((is_string($this->function) && function_exists($this->function)) || is_callable($this->function))) {
                    call_user_func($this->function);
                }
            }
        }
    }
    $SCRIPTS = array();

    //Положить в коллектор
    function add_script($arr) {
        global $SCRIPTS, $APAGES;

        $custom = array(
            'code'     => '',
            'type'     => 'script',
            'link'     => '',
            'function' => '',
            'priority' => '30',
            'auto'     => '',
            'category' => 'public',
            'zone'     => ''
        );

        $custom = set_merge($custom, $arr);

        $key = $custom['code'] = canone_code($custom['code']);

        if(validate_code($key) && !isset($SCRIPTS[$key]) && ((isset($APAGES['index']) && $custom['category'] == $APAGES['index']->category) || $custom['category'] == 'all')) {
            $SCRIPTS[$key] = new scripts_collector($custom);
        }
    }

    function scripts_zone($zone) {
        global $SCRIPTS;

        foreach($SCRIPTS as $item) {
            if($item->zone == $zone) {
                $item->doit();
            }
        }
    }

    function show_script($code) {
        global $SCRIPTS;

        if(isset($SCRIPTS[$code])) {
            $SCRIPTS[$code]->doit();
        }
        else return null;
    }

    function get_script($code) {
        global $SCRIPTS;

        return (isset($SCRIPTS[$code])) ? $SCRIPTS[$code]: null;
    }

    function delete_script($code) {
        global $SCRIPTS;

        if(isset($SCRIPTS[$code])) {
            unset($SCRIPTS[$code]);
            
            return true;
        }
        else return false;
    }

    function update_script($par) {
        if(isset($par['code']) && delete_script($par['code'])) {
            add_script($par);
        }
    }

    //JQuery
    add_script(array(
        'code'     => 'jquery',
        'type'     => 'script',
        'link'     => BASE_URL . INCLUDES . '/js/jquery.min.js',
        'zone'     => 'header',
        'priority' => 2,
        'category' => 'all'
    ));
    add_script(array(
        'code'     => 'jquery_ui',
        'type'     => 'script',
        'link'     => BASE_URL . INCLUDES . '/js/jquery-ui.min.js',
        'zone'     => 'header',
        'priority' => 14,
        'category' => 'all'
    ));
    //Detlib
    add_script(array(
        'code'     => 'detlib',
        'type'     => 'script',
        'link'     => BASE_URL . INCLUDES . '/js/jquery.detlib.min.js',
        'zone'     => 'header',
        'priority' => 3,
        'category' => 'all'
    ));
?>