<?php
    add_action(array(
        'code'     => 'include_admin_recaptcha',
        'rule'     => 'public',
        'category' => 'admin',
        'auto'     => 'login',
        'zone'     => 'before_template',
        'priority' => '-5',
        'function' => function() {
            get_module_template('recaptchalib.php');
        }
    ));

    add_action(array(
        'code'     => 'show_admin_recaptcha',
        'rule'     => 'public',
        'category' => 'admin',
        'auto'     => 'login',
        'zone'     => 'login_fields',
        'priority' => '30',
        'function' => function() {
        ?>
            <div class="field form-group">
                <label>Проверочный код</label>
                <?php show_recaptcha(); ?>
                <p class="help-block">Введите код, представленный на изображении.</p>
            </div>
        <?php
        }
    ));

    add_action(array(
        'code'     => 'check_admin_recaptcha',
        'rule'     => 'public',
        'category' => 'admin',
        'auto'     => 'login',
        'zone'     => 'login_check',
        'priority' => '30',
        'function' => function() {
            $t = true;

            $private_key = get_option('admin_recaptcha_keys')[1];

            if(isset($_POST['recaptcha_challenge_field'])) {
                $resp = recaptcha_check_answer($private_key, $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST["recaptcha_response_field"]);
                if(!$resp->is_valid) {
                    $t = false;
                    push_output_message(array(
                        'text'  => 'Ошибка заполнения проверочного кода',
                        'title' => 'Ошибка!',
                        'class' => 'alert alert-danger',
                        'type'  => 'error'
                    ));
                }
            }

            return $t;
        }
    ));

    function show_recaptcha($skin = 'white') {
        $public_key = get_option('admin_recaptcha_keys')[0];

        echo "
            <script>
                var RecaptchaOptions = {
                    theme : '$skin'
                };
            </script>
        ";

        echo recaptcha_get_html($public_key, null, true);
    }

    add_action(array(
        'code'     => 'admin_recaptcha_settings',
        'rule'     => 'admin_settings',
        'category' => 'admin',
        'auto'     => 'settings',
        'zone'     => 'settings_secure',
        'priority' => 100,
        'function' => function() {
            $keys = get_option('admin_recaptcha_keys');
        ?>
            <hr />
            <h3>Настройки Google ReCaptcha</h3>
            <div class="row">
                <div class="field form-group col-lg-4">
                    <label>Публичный ключ (Public key)</label>
                    <input type="text" name="recaptcha_key[]" class="form-control data-control" value="<?=$keys[0];?>">
                </div>
            </div>
            <div class="row">
                <div class="field form-group col-lg-4">
                    <label>Приватный ключ (Private key)</label>
                    <input type="text" name="recaptcha_key[]" class="form-control data-control" value="<?=$keys[1];?>">
                </div>
            </div>
        <?php
        }
    ));

    add_action(array(
        'code'     => 'check_admin_recaptcha_settings',
        'rule'     => 'admin_settings',
        'category' => 'admin',
        'zone'     => 'settings_checking',
        'priority' => 100,
        'function' => function($res) {
            if(isset($res['recaptcha_key']) && count($res['recaptcha_key']) == 2 && !is_assoc_array($res['recaptcha_key'])) {
                return update_option('admin_recaptcha_keys', $res['recaptcha_key']);
            }
            return false;
        }
    ));
?>