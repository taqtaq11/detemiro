<!DOCTYPE html>
<html>
<head>
    <title><?php page_title('Админ-панель'); ?></title>
    <?php page_header(); ?>
</head>
<body class="login-form">
    <div class="container">
        <form class="form-signin" method="POST" role="form">
            <h2 class="form-signin-heading">Вход</h2>
            <div class="field form-group">
                <label>Логин</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                    <input type="text" name="form_login" class="form-control" placeholder="Введите ваш логин" value="" required autofocus />
                </div>
            </div>
            <div class="field form-group">
                <label>Пароль</label>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-warning-sign"></i></span>
                    <input type="password" name="form_password" class="form-control" placeholder="Введите ваш пароль" value="" required />
                </div>
            </div>
            <?php actions_zone('login_fields') ?>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Войти</button>
        </form>
        <?php show_output_result_messages(); ?>
    </div>
<?php page_footer(); ?>
</body>
</html>