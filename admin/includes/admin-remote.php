<?php
    add_apage(array(
        'code'     => 'remote_keys',
        'title'    => 'Ключи доступа',
        'rule'     => 'admin_settings',
        'category' => 'admin',
        'parent'   => 'settings',
        'priority' => 35,
        'function' => function() {
            set_glob_content(array(
                'table' => 'remote_keys',
                'cols'  => '*',
                'pagi'  => true,
                'limit' => 30
            ));
            get_template('remote/panel.php');
        }
    ));

    add_apage(array(
        'code'     => 'remote_keys_edit',
        'title'    => 'Добавить ключ доступа',
        'rule'     => 'admin_settings',
        'category' => 'admin',
        'parent'   => 'remote_keys',
        'priority' => -1,
        'function' => function() {
            get_template('remote/add.php');
        }
    ));

    add_action(array(
        'code'     => 'action_remote_keys_edit',
        'rule'     => 'admin_settings',
        'category' => 'admin',
        'auto'     => 'remote_keys_edit',
        'function' => 'action_remote_keys_edit',
        'zone'     => 'before_template'
    ));

    function action_remote_keys_edit() {
        global $PAGE;

        $current = (isset($_GET['key_id']) && is_numeric($_GET['key_id'])) ? $_GET['key_id'] : null;

        $temp = $key = (object) array(
            'name'      => '',
            'key_value' => generate_remote_key(),
            'rules'     => array()
        );

        if($current && $res = get_remote_key($current)) {
            $key = set_merge($key, $res);
            $PAGE->title = 'Обновить ключ';
        }
        else {
            $current = null;
        }

        if(isset($_POST['save'])) {
            $key = set_merge($key, $_POST);

            if($key->name && $key->key_value && is_array($key->rules) && count($key->rules) > 0) {
                if($current) {
                    if(update_remote_key($current, $key)) {
                        push_output_message(array(
                            'text'  => 'Ключ успешно обновлён',
                            'title' => 'Успех!',
                            'class' => 'alert alert-success',
                            'type'  => 'success'
                        ));
                        $key->ID = $current;
                        set_glob_content(array('body' => $key));
                        return true;
                    }
                }
                elseif(add_remote_key($key)) {
                    push_output_message(array(
                        'text'  => 'Ключ успешно добавлен',
                        'title' => 'Успех!',
                        'class' => 'alert alert-success',
                        'type'  => 'success'
                    ));
                    set_glob_content(array('body' => $temp));
                    return true;
                }
                else {
                    set_glob_content(array('body' => $key));
                    return false;
                }
            }
            else {
                push_output_message(array(
                    'title' => 'Ошибка!',
                    'text'  => 'Заполните все поля (имя, ключ, хотя одно право)',
                    'class' => 'alert alert-danger'
                ));
            }
        }

        set_glob_content(array('body' => $key));

        return true;
    }

    add_action(array(
        'code'     => 'ajax_generate_remote_key',
        'rule'     => 'admin_settings',
        'category' => 'admin',
        'function' => function($param) {
            echo generate_remote_key();
        }
    ));

    add_action(array(
        'code'     => 'ajax_delete_remote_key',
        'rule'     => 'admin_settings',
        'category' => 'admin',
        'function' => function($params) {
            if(isset($params['window']['pre_window']['value']) && $params['window']['pre_window']['type'] == 'ID' && delete_remote_key($params['window']['pre_window']['value'])) {
                echo ajax_make_res('success', 'Ключ успешно удален', 'Успех!');
            }
            else {
                echo ajax_make_res('error', 'Произошла неизвестная ошибка', 'Ошибка!', '', true);
            }
        }
    ));
?>