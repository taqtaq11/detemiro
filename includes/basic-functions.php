<?php
    //Коды для БД (code)

    //Проверяю свободность кода, $сщтв - исключения одного ID
    function check_code($table, $value, $par = 'code', $ID = null) {
        global $DETDB;

        $plus = '';

        if($ID && is_numeric($ID)) {
            $plus = " AND ID != '$ID'";
        }
        elseif($ID && $query = $DETDB->custom_where($ID)) {
            if($res = $DETDB->select($table, 'code', false, $query)) {
                if(count($res) == 1 && $res[0]->code == $value) {
                    return false;
                }
            }
        }

        if($res = $DETDB->select($table, 'ID', false, "WHERE $par='$value'$plus")) {
            $L = count($res);
            for($i=0;$i<$L;$i++) {
                $res[$i] = $res[$i]->ID;
            }
            if($L == 1) {
                $res = $res[0];
            }
        }
        else {
            $res = false;
        }

        return $res;
    }

    //Проверяю валидность кода
    function validate_code($code) {
        return (is_string($code) && preg_match('/[a-z\_]+/i', $code) > 0 && preg_match('/^[a-z0-9\_]{3,59}$/i', $code) > 0);
    }

    //Привожу код к каноническому виду
    function canone_code($code) {
        if(is_string($code)) {
            return stripslashes(str_replace('/', '', str_replace(array(' ', ',', '.'), '_', trim(mb_strtolower($code)))));
        }
        else {
            return $code;
        }
    }

    //Работа с JSON

    //Проверка на строку JSON и её валидность
    function check_json($str) {
        if(is_string($str) && !is_numeric($str)) {
            json_decode($str);
            return (json_last_error() == JSON_ERROR_NONE);
        }
        else return false;
    }

    //Валидное преобразование в JSON
    function json_val_encode($str) {
        return json_encode($str, JSON_UNESCAPED_UNICODE);
    }

    //Считываю json-файл
    function read_json($path, $arr = true) {
        $content = '';
        return (file_exists($path) && ($content = file_get_contents($path)) && check_json($content)) ? json_decode($content, $arr) : null;
    }

    //Сделать подмассивы/объекты JSON-строкой
    function make_sub_strings($arr) {
        $obj = (is_object($arr)) ? true : false;
        $custom = array();
        
        if(is_jsoned($arr)) {
            foreach($arr as $key=>$item) {
                $custom[$key] = (is_jsoned($item)) ? json_val_encode($item) : $item;
            }

            if($obj) {
                $custom = (object) $custom;
            }

            return $custom;
        }
        else {
            return $arr;
        }
    }

    //Вставишь в json-массив элемент
    function insert_json($new, $str) {
        if(check_json($str)) {
            $str = json_decode($str, true);

            if(!in_array($new, $str)) {
                $str[] = $new;
            }

            $str = json_val_encode($str);
        }
        elseif($str == '') {
            $str = array($new);

            $str = json_val_encode($str);
        }

        return $str;
    }

    //Удаляю в json-массиве элемент
    function delete_json($search, $str) {
        if(check_json($str)) {
            $new = json_decode($str, true);

            foreach($str as $item) if($item != $search) {
                $new[] = $item;
            }

            $str = json_val_encode($new);
        }

        return $str;
    }

    //Слияние json строк
    function json_merge($str1, $str2, $eq = false) { //eq - прямое слияние, даже с повторами
        if(check_json($str1)) {
            $str1 = json_decode($str1, true);
        }
        else {
            $str1 = array();
        }

        if(check_json($str2)) {
            $str2 = json_decode($str2, true);
        }
        else {
            $str2 = array();
        }

        if($eq) {
            $str1 = array_merge($str1, $str2);
        }
        else {
            $new = array();

            if(count($str1) > 0) {
                foreach($str2 as $item) if(!in_array($item, $str1)) {
                    $str1[] = $item;
                }
            }
            else {
                $str1 = $str2;
            }
        }

        return json_val_encode($str1);
    }

    //Потенциальный клиент json
    function is_jsoned($obj) {
        return (is_array($obj) || is_merged($obj));
    }

    //Поиск в подмассивах
    function array_multi_search($value, $arr, $set_key) {
        if(is_object($arr)) $arr= (array) $arr;

        if(is_array($arr)) {
            foreach($arr as $key=>$item) {
                if(is_object($item)) $item = (array) $item;

                if(isset($item[$set_key]) && is_array($item) && $value == $item[$set_key]) {
                    return $key;
                }
            }
        }
        return null;
    }

    function destroy_cookie($name) {
        if(isset($_COOKIE[$name])) {
            unset($_COOKIE[$name]);
            setcookie($name, null, -1, '/');
            return true;
        }
        else return false;
    }

    //Вставка после первого вхождения
    function str_insert($search, $insert, $str) {
        $index = strpos($str, $search);

        if($index === false) {
            return $str;
        }
        else {
            return substr_replace($str, $search.$insert, $index, strlen($search));
        }
    }

    //Преобразую нечто насколько это возможно в массив
    function take_good_array($arr, $exp = false, $json = true) {
        if(!is_array($arr)) {
            if($json && check_json($arr)) {
                $arr = json_decode($arr, true);
            }
            elseif(is_object($arr)) {
                $arr = (array) $arr;
            }
            elseif($exp && is_string($arr)) {
                $arr = explode(',', $arr);
            }
            else {
                $arr = array($arr);
            }
        }
        return $arr;
    }

    function get_last_key($arr) {
        if(is_array($arr)) {
            return key(array_slice($arr, -1, 1, true));
        }
        else return null;
    }

    //Проверяю массив на ассоциативность
    function is_assoc_array($arr) {
        return (is_array($arr) && array_keys($arr) !== range(0, count($arr) - 1));
    }

    //Объект или ассоц. массив
    function is_merged($obj) {
        return (is_assoc_array($obj) || is_object($obj));
    }

    //Заполнение массива [1] по массиву [2], empty - заполнять ли только пустые зоны?
    function set_merge($arr1, $arr2, $empty = false, $secure = null) {
        $obj = false;
        if(is_object($arr1)) {
            $obj = true;
            $arr1 = (array) $arr1;
        }
        if(is_object($arr2)) $arr2 = (array) $arr2;

        if($arr2 && $arr1) foreach($arr2 as $key=>$item) {
            if(isset($arr2[$key]) && array_key_exists($key, $arr1) && ($empty == false || ($empty == true && ($arr1[$key] === null || $arr1[$key] == '')))) {
                if(is_merged($secure) || $secure === true) {
                    $secure = (array) $secure;
                    if(isset($secure['str'])) unset($secure['str']);
                    $arr1[$key] = secure_text($arr2[$key], $secure);
                }
                else {
                    $arr1[$key] = $arr2[$key];
                }
            }
        }

        return ($obj) ? (object) $arr1: $arr1;
    }

    function set_ref_merge(&$arr1, $arr2) {
        $obj = (is_object($arr1)) ? true : false;
        
        if(is_object($arr2)) $arr2 = (array) $arr2;

        if($arr2 && $arr1) foreach($arr2 as $key=>$item) {
            if(isset($arr2[$key]) && (!$obj && array_key_exists($key, $arr1) || $obj && property_exists($arr1, $key))) {
                if($obj) {
                    $arr1->$key = $arr2[$key];
                }
                else {
                    $arr1[$key] = $arr2[$key];
                }
            }
        }
    }

    //Строковое представление равенства [1] и [2]
    function string_values($arr, $var=null, $par=null) {
        $str = '';
        $mode = (is_assoc_array($arr) && $par == null) ? true : false;
        if($mode) $par = $var;

        $custom = array(
            'separ'  => ',',
            'empty'  => 'NULL',
            'middle' => '=',
            'body'   => "'",
            'json'   => false
        );
        $custom = set_merge($custom, $par);

        $arr = take_good_array($arr, true, $custom['json']);
        if(!$mode) {
            $var = take_good_array($var, false, $custom['json']);
            if(count($arr) == 1 && count($var) > 1) $var = array(json_val_encode($var));
        }

        $L = get_last_key($arr);
        $i = 1;

        if(!$mode && $var) {
            $B = (count($arr) < count($var)) ? count($arr) : count($var);
        }
        else {
            $B = (count($arr));
        }

        foreach($arr as $key=>$item) {
            if($i <= $B) {
                $item1 = $item2 = null;

                if($mode) {
                    $item1 = $key;
                    $item2 = $item;
                }
                elseif(isset($var[$key])) {
                    $item1 = $item;
                    $item2 = $var[$key];
                }

                if(is_jsoned($item1)) {
                    $item1 = json_val_encode($item1);
                }
                if(is_jsoned($item2)) {
                    $item2 = json_val_encode($item2);
                }

                $str.= "$item1{$custom['middle']}{$custom['body']}$item2{$custom['body']}";

                if($key != $L && $i<$B) $str.= $custom['separ'];

                $i++;
            }
        }

        return $str;
    }

    //Определяю код или ID
    function set_ID($ID) {
        if($ID && is_numeric($ID)) {
            $param = 'ID';
        }
        elseif(validate_code($ID)) {
            $param = 'code';
        }
        else {
            $param = null;
        }    
        return $param;
    }

    //Проверка файла по урлу
    function url_exists($file) {
        $file_headers = @get_headers($file);
        return ($file_headers[0] != 'HTTP/1.1 404 Not Found');
    }      

    //Сортировка по преоритету
    function collector_sort($a, $b) {
        if($a->priority == $b->priority) return 0;
        else if($a->priority > $b->priority) return 1;
        else return -1;
    }

    //Удалить папку
    function delete_folder($path) {
        if(!is_dir($path)) {
            return false;
        }

        foreach(scandir($path) as $item) {
            if($item != '.' && $item != '..') {
                if(is_file($path . '/' . $item)) {
                    unlink($path . '/' . $item);
                }
                elseif(is_dir($path . '/' . $item)) {
                    delete_folder($path . '/' . $item);
                }
            }
        }
        if(is_dir($path)) rmdir($path);

        if(is_dir($path)) {
            return false;
        }
        else return true;
    }

    //Редирект
    function redirect($url, $code = false) {
        if($code) {
            $url = get_page_link($url);
        }
        header('Location: ' . $url);
        die();
    }

    //Очистить значения ключей массива
    function array_clear_keys($arr) {
        $res = $arr;
        if(is_array($arr)) {
            foreach($arr as $key=>$item) {
                $res[$key] = '';
            }
        }

        return $res;
    }

    //Проверка вхождений
    function check_auto($code, $par) {
        if(is_object($par)) {
            $par = array($par);
        }
        elseif(is_string($par)) {
            $par = explode(',', str_replace(' ', '', $par));
        }

        $res = null; $m = false;

        foreach($par as $item) {
            if($item[0] == '-') {
                $m = true;
                $item = substr($item, 1);
                if($item == $code) {
                    $res = false;
                }
            }
            elseif($item == $code) {
                $res = true;
            }
        }

        if($res === null) {
            $res = ($m) ? true : false;
        }

        return $res;
    }

    //Получаю первое значение массива или объекта
    function get_first($obj) {
        if(is_array($obj) || is_merged($obj)) {
            foreach($obj as $item) {
                return $item;
            }
        }
        else return $obj;
    }

    //AJAX

    //Генерация AJAX-результата
    function ajax_make_res($param = 'success', $body = '', $title='', $data='') {
        $custom = array(
            'status' => '',
            'body'   => $body,
            'title'  => $title,
            'data'   => $data
        );
        if(is_merged($param)) {
            $custom = set_merge($custom, $param);
        }
        else {
            $custom['status'] = $param;
        }

        return json_val_encode($custom);
    }

    //Это AJAX-запрос
    function is_ajax() {
        global $IS_AJAX;
        return (isset($IS_AJAX) && $IS_AJAX);
    }
?>