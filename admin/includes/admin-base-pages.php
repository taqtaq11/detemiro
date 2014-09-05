<?php
    //All basic pages
    add_apage(array(
        'code'     => 'index',
        'title'    => 'Главная',
        'function' => 'admin_index',
        'rule'     => 'admin_panel',
        'category' => 'admin',
        'priority' => 0
    ));

    add_apage(array(
        'code'     => '404_error',
        'title'    => '404. Страница не найдена',
        'function' => 'admin_404',
        'rule'     => 'admin_panel',
        'category' => 'admin',
        'priority' => -1
    ));

    add_apage(array(
        'code'     => 'login',
        'title'    => 'Вход в админ-панель',
        'function' => 'admin_show_login',
        'rule'     => 'public',
        'category' => 'admin',
        'priority' => -1,
        'skelet'   => false
    ));

    //All basic functions for pages
    function admin_show_login() {
        get_template('users/login.php');
    }

    function admin_index() {
        get_template('index.php');
    }

    function admin_404() {
        get_template('404.php');
    }
?>