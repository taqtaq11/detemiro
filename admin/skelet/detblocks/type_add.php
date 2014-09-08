<?php
    $block = get_glob_content();
?>

<script>
$(function() {
    $('button[name="delete"]').click(function() {
        $.detconfirm('Вы действительно хотите удалить данный блок и весь связанный с ним контент с полями?', 'Внимание!', function(res) {
            if(res) {
                $('form').find('[name="action"]').val('delete');
                $('form').submit();
            }
        });
    });
});
</script>

<form method="POST">
    <div class="row">
        <div class="field form-group col-lg-5">
            <label>Код</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-barcode"></i></span>
                <input type="text" maxlength="60" name="code" class="form-control input-medium" value="<?=$block->code;?>" required/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="field form-group col-lg-5">
            <label>Имя блока</label>
            <div class="input-group">
                <span class="input-group-addon"><i class="glyphicon glyphicon-font"></i></span>
                <input type="text" name="name" class="form-control input-medium" value="<?=$block->name;?>"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="field form-group col-lg-5">
            <label>Описание блока</label>
            <textarea name="description" class="form-control" rows="3"><?=$block->description;?></textarea>
        </div>
    </div>
    <hr />
    <div class="form-group">
        <button class="btn btn-primary" name="save"><span class="glyphicon glyphicon-ok"></span> Сохранить</button>
        <?php if(isset($block->ID)): ?>
            <button class="btn btn-danger" name="delete" onclick="return false;"><span class="glyphicon glyphicon-trash"></span> Удалить блок</button>
        <?php endif ;?>
    </div>
    <input type="hidden" name="action" value="save" />
</form>