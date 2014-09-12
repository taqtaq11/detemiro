<?php
    $keys = get_glob_content();
?>

<div id="keys-block" class="detwork-control">
    <div class="panel panel-default">
        <div class="panel-body">
            <b>Упраление ключами: </b>
            <a class="btn btn-primary" href="<?=get_page_link('remote_keys_edit');?>"><span class="glyphicon glyphicon-cog"></span> Добавить ключ</a>
        </div>
    </div>

    <?php
        if(is_array($keys) && count($keys) > 0):
    ?>
        <table class="table table-striped editable-table table-hover table-bordered" width="100%">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="15%">Имя</th>
                    <th>Ключ</th>
                    <th>Права</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($keys as $item): ?>
                    <tr class="parenter">
                        <td data-id="<?=$item->ID; ?>"><?=$item->ID; ?></td>
                        <td><?=$item->name;?></td>
                        <td>
                            <p class="field"><?=$item->key_value; ?></p>
                            <p class="controls">
                                <a href="<?=get_page_link('remote_keys_edit') . '&key_id=' . $item->ID ?>">Редактировать</a>
                                |
                                <a class="button-control table-delete" data-set-action='delete_remote_key'>Удалить</a>
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
                    <th>Имя</th>
                    <th>Ключ</th>
                    <th>Права</th>
                </tr>
            </tfoot>
        </table>
        <?php
            pagination_show();
        ?>
    <?php else: ?>
        <p>Нет ключ доступа!</p>
    <?php endif; ?>
</div>