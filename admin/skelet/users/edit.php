<?php 
    $user = get_glob_content();
    $default = get_option('default_group');
    $rules = get_rules();
?>

<script>
$(function() {
    $('button[name="delete"]').click(function() {
        $.detconfirm('Вы действительно хотите удалить пользователя?', 'Внимание!', function(res) {
            if(res) {
                $('form').find('[name="action"]').val('delete');
                $('form').submit();
            }
        });
    });
});
</script>

<form method="POST">
    <ul class="nav nav-tabs" role="tablist">
        <li class="active"><a href="#main" role="tab" data-toggle="tab">Основное</a></li>
        <li><a href="#rules" role="tab" data-toggle="tab">Права</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="main">
            <h3>Основная информация</h3>
            <div class="row">
                <div class="field form-group col-lg-5">
                    <label>Логин</label>
                    <input type="text" name="login" class="form-control input-medium" value="<?=($user)?$user->login:''?>" required/>
                </div>
            </div>
            <div class="row">
                <div class="field form-group col-lg-5">
                    <label>Отображаемое имя</label>
                    <input type="text" name="display_name" class="form-control" value="<?=($user)?$user->display_name:''?>" required/>
                </div>
            </div>
            <div class="row">
                <div class="field form-group col-lg-5">
                    <label>Ссылка - Код</label>
                    <input type="text" name="code" maxlength="60" class="form-control" value="<?=($user)?$user->code:''?>" required/>
                </div>
            </div>
            <div class="row">
                <div class="field form-group col-lg-5">
                    <label>E-mail</label>
                    <input type="email" name="mail" class="form-control" value="<?=($user)?$user->mail:''?>" required/>
                </div>
            </div>
            <div class="row">
                <div class="field form-group col-lg-5">
                    <label>Новый пароль</label>
                    <input type="password" name="password" class="form-control" value=""/>
                </div>
            </div>
            <div class="row">
                <div class="field form-group col-lg-5">
                    <label>Группа</label>
                    <select multiple name="groups_ID[]" class="form-control">
                        <?php foreach(get_users_groups() as $item)
                            echo '<option value="' . $item->ID . '"' . (($user && in_array($item->ID, $user->groups_ID) || !$user && $item->ID == $default) ? ' selected':'') .'>' . $item->name . '</option>';
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="rules">
            <h3>Права пользователя</h3>
            <?php
                foreach($rules as $index=>$items):
            ?>
                <fieldset class="rule-group editable-table">
                    <legend class="rule-group-title"><?=($index==0) ? 'Системные права' : 'Дополнительные права' ?></legend>
                    <?php
                        if(count($items) > 0) foreach($items as $key=>$item):
                    ?>
                        <div class="field form-group rule data-control">
                            <input type="checkbox" name="rules[]" id="<?='param-' . $item['code'];?>" value="<?=$item['code'];?>"<?=($user && in_array($item['code'], $user->rules)) ? ' checked' : ''?>/>
                            <label data-code="<?=$item['code'];?>" for="<?='param-' . $item['code'];?>"><?=$item['code'];?></label>
                            <?=($item['desc']) ? '<p class="help-block">' . $item['desc'] . '</p>':'';?>
                        </div>
                    <?php
                        endforeach;
                        else {
                            echo 'Пусто!';
                        }
                    ?>
                </fieldset>
            <?php
                endforeach;
            ?>
        </div>
        <?php actions_zone('edit_user_tabs') ?>
    </div>
    <hr />
    <div class="form-group">
        <button class="btn btn-primary" name="save"><span class="glyphicon glyphicon-ok"></span> Сохранить</button>
        <?php if(isset($user->ID)): ?>
            <button class="btn btn-danger" name="delete" onclick="return false;"><span class="glyphicon glyphicon-trash"></span> Удалить пользователя</button>
        <?php endif ;?>
    </div>
    <input type="hidden" name="action" value="save" />
</form>