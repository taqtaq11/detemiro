<?php
    //Pages

    add_apage(array(
        'code'     => 'users_panel',
        'title'    => _('Пользователи'),
        'rule'     => 'admin_users',
        'category' => 'admin',
        'priority' => 20,
        'function' => function() {
            set_glob_content(array(
                'pagi'    => true,
                'handler' => 'handler_users_list',
                'limit'   => 15
            ));
            get_template('users/list.php');
        }
    ));

    add_apage(array(
        'code'     => 'user_info',
        'title'    => _('Информация о пользователе'),
        'rule'     => 'admin_users',
        'parent'   => 'users_panel',
        'category' => 'admin',
        'priority' => -1,
        'function' => function() {
            if(isset($_GET['user_id'])) {
                set_glob_content(array('body' => get_user($_GET['user_id'])));
            }
            get_template('users/info.php');
        }
    ));

    add_apage(array(
        'code'     => 'edit_user',
        'parent'   => 'users_panel',
        'title'    => _('Редактирование пользователя'),
        'function' => 'admin_edit_users',
        'rule'     => 'admin_users',
        'category' => 'admin',
        'priority' => -1
    ));

    function admin_edit_users() {
        get_template('users/edit.php');
    }

    add_apage(array(
        'code'     => 'user_groups',
        'parent'   => 'users_panel',
        'title'    => _('Группы пользователей'),
        'rule'     => 'admin_users',
        'category' => 'admin',
        'priority' => 10,
        'function' => function() {
            set_glob_content(array(
                'table' => 'users_groups',
                'pagi'  => true,
                'limit' => 20
            ));
            get_template('users/groups.php');
        }
    ));

    add_apage(array(
        'code'     => 'user_rules',
        'parent'   => 'users_panel',
        'title'    => _('Настройка прав'),
        'rule'     => 'admin_settings, admin_rules',
        'category' => 'admin',
        'priority' => 10,
        'function' => function() {
            set_glob_content(array(
                'body' => get_rules()
            ));
            get_template('users/rules.php');
        }
    ));

    //Others

    add_action(array(
        'code'     => 'ajax_update_rules',
        'category' => 'admin',
        'rule'     => 'admin_ajax, admin_settings',
        'function' => function($params=null) {
            if(isset($params['button']['input'])) {
                $params = $params['button']['input'];
            }
            if(isset($params['group']) || isset($params['user'])) {
                $res = array();
                foreach($params as $key=>$item) {
                    if($key == 'rule') {
                        foreach($item as $item) {
                            if(isset($item['on']) && $item['on']) {
                                $res[] = $item['value'];
                            }
                        }
                    }
                }
                if((isset($params['group']) && update_rules($params['group'], $res)) || (isset($params['user']) && update_rules($params['user'], $res, false))) {
                    echo ajax_make_res('success', 'Права успешно обновлены', 'Успех!');
                    die();
                }
            }
            echo ajax_make_res('error', 'Произошла неизвестная ошибка', 'Ошибка!');
        }
    ));

    add_action(array(
        'code'     => 'ajax_delete_rule',
        'category' => 'admin',
        'rule'     => 'admin_ajax, admin_settings',
        'function' => function($params=null) {
            if(isset($params['window']['pre_window']['value']) && $params['window']['pre_window']['type'] == 'code' && delete_rule($params['window']['pre_window']['value'])) {
                echo ajax_make_res('success', 'Право успешно удалено', 'Успех!');
            }
            else {
                echo ajax_make_res('error', 'Произошла неизвестная ошибка', 'Ошибка!');
            }
        }
    ));

    add_action(array(
        'code'     => 'ajax_add_rule',
        'category' => 'admin',
        'rule'     => 'admin_ajax, admin_settings',
        'function' => function($params=null) {
            if(isset($params['window']['input'])) {
                $params = $params['window']['input'];
            }
            if(isset($params['code']) && add_rule($params)) {
                echo ajax_make_res('reload', 'Право успешно добавлено', 'Успех!');
            }
            else {
                echo ajax_make_res('error', 'Поле кода обязательно для заполнения', 'Ошибка!');
            }
        }
    ));

    add_action(array(
        'code'     => 'edit_user',
        'function' => 'action_edit_user',
        'rule'     => 'admin_users',
        'zone'     => 'before_template',
        'category' => 'admin',
        'auto'     => 'edit_user'
    ));

    function action_edit_user() {
        global $PAGE;

        $current = (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) ? $_GET['user_id'] : null;

        if(!$current) {
            $PAGE->title = 'Добавить пользователя';
        }

        if(isset($_POST['action']) && $_POST['action'] == 'save') {
            $res = array(
                'login'        => '',
                'code'         => '',
                'display_name' => '',
                'mail'         => '',
                'groups_ID'    => array(),
                'rules'        => array()
            );
            $res = set_merge($res, $_POST);

            if($password = $_POST['password']) {
                $salt = random_salt();
                $password = crypt(md5($password), $salt);

                $res['password'] = $password;
                $res['salt']     = $salt;
            }

            if($res['login'] && $res['display_name'] && $res['mail'] && $res['code'] && count($res['groups_ID']) && (isset($res['password']) || $current)) {
                if($current) {
                    if(update_users($res, "WHERE ID='$current'")) {
                        push_output_message(array(
                            'title' => 'Обновлено!',
                            'text'  => 'Пользователь успешно обновлён',
                            'class' => 'alert alert-success'
                        ));
                    }
                    else {
                        push_output_message(array(
                            'title' => 'Ошибка!',
                            'text'  => 'Произошла неизвестная ошибка',
                            'class' => 'alert alert-danger'
                        ));
                    }
                    set_glob_content(array('body' => (object) $res));
                }
                else {
                    if(add_user($res)) {
                        push_output_message(array(
                            'title' => 'Добавлено!',
                            'text'  => 'Пользователь успешно добавлен',
                            'class' => 'alert alert-success'
                        ));
                    }
                    else {
                        push_output_message(array(
                            'title' => 'Ошибка!',
                            'text'  => 'Произошла неизвестная ошибка',
                            'class' => 'alert alert-danger'
                        ));
                    }
                }
            }
            else {
                push_output_message(array(
                    'title' => 'Ошибка!',
                    'text'  => 'Заполните все обязательные поля',
                    'class' => 'alert alert-danger'
                ));
                set_glob_content(array('body' => (object) $res));
            }
        }
        elseif(isset($_POST['action']) && $_POST['action'] == 'delete' && $current && delete_user($current)) {
            push_output_message(array(
                'title' => 'Удалено!',
                'text'  => 'Пользователь успешно удалён',
                'class' => 'alert alert-success'
            ));
        }
        elseif($current && $user = get_user($current, 'login, code, display_name, mail, groups_ID, rules', true)) {
            set_glob_content(array('body' => $user));
        }
    }

    add_action(array(
        'code'     => 'handler_users_list',
        'rule'     => 'admin_users',
        'category' => 'admin',
        'function' => function() {
            global $DETDB, $PAGE;

            $custom = array();

            $custom['all'] = $DETDB->count('users');

            $custom['body'] = get_users(array(
                'cols'   => 'ID, login, code, display_name, groups_ID',
                'offset' => $PAGE->content['offset'],
                'limit'  => $PAGE->content['limit']
            ));

            return $custom;
        }
    ));

    add_action(array(
        'code'     => 'ajax_delete_user',
        'function' => 'ajax_delete_user',
        'rule'     => 'admin_ajax, admin_users',
        'category' => 'admin',
        'function' => function($params=null) {
            if(isset($params['window']['pre_window']['value']) && $params['window']['pre_window']['type'] == 'ID' && delete_user($params['window']['pre_window']['value'])) {
                echo ajax_make_res('success', 'Пользователь успешно удалён', 'Успех!');
            }
            else {
                echo ajax_make_res('error', 'Произошла неизвестная ошибка', 'Ошибка!');
            }
        }
    ));

    add_action(array(
        'code'     => 'ajax_add_user_group',
        'rule'     => 'admin_ajax, admin_users',
        'category' => 'admin',
        'function' => function($params=null) {
            if(isset($params['window']['input'])) {
                $params = $params['window']['input'];
            }

            if($params !== null && isset($params['code']) && add_user_group($params)) {
                echo ajax_make_res('reload', "Группа {$params['code']} успешно добавлена", 'Успех!');
            }
            else {
                echo ajax_make_res('error', 'Проверьте правильность заполненных полей', 'Ошибка!');
            }
        }
    ));
    add_action(array(
        'code'     => 'ajax_get_user_group',
        'rule'     => 'admin_ajax, admin_users',
        'category' => 'admin',
        'function' => function($params=null) {
            if(isset($params['button']['pre']['value']) && $params['button']['pre']['type'] == 'ID') {
                $params = $params['button']['pre']['value'];
                if($res = get_user_group($params, 'code, name')) {
                    echo ajax_make_res(array(
                        'data' => $res
                    ));
                    die();
                }
            }
            echo ajax_make_res('error', 'Произошла неизвестная ошибка', 'Ошибка!');
        }
    ));
    add_action(array(
        'code'     => 'ajax_update_user_group',
        'rule'     => 'admin_ajax, admin_users',
        'category' => 'admin',
        'function' => function($params=null) {
            $ID = null;
            if(isset($params['button']['pre']['value']) && $params['button']['pre']['type'] == 'ID') {
                $ID = $params['button']['pre']['value'];
            }

            if(isset($params['window']['input'])) {
                $params = $params['window']['input'];
            }

            if($params !== null && $ID && update_user_group($ID, $params)) {
                echo ajax_make_res('reload', "Группа {$params['name']} успешно обновлена", 'Успех!');
            }
            else {
                echo ajax_make_res('error', 'Проверьте правильность заполненных полей', 'Ошибка!');
            }
        }
    ));
    add_action(array(
        'code'     => 'ajax_delete_user_group',
        'rule'     => 'admin_ajax, admin_users',
        'category' => 'admin',
        'function' => function($params=null) {
            if(isset($params['window']['pre_window']['value']) && $params['window']['pre_window']['type'] == 'ID' && delete_user_group($params['window']['pre_window']['value'])) {
                echo ajax_make_res('success', 'Группа успешно удалена', 'Успех!');
            }
            else {
                echo ajax_make_res('error', 'Произошла неизвестная ошибка', 'Ошибка!');
            }
        }
    ));
?>