<?php
    //Объект с инфой о странице
    class actions_collector {
        public $code;
        public $function;
        public $rule;
        public $category;
        public $zone;
        public $priority;
        public $auto;
        function __construct($array) {
            if(is_merged($array)) {
                set_ref_merge($this, $array);
            }
        }
        public function doit($params = null) {
            global $PAGE;

            if(check_rule($this->rule) && (!$this->auto || check_auto($PAGE->code, $this->auto))) {
                actions_zone("before_{$this->code}");

                if($this->function != null && ((is_string($this->function) && function_exists($this->function)) || is_callable($this->function))) {
                    return call_user_func($this->function, $params);
                }

                actions_zone("after_{$this->code}");
            }
        }
    }
    $ACTIONS = array();

    //Положить в коллектор
    function add_action($arr) {
        global $ACTIONS, $APAGES;

        $custom = array(
            'code'     => '',
            'function' => '',
            'rule'     => 'admin',
            'category' => 'public',
            'priority' => 30,
            'zone'     => '',
            'auto'     => ''
        );
        $custom = set_merge($custom, $arr);

        $key = $custom['code'] = canone_code($custom['code']);

        if(validate_code($key) && !isset($ACTIONS[$key]) && ((isset($APAGES['index']) && $custom['category'] == $APAGES['index']->category) || $custom['category'] == 'all')) {
            $ACTIONS[$key] = new actions_collector($custom);
        }
    }

    //Зона хуков
    function actions_zone($zone, $par=null) {
        global $ACTIONS;

        $res = array();

        foreach($ACTIONS as $key=>$item) {
            if($item->zone == $zone) {
                $res[$key] = $item->doit($par);
            }
        }

        return $res;
    }

    //Сделать action
    function make_action($code, $params = null) {
        global $ACTIONS;

        if(isset($ACTIONS[$code])) {
            return $ACTIONS[$code]->doit($params);
        }
        else return false;
    }

    function get_action($code) {
        global $ACTIONS;

        return (isset($ACTIONS[$code])) ? $ACTIONS[$code]: null;
    }

    function delete_action($code) {
        global $ACTIONS;

        if(isset($ACTIONS[$code])) {
            unset($ACTIONS[$code]);
            
            return true;
        }
        else return false;
    }

    function update_action($par) {
        if(isset($par['code']) && delete_action($par['code'])) {
            add_action($par);
        }
    }

    add_action(array(
        'code'     => 'track_activity',
        'rule'     => 'public',
        'category' => 'all',
        'zone'     => 'before_template',
        'priority' => 1000,
        'function' => function() {
            if(check_login()) {
                $ID = current_user('ID');
                update_user($ID, array(
                    'last_activity' => date('c'),
                    'last_place'    => BASE_URL . $_SERVER['REQUEST_URI']
                ));
            }
        }
    ));
?>