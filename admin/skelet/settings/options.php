<?php
    push_output_message(array(
        'title'  => 'Внимание!',
        'text'   => 'Редактирование опций предназначенно для "простых" (строк и 1-х массивов) данных.',
        'type'   => 'warning',
        'class'  => 'alert alert-warning',
        'closed' => false
    ));

    $options = get_glob_content();
?>

<div id="options-block" class="detwork-control">
    <div class="panel panel-default">
        <div class="panel-body">
            <b>Упраление опциями: </b><a role="button" class="btn btn-primary button-control" data-window="option-edit" data-set-action="add_option"><span class="glyphicon glyphicon-cog"></span> Добавить опцию</a>
        </div>
    </div>
    <?php
        if(count($options) > 0): 
    ?>
        <table class="table table-striped table-hover table-bordered">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="10%">Код</th>
                    <th width="20%">Имя</th>
                    <th>Значение</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($options as $item): ?>
                    <tr class="parenter">
                        <td data-id="<?=$item->ID;?>"><?=$item->ID; ?></td>
                        <td><?=$item->code; ?></td>
                        <td>
                            <p><?=_($item->name); ?></p>
                            <p>
                                <a class="button-control table-edit"
                                    data-set-action="update_option"
                                    data-window="option-edit"
                                    data-action="get_option"
                                >Редактировать</a>
                                |
                                <a class="button-control table-delete" data-set-action="delete_option">Удалить</a>
                            </p>
                        </td>
                        <td class="item-value"><div><?=htmlspecialchars($item->value); ?></div></td>
                    </tr> 
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Код</th>
                    <th>Имя</th>
                    <th>Значение</th>
                </tr>
            </tfoot>
        </table>
        <?php
            pagination_show();
        ?>
    <?php else: ?>
        <p>Нет опций!</p>
    <?php endif; ?>
    <div id="option-edit" class="dialog-basic input-control" title="Настройки опции">
        <form id="form-options-add" method="POST" name="options_form">
            <div class="field form-group">
                <label>Код опции</label>
                <input type="text" maxlength="60" class="data-control form-control" name="code" value="">
            </div>
            <div class="field form-group">
                <label>Название опции</label>
                <input type="text" class="data-control form-control" name="name" value="">
            </div>
            <div class="field form-group">
                <p class="subfield">
                    <label>Значение</label>
                    <input type="text" class="data-control form-control" name="value[]" value="">
                </p>
                <button type="button" class="button-more btn btn-info btn-xs">Ещё...</button>
            </div>
        </form>
    </div>
</div>