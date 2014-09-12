<?php
    $groups = get_glob_content();
?>

<div id="groups-block" class="detwork-control">
    <div class="panel panel-default">
        <div class="panel-body">
            <b>Упраление группами: </b>
            <a role="button" class="btn btn-primary button-control" data-window="dialog-add" data-set-action="add_user_group"><span class="glyphicon glyphicon-list-alt"></span> Добавить группу</a>
        </div>
    </div>

    <?php
        if(is_array($groups) && count($groups) > 0):
    ?>
        <table class="table table-striped editable-table table-hover table-bordered" width="100%">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="15%">Код</th>
                    <th>Имя</th>
                    <th>Права</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($groups as $item): ?>
                    <tr class="parenter">
                        <td data-id="<?=$item->ID; ?>"><?=$item->ID; ?></td>
                        <td data-code="<?=$item->code; ?>"><?=$item->code; ?></td>
                        <td>
                            <p class="field"><?=$item->name; ?></p>
                            <p class="controls">
                                <a class="button-control table-edit"
                                    data-set-action="update_user_group"
                                    data-window="dialog-add"
                                    data-action="get_user_group"
                                >
                                Редактировать</a>
                                |
                                <a class="item-edit-more" href="<?=get_page_link('user_rules'); ?>&group=<?=$item->ID;?>">Права</a>
                                |
                                <a class="button-control table-delete" data-set-action='delete_user_group'>Удалить</a>
                            </p>
                        </td>
                        <td><?php
                            if(is_string($item->rules)) $item->rules = json_decode($item->rules, true);
                            if(is_array($item->rules)) echo implode(', ', $item->rules);
                        ?></td>
                    </tr> 
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Код</th>
                    <th>Имя</th>
                    <th>Права</th>
                </tr>
            </tfoot>
        </table>
        <?php
            pagination_show();
        ?>
    <?php else: ?>
        <p>Нет категорий для опций!</p>
    <?php endif; ?>
    <div id="dialog-add" class="dialog-basic input-control" title="Добавить группу">
        <form method="POST">
            <div class="field form-group">
                <label>Код группы</label>
                <input type="text" maxlength="60" class="form-control data-control" name="code" value="">
            </div>
            <div class="field form-group">
                <label>Имя группы</label>
                <input type="text" class="form-control data-control" name="name" value="">
            </div>
        </form>
    </div> 
</div>