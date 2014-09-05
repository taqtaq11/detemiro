<?php
    /*
    add_apage(array(
        'code'     => 'detblocks_panel',
        'title'    => 'DET-блоки',
        'rule'     => 'admin_detblocks',
        'category' => 'admin',
        'priority' => 6,
        'function' => function() {
            get_template('detblocks/panel.php');
        }
    ));

    add_apage(array(
        'code'     => 'detblocks_types',
        'title'    => 'Типы контента',
        'rule'     => 'admin_detblocks',
        'category' => 'admin',
        'priority' => 3,
        'parent'   => 'detblocks_panel',
        'function' => function() {
            set_glob_content(array(
                'table' => 'detblocks_types',
                'pagi'  => true,
                'limit' => 10
            ));
            get_template('detblocks/types.php');
        }
    ));

    add_apage(array(
        'code'     => 'detblocks_type_add',
        'title'    => 'Добавить DET-блок',
        'rule'     => 'admin_detblocks',
        'category' => 'admin',
        'priority' => -1,
        'parent'   => 'detblocks_panel',
        'function' => function() {
            get_template('detblocks/type_add.php');
        }
    ));

    add_apage(array(
        'code'     => 'detblocks_categoreis',
        'title'    => 'Категории контента',
        'rule'     => 'admin_detblocks',
        'category' => 'admin',
        'priority' => 5,
        'parent'   => 'detblocks_panel',
        'function' => function() {
            get_template('detblocks/categories.php');
        }
    ));

    add_apage(array(
        'code'     => 'detblocks_fields',
        'title'    => 'Поля',
        'rule'     => 'detblocks',
        'category' => 'admin',
        'priority' => 7,
        'parent'   => 'detblocks_panel',
        'function' => function() {
            get_template('detblocks/fields.php');
        }
    ));

    add_apage(array(
        'code'     => 'detblocks_content',
        'title'    => 'Контент',
        'rule'     => 'detblocks',
        'category' => 'admin',
        'priority' => 7,
        'parent'   => 'admin_detblocks_panel',
        'function' => function() {
            get_template('detblocks/content.php');
        }
    ));

    add_action(array(
        'code'     => 'action_detblocks_type',
        'rule'     => 'admin_detblocks',
        'category' => 'admin',
        'auto'     => 'detblocks_type_add',
        'function' => 'action_detblocks_type',
        'zone'     => 'before_template'
    ));

    function action_detblocks_type() {
        global $DETDB;

        $current = (isset($_GET['block_id']) && is_numeric($_GET['block_id'])) ? $_GET['block_id'] : null;

        $block = array(
            'code' => '',
            'name' => ''
        );

        if($current) {
            
        }

        if(isset($_POST['save'])) {
            $block = set_merge($block, $_POST);
            set_glob_content((object) $block);

            if($ID = add_detblock($block)) {
                push_output_message(array(
                    'text'  => "DET-блок {$block['code']} успешно добавлен!<br />Создана таблица {$DETDB->prefix}detblocks_content_{$ID}",
                    'title' => 'Успех!',
                    'class' => 'alert alert-success',
                    'type'  => 'success'
                ));
            }
            else {
                push_output_message(array(
                    'text'  => 'Произошла неизвестная ошибка',
                    'title' => 'Ошибка!',
                    'class' => 'alert alert-danger',
                    'type'  => 'error'
                ));
            }
        }
        else {
            set_glob_content((object) $block);
        }
    }

    add_action(array(
        'code'     => 'ajax_delete_detblock',
        'rule'     => 'admin_detblocks',
        'category' => 'admin',
        'function' => function($params) {
            if(isset($params['window']['pre_window']['value']) && $params['window']['pre_window']['type'] == 'ID' && delete_detblock($params['window']['pre_window']['value'])) {
                echo ajax_make_res('success', 'Блок и связанные с ним записи успешно удалены', 'Успех!');
            }
            else {
                echo ajax_make_res('error', 'Произошла неизвестная ошибка', 'Ошибка!');
            }
        }
    ));
    */
?>