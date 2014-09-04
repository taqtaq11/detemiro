<?php
    add_action(array(
        'code'     => 'generate_math_captcha',
        'rule'     => 'public',
        'category' => 'admin',
        'zone'     => 'before_template',
        'priority' => -5,
        'auto'     => 'login',
        'function' => function() {
            generate_math_captcha_session();
        }
    ));

    function generate_math_captcha_session() {
        if(session_status() == 1) {
            session_start();
        }
        if(!isset($_POST['math_captcha']) || !isset($_SESSION['math_captcha'])) {
            $_SESSION['math_captcha'] = array(
                'p1' => rand(0, 11),
                'p2' => rand(1, 11),
                'n'  => 1
            );
            switch(rand(0, 2)) {
                case 0:
                     $_SESSION['math_captcha']['op'] = '*';
                     break;
                case 1:
                     $_SESSION['math_captcha']['op'] = '+';
                     break;
                case 2:
                     $_SESSION['math_captcha']['op'] = '-';
                     break;
            }
        }
    }

    function show_math_captcha_exp() {
        if(session_status() == 2 && isset($_SESSION['math_captcha'])) {
            $str = "{$_SESSION['math_captcha']['p1']} {$_SESSION['math_captcha']['op']} {$_SESSION['math_captcha']['p2']} = ";
        }
        else {
            $str = 'Включите хранение cookie';
        }
        return $str;
    }

    function check_math_captcha($value) {
        $t = false;

        if(session_status() == 2 && isset($_SESSION['math_captcha'])) {
            if($value !== '') {
                $value = (int) $value;
            }
            if(is_numeric($_SESSION['math_captcha']['p1']) && is_numeric($_SESSION['math_captcha']['p2'])) switch($_SESSION['math_captcha']['op']) {
                case '*':
                    $t = ($value === ($_SESSION['math_captcha']['p1'] * $_SESSION['math_captcha']['p2']));
                    break;
                case '+':
                    $t = ($value === ($_SESSION['math_captcha']['p1'] + $_SESSION['math_captcha']['p2']));
                    break;
                case '-':
                    $t = ($value === ($_SESSION['math_captcha']['p1'] - $_SESSION['math_captcha']['p2']));
                    break;
            }
            if(!$t) {
                $try = (int) get_option('admin_math_captcha');
                if(!is_numeric($try) || is_numeric($try) && $try <= 0) {
                    $try = 1;
                }
                $_SESSION['math_captcha']['n']++;
                if($_SESSION['math_captcha']['n'] > $try) {
                    unset($_SESSION['math_captcha']);
                    push_output_message(array(
                        'text'  => 'Ошибка в ответе, пример обновлён.',
                        'title' => 'Ошибка!',
                        'class' => 'alert alert-danger',
                        'type'  => 'error'
                    ));
                    generate_math_captcha_session();
                }
                else {
                    push_output_message(array(
                        'text'  => 'Ошибка в ответе, у вас осталось ' . ($try - 1) . ' попыток.',
                        'title' => 'Ошибка!',
                        'class' => 'alert alert-danger',
                        'type'  => 'error'
                    ));
                }
            }
            else {
                unset($_SESSION['math_captcha']);
                generate_math_captcha_session();
            }
        }

        return $t;
    }

    add_action(array(
        'code'     => 'show_math_captcha',
        'rule'     => 'public',
        'category' => 'admin',
        'auto'     => 'login',
        'zone'     => 'login_fields',
        'priority' => 50,
        'function' => function() {
        ?>
            <div class="field form-group">
                <label>Математическая задача</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-pencil"></i></span>
                    <input class="form-control" type="number" name="math_captcha" value="" placeholder="<?=show_math_captcha_exp(); ?>" />
                </div>
                <p class="help-block">Введите ответ.</p>
            </div>
        <?php
        }
    ));

    add_action(array(
        'code'     => 'check_math_recaptcha',
        'rule'     => 'public',
        'category' => 'admin',
        'auto'     => 'login',
        'zone'     => 'login_check',
        'priority' => 1,
        'function' => function() {
            if(isset($_POST['math_captcha'])) {
                return check_math_captcha($_POST['math_captcha']);
            }
            return false;
        }
    ));

    add_action(array(
        'code'     => 'admin_math_captcha_settings',
        'rule'     => 'admin_settings',
        'category' => 'admin',
        'auto'     => 'settings',
        'zone'     => 'settings_secure',
        'priority' => 100,
        'function' => function() {
            $try = get_option('admin_math_captcha');
        ?>
            <hr />
            <h3>Настройки Math Captcha</h3>
            <div class="row">
                <div class="field form-group col-lg-4">
                    <label>Количество попыток ввода примера</label>
                    <input type="number" min="1" name="math_captcha_try" class="form-control data-control" value="<?=$try;?>">
                </div>
            </div>
        <?php
        }
    ));

    add_action(array(
        'code'     => 'check_admin_math_captcha_settings',
        'rule'     => 'admin_settings',
        'category' => 'admin',
        'zone'     => 'settings_checking',
        'priority' => 100,
        'function' => function($res) {
            if(isset($res['math_captcha_try']) && $res['math_captcha_try'] >= 1) {
                return update_option('admin_math_captcha', $res['math_captcha_try']);
            }
            return false;
        }
    ));
?>