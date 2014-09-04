<?php
    class option {
        public $ID    = null;
        public $code  = null;
        public $name  = null;
        public $value = null;
        function __construct($par=null, $unset_ID=false) {
            if($par && (is_merged($par))) {
                set_ref_merge($this, $par);
                if($unset_ID) unset($this->ID);
            }
        }
    }

    //Получаю опцию
    function get_option($opt, $arr=true, $full=false) {
        global $DETDB;

        $param = set_ID($opt);

        $cols = ($full) ? '*' : 'value';

        $res = null;

        if($param && $res = $DETDB->select('options', $cols, true, "WHERE $param='$opt'")) {
            $value = $res->value;

            if(check_json($value)) {
                $value = json_decode($value, $arr);

                if($arr && !is_array($value)) {
                    $value= array($value);
                }
            }

            if($full) {
                $res->value = $value;
            }
            else {
                $res = $value;
            }
        }
        return $res;
    }

    //Обновляю опцию
    function update_option($ID, $par, $full=false) {
        global $DETDB;

        if($param = set_ID($ID)) {
            if($full && is_merged($par)) {
                $custom = (array) new option($par, true);

                $custom['code'] = canone_code($custom['code']);

                $sett = ($param == 'ID') ? $ID : null;

                if(validate_code($custom['code'])) {
                    return ($DETDB->update('options', $custom, "WHERE $param='$ID'"));
                }
            }
            else {
                return ($DETDB->update('options', 'value', $par, "WHERE $param='$ID'"));
            }
        }

        return false;
    }

    //Добавляю опцию
    function add_option($params) {
        global $DETDB;

        if(is_array($params)) {
            $custom = (array) new option($params, true);

            $custom['code'] = canone_code($custom['code']);

            if(validate_code($custom['code'])) {
                return ($DETDB->insert('options', $custom));
            }
        }

        return false;
    }

    //Удалить опцию
    function delete_option($ID) {
        global $DETDB;

        return $DETDB->delete('options', $ID);
    }
?>