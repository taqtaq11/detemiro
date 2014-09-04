<?php
    $groups = get_users_groups();

    $rules = get_glob_content();

    $main_ID = (isset($_GET['group'])) ? $_GET['group'] : 1;

    $current_group = get_user_group($main_ID, 'rules');
    if($current_group) {
        $current_group = $current_group->rules;
        $current_group = (check_json($current_group)) ? json_decode($current_group, false) : array();
    }
?>

<div id="rules-panel">
    <div class="panel panel-default detwork-control">
        <div class="panel-body">
            <b>Упраление правами: </b>
            <a role="button" class="btn btn-primary button-control" data-window="dialog-add" data-set-action="add_rule"><span class="glyphicon glyphicon-certificate"></span> Добавить право</a>
            <form methot="GET" class="pull-right">
                <input type="hidden" name="page" value="<?=get_current_key(); ?>" />
                <select name="group" onchange="this.form.submit()" class="form-control">
                    <?php
                        foreach($groups as $item) {
                            echo '<option value="' . $item->ID . '"' . (($main_ID == $item->ID) ? ' selected' : '') . '>' . $item->name . '</option>';
                        }
                    ?>
                </select>
            </form>
        </div>
        <div id="dialog-add" class="dialog-basic input-control" title="Добавить право">
            <form method="POST">
                <div class="field form-group" data-table="code">
                    <label>Код *</label>
                    <input type="text" class="form-control data-control" name="code" value="" required />
                </div>
                <div class="field form-group" data-table="desc">
                    <label>Описание параметра</label>
                    <textarea class="form-control data-control" name="desc"></textarea>
                </div>
                <input type="hidden" name="action" value="add_rule" />
            </form>
        </div>
    </div>

    <div id="rules-config" class="detwork-control">
        <form method="POST">
            <?php
                foreach($rules as $index=>$items):
            ?>
                <fieldset class="rule-group editable-table">
                    <legend class="rule-group-title"><?=($index==0) ? 'Системные права' : 'Дополнительные права' ?></legend>
                    <?php
                        if(count($items) > 0) foreach($items as $key=>$item):
                    ?>
                        <div class="field form-group rule data-control">
                            <input type="checkbox" name="rule[]" id="<?='param-' . $item['code'];?>" value="<?=$item['code'];?>"<?=(in_array($item['code'], $current_group)) ? ' checked' : ''?>/>
                            <label data-code="<?=$item['code'];?>" for="<?='param-' . $item['code'];?>"><?=$item['code'];?></label>
                            <?=($index==1) ? '<a class="btn btn-danger btn-xs table-delete button-control" data-set-action="delete_rule">удалить</a>' : '' ?>
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
            <hr />
            <div class="form-group">
                <button type="button" id="update-rules" class="btn btn-primary button-control input-control" data-action="update_rules"><span class="glyphicon glyphicon-ok"></span> Сохранить</button>
            </div>
            <input type="hidden" class="data-control" value="<?=$main_ID;?>" name="group" />
        </form>
    </div>
</div>