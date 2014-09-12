<?php
    add_action(array(
        'code'     => 'check_logout',
        'rule'     => 'public',
        'category' => 'admin',
        'zone'     => 'before_form_page',
        'priority' => '-3',
        'function' => function($par = false) {
            if((isset($_POST['logout']) || $par)  && check_login()) {
                destroy_cookie('user_ID');
                destroy_cookie('user_hash');

                int_user();
                
                if(!$par) return make_action('check_login');
                else return true;

            }
            return false;
        }
    ));

    add_action(array(
        'code'     => 'ajax_logout',
        'rule'     => 'public',
        'category' => 'admin',
        'function' => function() {
            if(make_action('check_logout', true)) {
                echo ajax_make_res('reload', 'Вы успешно вышли', 'Успех!');
            }
            else echo ajax_make_res('error', 'По неизвестным причинам произошла ошибка', 'Ошибка!');
        }
    ));

    add_action(array(
        'code'     => 'check_login',
        'function' => 'action_check_login',
        'rule'     => 'public',
        'category' => 'admin',
        'zone'     => 'before_form_page',
        'priority' => '-1'
    ));

    add_action(array(
        'code'     => 'login',
        'function' => 'action_login',
        'rule'     => 'public',
        'category' => 'admin',
        'auto'     => 'login',
        'zone'     => 'before_template',
        'priority' => '-2'
    ));

    add_action(array(
        'code'     => 'update_note',
        'function' => 'action_update_note',
        'rule'     => 'admin, admin-note',
        'category' => 'admin',
        'auto'     => 'index',
        'zone'     => 'before_template'
    ));

    function action_check_login() {
        global $PAGE, $APAGES;
        if(!check_login()) {
            if($PAGE->code != 'login') {
                setcookie('from_page', get_real_key(), time() + get_option('cookie_login_live'), '/');
                replace_page('login');
            }
            return false;
        }
        elseif(!check_rule($APAGES['index']->rule) && isset($APAGES[get_real_key()]) && !check_rule($APAGES[get_real_key()]->rule)) {
            if(get_real_key() == 'index' || isset($APAGES[get_real_key()])) foreach($APAGES as $key=>$item) {
                if($key != 'login' && $key != '404_error' && $item->category == $APAGES['index']->category && !$item->parent && check_rule($item->rule)) {
                    replace_page($key);
                    return false;
                }
            }

            destroy_cookie('user_ID');
            destroy_cookie('user_hash');

            replace_page('login');

            push_output_message(array(
                'text'  => 'У вас недостаточно прав для доступа к админ-панели',
                'title' => 'Ошибка!',
                'class' => 'alert alert-danger',
                'type'  => 'error'
            ));

            return false;
        }
        return true;
     }

    function action_login() {
        global $DETDB;

        if(check_login() && action_check_login()) {
            replace_page('index');
        }

        if(isset($_POST['form_login'])) {
            $checker = actions_zone('login_check');

            if(!in_array(false, $checker, true)) {
                $login = $_POST['form_login'];

                if($user = $DETDB->select('users', 'ID, login, password, salt', true, "WHERE login='$login'")) {
                    $password = md5($_POST['form_password']);

                    if(crypt($password, $user->salt) == $user->password) {
                        setcookie('user_ID', $user->ID, time() + get_option('cookie_login_live'), '/');

                        $hash = random_hash(10);

                        setcookie('user_hash', $hash, time() + get_option('cookie_login_live'), '/');

                        if($DETDB->update('users', array(
                                                'hash'          => $hash,
                                                'last_ip'       => $_SERVER['REMOTE_ADDR'],
                                                'last_activity' => date('c'),
                                                'last_agent'    => $_SERVER['HTTP_USER_AGENT']
                                                ),
                                                "WHERE ID='" . $user->ID . "'"
                                            ))
                        {
                            $_COOKIE['user_ID']   = $user->ID;
                            $_COOKIE['user_hash'] = $hash;

                            $url = (isset($_COOKIE['from_page'])) ? $_COOKIE['from_page'] : 'index';
                            destroy_cookie('from_page');

                            int_user();

                            if(make_action('check_login')) replace_page($url);
                        }
                    }
                }
                if(!$user || !isset($hash)) {
                    push_output_message(array(
                        'text'  => 'Неверный логин или пароль',
                        'title' => 'Ошибка!',
                        'class' => 'alert alert-danger',
                        'type'  => 'error'
                    ));
                }
            }
        }
    }

    add_action(array(
        'code'     => 'ajax_update_note',
        'rule'     => 'admin, admin_settings',
        'category' => 'admin',
        'function' => function($par=null) {
            if($par) {
                $par = $par['button']['input'];
            }
            if(isset($par['note']) && action_update_note($par)) {
                echo ajax_make_res('success', 'Заметка успешно обновлена', 'Обновено!');
            }
            else {
                echo ajax_make_res('error', 'У вас недостаточно прав');
            }
        }
    ));

    function action_update_note($par=null) {
        if(is_ajax() || isset($_POST['note'])) {
            $note = (isset($par['note'])) ? $par['note'] : ((isset($_POST['note'])) ? $_POST['note'] : '');
            $note = secure_text($note);
            if((isset($par['note']) || isset($_POST['note'])) && check_rule('admin_settings') && update_option('admin_notes', $note)) {
                if(!is_ajax()) push_output_message(array(
                    'title' => 'Обновлено!',
                    'text'  => 'Заметка успешно обновлена',
                    'class' => 'alert alert-success'
                ));
                return true;
            }
            else {
                if(!is_ajax()) push_output_message(array(
                    'title' => 'Ошибка!',
                    'text'  => 'У вас недостаточно прав для правки публичной заметки',
                    'class' => 'alert alert-danger'
                ));
                return false;
            }
        }
        return false;
    }
?>