<?php
    define('DET_URL', 'http://friends-gt.ru/connect.php');

    function make_remote_action($key, $name, $params=null, $url=DET_URL) {
        $data = array(
            'action' => $name,
            'key'    => $key,
            'params' => $params
        );

        $query = array(
            'http' => array(
                'header'     => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'     => 'POST',
                'user_agent' => 'DETWorker',
                'content'    => http_build_query($data)
            )
        );

        return file_get_contents($url, false, stream_context_create($query));
    }
?>