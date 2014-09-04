<?php
    //Константы БД
    define('DB_HOST', '');
    define('DB_NAME', '');
    define('DB_USER', '');
    define('DB_PASS', '');
    define('DB_PREFIX', '');

    //Админка, путь начинается от корня сайта (ABSPATH)
    define('ADMIN', 'admin');

    //Папка с модулями, путь начинается от корня сайта (ABSPATH)
    define('MODULES', 'modules');

    //Основной URL на сайте
    define('BASE_URL', 'http://site.com');

    //Язык
    define('LANG_LOCALE', 'ru_RU');
    define('LANG_DOMAIN', 'default');
    define('LANG_CHARSET', 'UTF-8');

    putenv('LANG=' . LANG_LOCALE);
    putenv('LC_ALL=' . LANG_LOCALE);
    setlocale(LC_ALL, LANG_LOCALE.'.utf8');
    bindtextdomain(LANG_DOMAIN, ABSPATH . '/language');
    bind_textdomain_codeset(LANG_DOMAIN, LANG_CHARSET);
    
    textdomain(LANG_DOMAIN);
?>