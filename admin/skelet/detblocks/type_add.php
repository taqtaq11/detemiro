<?php
    $block = get_glob_content();
?>
<form method="POST">
    <div class="row">
        <div class="field form-group col-lg-5">
            <label>Код</label>
            <input type="text" maxlength="60" name="code" class="form-control input-medium" value="<?=$block->code;?>" required/>
        </div>
    </div>
    <div class="row">
        <div class="field form-group col-lg-5">
            <label>Имя блока</label>
            <input type="text" name="name" class="form-control input-medium" value="<?=$block->name;?>"/>
        </div>
    </div>
    <hr />
    <div class="form-group">
        <button class="btn btn-primary" name="save"><span class="glyphicon glyphicon-ok"></span> Сохранить</button>
    </div>
</form>