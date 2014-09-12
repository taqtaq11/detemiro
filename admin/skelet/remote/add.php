<?php
    $remote_key = get_glob_content();
    $rules = get_rules();
?>

<script>
$(function() {
    function set_remote_key(res) {
        if($.get_property(res, 'action', 'data')) {
            $('[name="key_value"]').val(res.action['data']);
        }
    }
    $.add_function(set_remote_key);
});
</script>

<form method="POST" class="detwork-control">
    <fieldset>
        <legend>Основная информация</legend>
        <div class="row">
            <div class="field form-group col-lg-5">
                <label>Имя ключа</label>
                <input type="text" name="name" class="form-control input-medium" value="<?=$remote_key->name;?>" required/>
            </div>
        </div>
        <div class="row">
            <div class="field form-group col-lg-5">
                <label>Ключ</label>
                <div class="input-group">
                    <span class="input-group-addon"><a id="generate-remote-key" class="button-control" data-action="generate_remote_key" data-post-function="set_remote_key" title="Генерировать"><i class="glyphicon glyphicon-repeat"></i></a></span>
                    <input type="text" name="key_value" class="form-control" placeholder="Код от 16 до 20 символов" maxlength="20" value="<?=$remote_key->key_value;?>" required/>
                </div>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <?php
            foreach($rules as $index=>$items):
        ?>
            <fieldset class="rule-group editable-table">
                <legend class="rule-group-title"><?=($index==0) ? 'Системные права' : 'Дополнительные права' ?></legend>
                <?php
                    if(count($items) > 0) foreach($items as $key=>$item):
                ?>
                    <div class="field form-group rule data-control">
                        <input type="checkbox" name="rules[]" id="<?='param-' . $item['code'];?>" value="<?=$item['code'];?>"<?=($remote_key->rules && in_array($item['code'], $remote_key->rules)) ? ' checked' : ''?>/>
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
    </fieldset>
    <?php actions_zone('remote_keys_edit_fieldset') ?>
    <hr />
    <button class="btn btn-primary" name="save"><span class="glyphicon glyphicon-ok"></span> Сохранить</button>
</form>