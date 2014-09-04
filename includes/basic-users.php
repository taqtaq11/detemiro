<?php
    //Получаю текущего пользователя
    $USER = null;

    ///Формирование массива прав пользователя
    function form_user_rules($user, $only = false) {
        global $DETDB;
        if(isset($user->groups_ID) && isset($user->rules)) {
            if($user->groups_ID && check_json($user->groups_ID)) {
                $user->groups_ID = json_decode($user->groups_ID, true);
            }
            else {
                $user->groups_ID = array();
            }
            if($user->rules && check_json($user->rules)) {
                $user->rules = json_decode($user->rules, true);
            }
            elseif($user->rules == '') $user->rules = array();
            if(!$only && $user->groups_ID) foreach($user->groups_ID as $group) {
                $rules = $DETDB->select('users_groups', 'rules', true, "WHERE ID='$group'");
                if($rules) {
                    $rules = json_decode($rules->rules, true);
                    foreach($rules as $item) {
                        if(!in_array($item, $user->rules)) {
                            $user->rules[] = $item;
                        }
                    }
                }
            }
        }
        return $user;
    }

    //Инициализровать текущего пользователя
    function int_user() {
        global $DETDB, $USER;

        if(check_login(true)) {
            $USER = $DETDB->select('users', 'ID, display_name, groups_ID, last_ip, rules', true, "WHERE ID='" . $_COOKIE['user_ID'] . "'");
            $USER->check = true;
        }
        else {
            $USER = (object) array(
                'ID'           => null,
                'display_name' => _('Гость'),
                'code'         => 'ghost',
                'groups_ID'    => '["2"]', //["public"]
                'last_ip'      => $_SERVER['REMOTE_ADDR'],
                'rules'        => ''
            );
            $USER->check = false;
        }

        $USER = form_user_rules($USER);
    }

    //Инициализация пользователя
    int_user();

    //Получаю текущего пользователя
    function current_user($cols = '') {
        global $USER, $DETDB;
        
        if($cols == '') {
            return $USER;
        }
        else {
            $obj = new stdClass();

            $cols = take_good_array($cols, true);

            foreach($USER as $key=>$item) {
                if(in_array($key, $cols)) {
                    $obj->$key = $item;
                }
            }

            if(count($cols) != count($obj)) {
                if($sec = $DETDB->select('users', $cols, true, "WHERE ID='{$USER->ID}'")) {
                    if($obj === null) $obj = new stdClass();
                    foreach($sec as $key=>$item) {
                        if(!isset($obj->$key)) {
                            $obj->$key = $item;
                        }
                    }
                }
            }

            if(count($cols) == 1 && isset($obj->$cols[0])) {
                $obj = $obj->$cols[0];
            }

            return $obj;
        }
    }

    //Получаю пользователя по ID/code
    function get_user($ID, $cols = '*', $only = false) {
        global $DETDB;

        $res = null;

        if(($param = set_ID($ID)) && ($res = $DETDB->select('users', $cols, true, "WHERE $param='$ID'"))) {
            $res = form_user_rules($res, $only);
        }
        return $res;
    }

    //Получаю пользователей по какому-то критерию (custom_where)
    function get_users($par, $only = false) {
        global $DETDB;

        if(is_object($par)) {
            $par = (array) $par;
        }
        
        $par['table'] = 'users';

        if($res = $DETDB->select($par)) {
            $L = count($res);
            for($i = 0; $i < $L; $i++) {
                if(isset($res[$i]->groups_ID) && check_json($res[$i]->groups_ID)) {
                    $res[$i]->groups_ID = json_decode($res[$i]->groups_ID, true);
                }
                if(isset($res[$i]->rules)) {
                    $res[$i] = form_user_rules($res, $only);
                }
            }
            return $res;
        }
        return null;
}

    //Добавить пользователя
    function add_user($par) {
        global $DETDB;

        if(is_merged($par)) {
            $par = (array) $par;

            if(isset($par['code'])) {
                $par['code'] = canone_code($par['code']);
                if(!validate_code($par['code'])) {
                    return false;
                }
            }
            else return false;

            if(!isset($par['reg_date'])) $par['reg_date'] = date('c');

            return ($DETDB->insert('users', $par));
        }

        return false;
    }

    //Обновить пользователя
    function update_user($ID, $par) {
        global $DETDB;

        if(is_merged($par)) {
            $par = (array) $par;

            if(isset($par['code'])) {
                $par['code'] = canone_code($par['code']);
                if(!validate_code($par['code'])) {
                    return false;
                }
            }

            $param = set_ID($ID);

            return ($param && $DETDB->update('users', $par, "WHERE $param='$ID'"));
        }

        return false;
    }

    ///Обновить пользователей
    function update_users($par, $cond) {
        global $DETDB;
        if(is_merged($par)) {
            $par = (array) $par;

            if(isset($par['code'])) {
                $par['code'] = canone_code($par['code']);
                if(!validate_code($par['code'])) {
                    return false;
                }
            }

            return ($DETDB->update('users', $par, $cond));
        }

        return false;
    }

    //Удаляю пользователя
    function delete_user($ID) {
        global $DETDB;

        return $DETDB->delete('users', $ID);
    }

    //Получаю группы пользователя по ID
    function get_user_group($ID, $fields='*') {
        global $DETDB;

        if($param = set_ID($ID)) {
            return $DETDB->select('users_groups', $fields, true, "WHERE $param='$ID'");
        }
        return null;
    }

    //Получаю группы
    function get_users_groups($cols='*', $cond = null) {
        global $DETDB;
        return $DETDB->select('users_groups', $cols, false, $cond);
    }

    //Добавляю группу
    function add_user_group($par) {
        global $DETDB;
        if(is_array($par)) {
            $par = (array) $par;

            if(isset($par['code'])) {
                $par['code'] = canone_code($par['code']);
                if(!validate_code($par['code'])) {
                    return false;
                }
            }
            else return false;

            return ($DETDB->insert('users_groups', $par));
        }
        return false;
    }

    //Обновляю группу
    function update_user_group($ID, $par) {
        global $DETDB;

        if(is_array($par)) {
            $par = (array) $par;

            if(isset($par['code'])) {
                $par['code'] = canone_code($par['code']);
                if(!validate_code($par['code'])) {
                    return false;
                }
            }

            $param = set_ID($ID);

            $cond = "WHERE $param='$ID'";

            return ($param && $DETDB->update('users_groups', $par, $cond));
        }

        return false;
    }

    //Обновить группы
    function update_users_groups($par, $cond) {
        global $DETDB;
        if(is_merged($par)) {
            $par = (array) $par;

            if(isset($par['code'])) {
                $par['code'] = canone_code($par['code']);
                if(!validate_code($par['code'])) {
                    return false;
                }
            }

            return ($DETDB->update('users_groups', $par, $cond));
        }

        return false;
    }


    //Удаляю группу
    function delete_user_group($ID) {
        global $DETDB;

        return $DETDB->delete('users_groups', $ID);
    }
?>