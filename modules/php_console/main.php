<?php
    add_apage(array(
        'code'     => 'console',
        'parent'   => 'settings',
        'title'    => 'Консоль',
        'rule'     => 'admin_settings',
        'category' => 'admin',
        'priority' => 20,
        'function' => function() {
            get_module_template('console.php');
        }
    ));

    add_action(array(
        'code'     => 'console_doit',
        'function' => 'action_console_doit',
        'rule'     => 'admin, admin_settings',
        'zone'     => 'before_template',
        'category' => 'admin',
        'auto'     => 'console'
    ));

    function action_console_doit() {
        global $DETDB, $ACTIONS, $SCRIPTS, $BLOCK, $PAGE, $APAGES;

        set_glob_content(array(
            'body' => array('','')
        ));
        if(isset($_POST['input'])) {
            $input = $_POST['input'];
            $output = $input;

            ob_start();

            $time_start = microtime(true);

            eval($output);

            $time_end= microtime(true);

            $output = ob_get_clean();

            $time = number_format($time_end - $time_start, 7);

            set_glob_content(array(
                'body' => array($input, $output)
            ));

            push_output_message(array(
                'title'  => 'Выполнено!',
                'text'   => "Время выполнения вашего запроса: <b>$time сек</b>.",
                'type'   => 'success',
                'class'  => 'alert alert-success'
            ));
        }
    }
?>