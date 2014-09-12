<?php
    //Генерирую хеш заданной длины
    function random_hash($L=15) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789!@#$%^*()';
        $hash = '';
        $C = strlen($chars) - 1;

        while(strlen($hash) < $L) {
           $hash.= $chars[mt_rand(0, $C)];  
        }

        return $hash;
    }    

    //Случайная соль
    function random_salt($it = 10) {
        return '$2a$' . $it . '$' . substr(str_replace('+', '.', base64_encode(pack('N4', mt_rand(), mt_rand(), mt_rand(), mt_rand()))), 0, 22) . '$';
    }

    //Проверка прав
    function check_rule($rule='') {
        global $DETDB, $USER;

        $t = true;

        if($rule) {
            $rule = take_good_array($rule);
            foreach($rule as $item) {
                if(!in_array($item, $USER->rules)) {
                    $t = false;
                }
            }
        }
        else {
            $t = false;
        }

        return ($t || in_array('admin', $USER->rules));
    }

    //Проверку на правильный вход
    function check_login($full = false) {
        global $USER, $DETDB;

        if($full) {
            $t = false;

            if(isset($_COOKIE['user_ID']) && isset($_COOKIE['user_hash'])) {
                $check_plus = get_option('admin_check_login');
                $cond = array(
                    array(
                        'param' => 'ID',
                        'value' => $_COOKIE['user_ID']
                    ),
                    array(
                        'param' => 'hash',
                        'value' => $_COOKIE['user_hash']
                    )
                );
                if(isset($check_plus['login_ip']) && $check_plus['login_ip']) {
                    $cond[] = array(
                        'param' => 'last_ip',
                        'value' => $_SERVER['REMOTE_ADDR']
                    );
                }
                if(isset($check_plus['login_agent']) && $check_plus['login_agent']) {
                    $cond[] = array(
                        'param' => 'last_agent',
                        'value' => $_SERVER['HTTP_USER_AGENT']
                    );
                }

                if($DETDB->select('users', 'ID', true, $cond)) {
                    $t = true;
                }
                else {
                    destroy_cookie('user_ID');
                    destroy_cookie('user_hash');
                }
            }

            return $t;
        }
        else {
            return (isset($USER->check) && $USER->check);
        }
    }

    //Права пользователей и групп

    //Добавляю право
    function add_rule($arr) {
        if(isset($arr['code']) || $arr) {
            $value = array(
                'code' => '',
                'desc' => ''
            );
            if(!is_array($arr)) $value['code'] = $arr;
            else {
                $value = set_merge($value, $arr);
            }

            $rules = get_option('rules_all', true);

            $rules_search = get_rules();
            $rules_search = array_merge($rules_search[0], $rules_search[1]);

            if(!is_array($rules)) {
                $rules = array();
            }

            $value['code'] = canone_code($value['code']);

            $t = true;
            foreach($rules_search as $item) if($value['code'] == $item['code']) {
                $t = false;
            }

            if($t) {
                $rules[] = $value;
                return update_option('rules_all', json_val_encode($rules));
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }

    //Удаляю право
    function delete_rule($ID) {
        $rules = get_option('rules_all', true);
        $param = set_ID($ID);

        if($rules && is_array($rules) && $param) {
            $L = count($rules);
            $new = array();

            for($i=0; $i<$L; $i++) {
                if($param == 'ID' && ($ID-1) != $i || $param == 'code' && $ID != $rules[$i]['code']) {
                    $new[] = $rules[$i]; 
                }
            }

            return update_option('rules_all', json_val_encode($new));
        }
        else {
            return false;
        }
    }

    //Обновить права группы или пользователя
    function update_rules($ID, $arr, $group = true) {
        global $DETDB;

        if(check_json($arr)) $arr = json_decode($arr, true);

        if(($param = set_ID($ID)) && is_jsoned($arr)) {
            $custom = array();

            foreach($arr as $item) {
                if(is_string($item)) {
                    $custom[] = $item;
                }
            }

            $custom = json_val_encode($custom);
            if($group) {
                return $DETDB->update('users_groups',  'rules', $custom, "WHERE $param='$ID'");
            }
            else {
                return $DETDB->update('users', 'rules', $custom, "WHERE $param='$ID'");
            }
        }
        return false;
    }

    //Устанавливаю право для группы/пользователя
    function give_rule($rule, $ID, $group = true) {
        global $DETDB;

        if($par = set_ID($ID)) {
            if($group) {
                $rules = get_user_group($ID, 'rules');
            }
            else {
                $rules = get_user($ID, 'rules');
            }

            if($rules) {
                $rules = $rules->rules;
                $rules = insert_json($rule, $rules);

                if($group) {
                    return $DETDB->update('users_groups', 'rules', $rules, "WHERE $par='$ID'");
                }
                else {
                    return $DETDB->update('users', 'rules', $rules, "WHERE $par='$ID'");
                }
            }
        }
        return false;
    }

    //Забрать право
    function take_rule($rule, $ID, $group = true) {
        global $DETDB;

        if($par = set_ID($ID)) {
            if($group) {
                $rules = get_user_group($ID, 'rules');
            }
            else {
                $rules = get_user($ID, 'rules');
            }

            if($rules) {
                $rules = $rules->rules;
                $rules = delete_json($rule, $rules);

                if($group) {
                    return $DETDB->update('users_groups', 'rules', $rules, "WHERE $par='$ID'");
                }
                else {
                    return $DETDB->update('users', 'rules', $rules, "WHERE $par='$ID'");
                }
            }
        }
        return false;
    }

    //Получаю список всех прав
    function get_rules() {
        $default = array();

        $value = array(
            'code' => '',
            'desc' => ''
        );

        $rules = get_option('rules_all', true);

        if(!$rules || !is_array($rules)) {
            $rules = array();
        }

        $default[] = array(
            'code' => 'admin',
            'desc' => 'Абсолютные права'
        );
        $default[] = array(
            'code' => 'admin_panel',
            'desc' => 'Просмотр панели'
        );
        $default[] = array(
            'code' => 'admin_ajax',
            'desc' => 'Админские AJAX-запросы'
        );
        $default[] = array(
            'code' => 'admin_settings',
            'desc' => 'Редактирование настроек'
        );
        $default[] = array(
            'code' => 'admin_detblocks',
            'desc' => 'Контроль DET-блоков'
        );
        $default[] = array(
            'code' => 'admin_users',
            'desc' => 'Редактирование пользователей'
        );
        $default[] = array(
            'code' => 'admin_modules',
            'desc' => 'Редактирование модулей'
        );
        $default[] = array(
            'code' => 'public',
            'desc' => 'Публичный доступ'
        );
        $default[] = array(
            'code' => 'ghost',
            'desc' => 'Гостевой режим'
        );

        return array($default, $rules);
    }

    //Безопасный ввод
    function secure_text($str, $param = null) {
        $custom = array(
            'str'          => $str,
            'clear'         => false,
            'html'          => true,
            'basic_remove'  => 'script, iframe, applet',
            'custom_remove' => ''
        );
        if(is_merged($param)) $custom = set_merge($custom, $param);

        if($custom['clear']) {
            $custom['str'] = strip_tags($custom['str']);
        }
        else {
            if(!$custom['html']) {
                $custom['str'] = htmlentities($custom['str'], ENT_HTML5, 'UTF-8');
            }

            $removes = array();

            if($custom['custom_remove']) {
                $removes = array_merge($removes, take_good_array($custom['custom_remove'], true));
            }
            if($custom['basic_remove']) {
                $removes = array_merge($removes, take_good_array($custom['basic_remove'], true));
            }
            $removes = array_unique($removes);
            if(count($removes)) {
                foreach($removes as $tag) {
                    $custom['str'] = preg_replace("/<$tag.*?\/$tag>/i",'', $custom['str']);
                }
            }
        }

        return $custom['str'];
    }

    function secure_loop($obj, $param = null) {
        if(is_jsoned($obj)) {
            $copy = $obj;
            foreach($copy as &$item) {
                $item = secure_text($item, $param);
            }
            $obj = $copy;
        }
        return $obj;
    }

    //Remote Actions
    function check_remote_key($key='') {
        global $DETDB;
        $L = strlen($key);
        if($L>=16 && $L<=20 && ($res = $DETDB->select('remote_keys', 'ID', true, "WHERE key_value='$key'"))) {
            return true;
        }
        return false;
    }

    function get_remote_key_rules($key='') {
        global $DETDB, $CONNECT;
        if(!$key) $key = $CONNECT->key;
        if($key && $res = $DETDB->select('remote_keys', 'rules', true, "WHERE key_value='$key'")) {
            return $res->rules;
        }
        else {
            return '';
        }
    }

    function generate_remote_key() {
        return random_hash(rand(16,20));
    }

    function get_remote_key($ID) {
        global $DETDB;
        if($res = $DETDB->select('remote_keys', '*', true, "WHERE ID=$ID")) {
            if(check_json($res->rules)) {
                $res->rules = json_decode($res->rules, true);
            }
            return $res;
        }
        return null;
    }

    function add_remote_key($key) {
        global $DETDB;
        if(is_object($key)) {
            $key = (array) $key;
        }
        if(is_merged($key) && isset($key['key_value'])) {
            $L = strlen($key['key_value']);

            $custom = set_merge(array(
                'name'      => '',
                'key_value' => '',
                'rules'     => ''
            ), $key);

            if($L>=16 && $L<=20) {
                if(preg_match('/^[abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789!@#$%^*()]{16,20}$/i', $custom['key_value'])) {
                    if($DETDB->insert('remote_keys', $custom)) {
                        return true;
                    }
                    else {
                        push_output_message(array(
                            'title' => 'Ошибка!',
                            'text'  => 'Произошла неизвестная ошибка, возможно, ключ повторяется',
                            'class' => 'alert alert-danger'
                        ));
                    }
                }
                else {
                    push_output_message(array(
                        'title' => 'Ошибка ключа!',
                        'text'  => 'Воспользуйтесь генератором ключей',
                        'class' => 'alert alert-danger'
                    ));
                }
            }
            else {
                push_output_message(array(
                    'title' => 'Ошибка!',
                    'text'  => 'Ключ должен иметь длину от 16 до 20 символов',
                    'class' => 'alert alert-danger'
                ));
            }
        }
        return false;
    }

    function update_remote_key($ID, $key) {
        global $DETDB;
        if(is_object($key)) {
            $key = (array) $key;
        }
        if(is_merged($key) && isset($key['key_value'])) {
            $L = strlen($key['key_value']);

            $custom = set_merge(array(
                'name'      => '',
                'key_value' => '',
                'rules'     => ''
            ), $key);

            if($L>=16 && $L<=20) {
                if(preg_match('/^[abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789!@#$%^*()]{16,20}$/i', $custom['key_value'])) {
                    if($DETDB->update('remote_keys', $custom, "WHERE ID=$ID")) {
                        return true;
                    }
                    else {
                        push_output_message(array(
                            'title' => 'Ошибка!',
                            'text'  => 'Произошла неизвестная ошибка, возможно, ключ повторяется',
                            'class' => 'alert alert-danger'
                        ));
                    }
                }
                else {
                    push_output_message(array(
                        'title' => 'Ошибка ключа!',
                        'text'  => 'Воспользуйтесь генератором ключей',
                        'class' => 'alert alert-danger'
                    ));
                }
            }
            else {
                push_output_message(array(
                    'title' => 'Ошибка!',
                    'text'  => 'Ключ должен иметь длину от 16 до 20 символов',
                    'class' => 'alert alert-danger'
                ));
            }
        }
        return false;
    }

    function delete_remote_key($ID) {
        global $DETDB;

        if(is_numeric($ID)) {
            return $DETDB->delete('remote_keys', $ID);
        }
        return false;
    }
?>