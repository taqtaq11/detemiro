<?php
    $active = get_option('active_modules');

    if($active == null) $active = array();

    $modules = scandir(ABSPATH . '/' . MODULES);

    foreach($modules as $dir) {
        $path = ABSPATH . '/' . MODULES . '/' . $dir;

        $key = null;
        if(is_dir($path) && file_exists($path . '/main.php') && ($key = array_multi_search($dir, $active, 0)) !== null && $active[$key][1] == true) {

            require_once($path . '/main.php');
        }
    }

    unset($active, $modules);
?>