<?php
    //Получать имя папки модуля
    function get_module_folder() {
        $lvl = 1;
        $current = realpath(debug_backtrace(FALSE, 5)[$lvl]['file']);

        $default = ABSPATH . '/' . MODULES;

        $new = str_replace($default, '', $current);

        $res = '';

        if($new != $current) {
            $i = 1;
            $L = count($new);
            while($new[$i] != '/' || $i <= $L) {
                $res .= $new[$i];
                $i++;
            }
        }

        return $res;
    }

    function get_module_template($file) {
        $MODULE_PATH = get_module_folder();

        if(file_exists(ABSPATH . '/' . MODULES . '/' . $MODULE_PATH . '/' . $file)) {
            require(ABSPATH . '/' . MODULES . '/' . $MODULE_PATH . '/' . $file);
        }
        else {
            echo 'Error loading of ' . $file;
        }
    }

    function get_module_file($file) {
        $MODULE_PATH = get_module_folder();

        if(file_exists(ABSPATH . '/' . MODULES . '/' . $MODULE_PATH . '/' . $file)) {
            return BASE_URL . '/' . MODULES . '/' . $MODULE_PATH . '/' . $file;
        }
        else {
            return 'Error loading of ' . $file;
        }
    }

    //Получить заданный список доступных модулей
    function get_modules_list($size = 10, $offset=0) {
        $modules = scandir(ABSPATH . '/' . MODULES);

        $res = array();
        $i = $o = 0;

        foreach($modules as $dir) {
            $path = ABSPATH . '/' . MODULES . '/' . $dir;
            if(is_dir($path) && file_exists($path . '/main.php')) {
                $o++;
                if($o > $offset) {
                    $i++;
                    if($i > $size) {
                        break;
                    }
                    $custom = array(
                        'name'        => $dir,
                        'description' => '',
                        'version'     => '',
                        'author'      => 'Unnamed',
                        'url'         => ''
                    );

                    if(file_exists($path . '/info.json')) {
                        $content = read_json($path . '/info.json', true);
                        if($content && is_assoc_array($content)) {
                            $custom = set_merge($custom, $content, false, array(
                                'clear' => true
                            ));
                        }
                    }

                    $custom['path'] = $dir;

                    $res[] = $custom;
                }
            }
        }

        return $res;
    }

    function get_modules_count() {
        return (count(scandir(ABSPATH . '/' . MODULES)) - 2);
    }

    //Обновить модули
    function update_modules($params) {
        $active = get_option('active_modules');

        $params = (array) $params;

        $modules = scandir(ABSPATH . '/' . MODULES);

        $res = array();

        if(count($params) > 0) foreach($params as $dir) {
            $dir = set_merge(array(null, false), $dir);

            $path = ABSPATH . '/' . MODULES . '/' . $dir[0];

            if(is_dir($path) && file_exists($path . '/main.php')) {
                $res[] = $dir;
                if(array_multi_search($dir[0], $active, 0) === null && file_exists($path . '/install.php')) {
                    require_once($path . '/install.php');
                }
            }
        }

        return update_option('active_modules', json_val_encode($res));
    }

    //Установка модуля
    function install_module($dir) {
        $active = get_option('active_modules');

        $path = ABSPATH . '/' . MODULES . '/' . $dir;

        if(is_dir($path) && file_exists($path . '/main.php') && array_multi_search($dir, $active, 0) === null) {
            $active[] = [$dir, false];
            if(file_exists($path . '/install.php')) {
                require_once($path . '/install.php');
            }
            return update_option('active_modules', json_val_encode($active));
        }

        return false;
    }

    //Активация модуля
    function activate_module($dir) {
        $active = get_option('active_modules');
        $path = ABSPATH . '/' . MODULES . '/' . $dir;

        if(is_dir($path) && file_exists($path . '/main.php')) {
            $key = null;
            if(($key = array_multi_search($dir, $active, 0)) !== null && $active[$key][1] == false);
            elseif(array_multi_search($dir, $active, 0) === null) {
                if(file_exists($path . '/install.php')) {
                    require_once($path . '/install.php');
                }
                $active[] = array($dir, true);
                $key = count($active) - 1;
            }
            else {
                $key = null;
            }
            if($key !== null) {
                $active[$key][1] = true;
                return update_option('active_modules', json_val_encode($active));
            }
        }

        return false;
    }

    //Деактивация модуля
    function deactivate_module($dir) {
        $active = get_option('active_modules');
        $path = ABSPATH . '/' . MODULES . '/' . $dir;

        $key = null;
        if(is_dir($path) && file_exists($path . '/main.php') && ($key = array_multi_search($dir, $active, 0)) !== null && $active[$key][1] == true) {
            $active[$key][1] = false;
            return update_option('active_modules', json_val_encode($active));
        }
        else {
            return false;
        }
    }

    //Полное удаление модуля
    function delete_module($dir, $full = false) {
        $active = get_option('active_modules');
        $path = ABSPATH . '/' . MODULES . '/' . $dir;

        if(is_dir($path) && file_exists($path . '/main.php')) {
            if(($key = array_multi_search($dir, $active, 0)) !== null) {
                unset($active[$key]);
                sort($active);

                if(file_exists($path . '/uninstall.php')) {
                    require_once($path . '/uninstall.php');
                }
                
                return update_option('active_modules', json_val_encode($active));
            }
            if($full) {
                return delete_folder($path);
            }
        }

        return false;
    }
?>