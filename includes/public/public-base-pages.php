<?php
    //All basic pages
    add_apage(array(
        'code'     => 'index',
        'parent'   => '0',
        'title'    => 'Главная страница',
        'function' => 'show_index_page',
        'rule'     => 'public',
        'priority' => 0
    ));

    add_apage(array(
        'code'     => '404_error',
        'parent'   => '0',
        'title'    => 'Страница не найдена',
        'function' => 'show_404_page',
        'rule'     => 'public',
        'priority' => 0
    ));

    function show_index_page() {
        get_template('index.php');
    }

    function show_404_page() {
        echo '404';
    }
?>