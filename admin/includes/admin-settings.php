<?php
    //Pages
    add_apage(array(
        'code'     => 'settings',
        'title'    => 'Настройки',
        'rule'     => 'admin_settings',
        'category' => 'admin',
        'priority' => 5,
        'function' => function() {
            get_template('settings/panel.php');
        }
    ));
    
    add_action(array(
        'code'     => 'settings_main',
        'function' => 'action_settings_main',
        'rule'     => 'admin, admin_settings',
        'zone'     => 'before_template',
        'category' => 'admin',
        'auto'     => 'settings'
    ));
    
    function action_settings_main($pre = null) {
        $custom = array(
            'site_name'         => '',
            'current_template'  => '',
            'default_group'     => '',
            'login_agent'       => false,
            'login_ip'          => false,
            'cookie_login_live' => null
        );

        if(!isset($_POST['site_name'])) {
            $custom['site_name']         = get_option('site_name');
            $custom['current_template']  = get_option('current_template');
            $custom['default_group']     = get_option('default_group');
            $login                       = get_option('admin_check_login');
            $custom['login_ip']          = $login['login_ip'];
            $custom['login_agent']       = $login['login_agent'];
            $custom['cookie_login_live'] = get_option('cookie_login_live') / 60;
            set_glob_content(array(
                'body' => $custom
            ));
        }

        if(isset($_POST['site_name'])) {
            $data = ($pre) ? $pre : $_POST;
            $custom_new = set_merge($custom, $data, false, true);

            set_glob_content(array(
                'body' => $custom_new
            ));

            if($custom_new['site_name'] && $custom_new['current_template'] && $custom_new['default_group']) {
                $checker = array();
                if($custom['site_name'] != $custom_new['site_name']) {
                    $checker[] = update_option('site_name', $custom_new['site_name']);
                }
                if($custom['current_template'] != $custom_new['current_template']) {
                    $checker[] = update_option('current_template', $custom_new['current_template']);
                }
                if($custom['default_group'] != $custom_new['default_group']) {
                    $checker[] = update_option('default_group', $custom_new['default_group']);
                }
                if($custom['login_agent'] !== $custom_new['login_agent'] || $custom['login_ip'] !== $custom_new['login_ip']) {
                    $checker[] = update_option('admin_check_login', array(
                        'login_ip'    => ($custom_new['login_ip'] != false),
                        'login_agent' => ($custom_new['login_agent'] != false)
                    ));
                }
                if($custom['cookie_login_live'] != $custom_new['cookie_login_live']) {
                    $checker[] = update_option('cookie_login_live', $custom_new['cookie_login_live'] * 60);
                }

                $checker = array_merge($checker, actions_zone('settings_checking', $data));

                if(!in_array(false, $checker, true)) {
                    push_output_message(array(
                        'title' => 'Обновлено!',
                        'text'  => 'Поля успешно обновлены',
                        'class' => 'alert alert-success'
                    ));
                }
                else {
                    push_output_message(array(
                        'title' => 'Ошибка!',
                        'text'  => 'Поля заполнены некорректно',
                        'class' => 'alert alert-danger',
                        'type'  => 'error'
                    ));
                }
            }
            else {
                push_output_message(array(
                    'text'  => 'Поля не заполнены',
                    'title' => 'Ошибка!',
                    'class' => 'alert alert-warning',
                    'type'  => 'warning'
                ));
            }
        }
    }

    add_apage(array(
        'code'     => 'settings_php',
        'parent'   => 'settings',
        'title'    => 'Информация о PHP',
        'rule'     => 'admin_settings',
        'category' => 'admin',
        'priority' => 100,
        'function' => function() {
            get_template('settings/php_info.php');
        }
    ));

    add_apage(array(
        'code'     => 'options',
        'parent'   => 'settings',
        'title'    => 'Опции',
        'rule'     => 'admin_settings',
        'category' => 'admin',
        'priority' => 0,
        'function' => function() {
            set_glob_content(array(
                'table' => 'options',
                'pagi'  => true,
                'limit' => 10
            ));
            get_template('settings/options.php');
        }
    ));

    //Others
    
    add_action(array(
        'code'     => 'ajax_add_option',
        'rule'     => 'admin_ajax, admin_settings',
        'category' => 'admin',
        'function' => function($params=null) {
            if(isset($params['window']['input'])) {
                $params = $params['window']['input'];
            }

            if($params) foreach($params as $key=>$item) {
                if(is_object($item) || is_array($item)) {
                    $params[$key] = json_val_encode($item);
                }
            }

            if($params !== null && isset($params['code']) && add_option($params)) {
                echo ajax_make_res('reload', "Опция {$params['code']} успешно добавлена", 'Успех!');
            }
            else {
                echo ajax_make_res('error', 'Проверьте правильность заполненных полей', 'Ошибка!');
            }
        }
    ));
    add_action(array(
        'code'     => 'ajax_delete_option',
        'rule'     => 'admin_ajax, admin_settings',
        'category' => 'admin',
        'function' => function($params=null) {
            if(isset($params['window']['pre_window']['value']) && $params['window']['pre_window']['type'] == 'ID' && delete_option($params['window']['pre_window']['value'])) {
                echo ajax_make_res('success', 'Опция успешно удалена', 'Успех!');
            }
            else {
                echo ajax_make_res('error', 'Произошла неизвестная ошибка', 'Ошибка!');
            }
        }
    ));
    add_action(array(
        'code'     => 'ajax_update_option',
        'rule'     => 'admin_ajax, admin_settings',
        'category' => 'admin',
        'function' => function($params=null) {
            $ID = null;
            if(isset($params['button']['pre']['value']) && $params['button']['pre']['type'] == 'ID') {
                $ID = $params['button']['pre']['value'];
            }

            if(isset($params['window']['input'])) {
                $params = $params['window']['input'];
            }

            if($params) foreach($params as $key=>$item) {
                if(is_object($item) || is_array($item)) {
                    $params[$key] = json_val_encode($item);
                }
            }

            if($params !== null && $ID && update_option($ID, $params, true)) {
                echo ajax_make_res('reload', "Опция {$params['code']} успешно обновлена", 'Успех!');
            }
            else {
                echo ajax_make_res('error', 'Проверьте правильность заполненных полей', 'Ошибка!');
            }
        }
    ));
    add_action(array(
        'code'     => 'ajax_get_option',
        'rule'     => 'admin_ajax, admin_settings',
        'category' => 'admin',
        'function' => function($params=null) {
            if(isset($params['button'])) {
                $params = $params['button'];
            }
            if(isset($params['pre']['value']) && $params['pre']['type'] == 'ID') {
                $params = $params['pre']['value'];
                if($res = get_option($params, false, true)) {
                    echo ajax_make_res(array(
                        'data' => $res
                    ));
                    die();
                }
            }
            echo ajax_make_res('error', 'Произошла неизвестная ошибка', 'Ошибка!');
        }
    ));
?>