<?php
    //Bootstrap
    add_script(array(
        'code'     => 'bootstrap_js',
        'type'     => 'script',
        'link'     => '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js',
        'zone'     => 'header',
        'priority' => 5,
        'category' => 'admin'
    ));

    //Подгонка элементов для бутстрепа
    add_script(array(
        'code'     => 'make_bootstrap_js',
        'type'     => 'script',
        'link'     => get_file('js/make_bootstrap.min.js'),
        'zone'     => 'header',
        'priority' => 4,
        'category' => 'admin'
    ));

    add_script(array(
        'code'     => 'bootstrap_css',
        'type'     => 'style',
        'link'     => '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css',
        'zone'     => 'header',
        'priority' => 5,
        'category' => 'admin'
    ));
    add_script(array(
        'code'     => 'bootstrap_theme',
        'type'     => 'style',
        'link'     => '//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css',
        'zone'     => 'header',
        'priority' => 6,
        'category' => 'admin'
    ));

    //Свои изменения
    add_script(array(
        'code'     => 'bootstrap_custom',
        'type'     => 'style',
        'link'     => get_file('css/dashboard.css'),
        'zone'     => 'header',
        'priority' => 10,
        'category' => 'admin'
    ));
?>