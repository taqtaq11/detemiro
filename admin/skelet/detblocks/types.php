<?php
    $blocks = get_glob_content();
?>

<div id="options-block" class="detwork-control">
    <div class="panel panel-default">
        <div class="panel-body">
            <b>Упраление блоками: </b><a class="btn btn-primary" href="<?=get_page_link('detblocks_type_add')?>"><span class="glyphicon glyphicon glyphicon glyphicon-list-alt"></span> Добавить блок</a>
        </div>
    </div>
    <?php
        if($blocks && count($blocks) > 0): 
    ?>
        <table class="table table-striped table-hover table-bordered">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="10%">Код</th>
                    <th>Имя</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($blocks as $item): ?>
                    <tr class="parenter">
                        <td data-id="<?=$item->ID; ?>"><?=$item->ID; ?></td>
                        <td><?=$item->code; ?></td>
                        <td>
                            <p><?=$item->name; ?></p>
                            <p>
                                <a href="<?=get_page_link('detblocks_type_add')?>&block_id=<?=$item->ID; ?>">Редактировать</a>
                                |
                                <a class="button-control table-delete" data-set-action="delete_detblock">Удалить</a>
                            </p>
                        </td>
                    </tr> 
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Код</th>
                    <th>Имя</th>
                </tr>
            </tfoot>
        </table>
        <?php
            pagination_show();
        ?>
    <?php else: ?>
        <p>Нет блоков!</p>
    <?php endif; ?>
</div>