<?php
    //Pages
    
    add_apage(array(
        'code'     => 'modules_panel',
        'title'    => _('Модули'),
        'rule'     => 'admin_modules, admin_settings',
        'category' => 'admin',
        'priority' => 15,
        'function' => function() {
            get_template('modules/panel.php');
        }
    ));

    //Others
    
    add_action(array(
        'code'     => 'ajax_update_modules',
        'rule'     => 'admin_ajax',
        'category' => 'admin',
        'function' => function($params=null) {
            if(isset($params['button']['input']['active'])) {
                $params = $params['button']['input'];
            }
            if(isset($params['active'])) {
                $res = array();
                foreach($params['active'] as $key=>$item) {
                    if(isset($item['on'])) $res[] = array($item['value'], $item['on']);
                }
                if(update_modules($res)) {
                    echo ajax_make_res('reload', 'Модули успешно обновлены', 'Успех!');
                    die();
                }
            }
            echo ajax_make_res('error', 'Произошла неизвестная ошибка', 'Ошибка!');
        }
    ));

    add_action(array(
        'code'     => 'ajax_deactivate_module',
        'rule'     => 'admin_ajax',
        'category' => 'admin',
        'function' => function($params=null) {
            if(isset($params['button']['pre']['value'])) {
                $params = $params['button']['pre']['value'];
            }
            if($params && deactivate_module($params)) {
                echo ajax_make_res('reload', "Модуль $params успешно деактивирован", 'Успех!');
            }
            else {
                echo ajax_make_res('error', 'Произошла неизвестная ошибка', 'Ошибка!');
            }
        }
    ));

    add_action(array(
        'code'     => 'ajax_activate_module',
        'rule'     => 'admin_ajax',
        'category' => 'admin',
        'function' => function($params=null) {
            if(isset($params['button']['pre']['value'])) {
                $params = $params['button']['pre']['value'];
            }
            if($params && activate_module($params)) {
                echo ajax_make_res('reload', "Модуль $params успешно активирован", 'Успех!');
            }
            else {
                echo ajax_make_res('error', 'Произошла неизвестная ошибка', 'Ошибка!');
            }
        }
    ));

    add_action(array(
        'code'     => 'ajax_install_module',
        'rule'     => 'admin_ajax',
        'category' => 'admin',
        'function' => function($params=null) {
            if(isset($params['button']['pre']['value'])) {
                $params = $params['button']['pre']['value'];
            }
            if($params && activate_module($params)) {
                echo ajax_make_res('reload', "Модуль $params успешно установлен", 'Успех!');
            }
            else {
                echo ajax_make_res('error', 'Произошла неизвестная ошибка', 'Ошибка!');
            }
        }
    ));

    add_action(array(
        'code'     => 'ajax_delete_module',
        'rule'     => 'admin_ajax',
        'category' => 'admin',
        'function' => function($params=null) {
            if(isset($params['button']['pre']['value'])) {
                $params = $params['button']['pre']['value'];
            }
            if($params && delete_module($params, false)) {
                echo ajax_make_res('reload', "Модуль $params удалён с сохранением данных", 'Успех!');
            }
            else {
                echo ajax_make_res('error', 'Произошла неизвестная ошибка', 'Ошибка!');
            }
        }
    ));

    add_action(array(
        'code'     => 'ajax_delete_full_module',
        'rule'     => 'admin_ajax',
        'category' => 'admin',
        'function' => function($params=null) {
            if(isset($params['button']['pre']['value'])) {
                $params = $params['button']['pre']['value'];
            }
            if($params && delete_module($params, true)) {
                echo ajax_make_res('reload', "Модуль $params полностью удалён", 'Успех!');
            }
            else {
                echo ajax_make_res('error', 'Произошла неизвестная ошибка', 'Ошибка!');
            }
        }
    ));
?>