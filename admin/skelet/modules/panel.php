<?php
    $active = get_option('active_modules');
    $list = get_modules_list();
?>

<div id="modules-panel" class="detwork-control">
    <table width="100%" class="table table-striped table-hover table-bordered">
        <thead>
            <tr>
                <th width="20%">Название</th>
                <th>Описание</th>
                <th width="5%">Версия</th>
                <th width="5%">Акт.</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($list as $item): ?>
            <?php
                $key = null;
                $in = (($key = array_multi_search($item['path'], $active, 0)) !== null) ? true : false;
                $on = ($in && $active[$key][1]) ? true : false;

                echo '<tr class="parenter ';
                if($in) {
                    echo 'installed';
                    if($on) {
                        echo ' success';
                    }
                }
                else {
                    echo 'active';
                }
                echo '">';
            ?>
                <td><?=$item['name']; ?></td>
                <td>
                    <?php if($item['description']):?><p><?=$item['description']; ?></p><?php endif; ?>
                    <p><b>Папка: </b><span data-code="<?=$item['path'];?>"><?=$item['path'];?></span></p>
                    <p><b>Автор: </b><?=$item['author'];?></p>
                    <?php if($item['url']):?><p><b>Ссылка: </b><a href="<?=$item['url'];?>">Страница Модуля</a></p><?php endif; ?>
                    <p>
                    <?php if(!$on): ?>
                        <a data-action="activate_module" class="button-control" data-pre-function="table_get_ID">Активировать</a> |
                    <?php endif;?>
                    <?php if($on): ?>
                        <a data-action="deactivate_module" class="button-control" data-pre-function="table_get_ID">Деактивировать</a> |
                    <?php endif;?>
                    <?php if(!$in): ?>
                        <a data-action="install_module" class="button-control" data-pre-function="table_get_ID">Установить</a> |
                    <?php endif;?>
                    <?php if($in): ?>
                        <a data-action="delete_module" class="button-control" data-pre-function="table_get_ID">Удалить</a> |
                    <?php endif;?>
                        <a class="button-control table-delete" data-set-action="delete_full_module" data-pre-function="table_get_ID">Полное удаление</a>
                    </p>
                </td>
                <td><?=$item['version'];?></td>
                <td><input class="data-control" name="active[]" type="checkbox" value="<?=$item['path'];?>"<?=($on)?' checked':''?>/></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Название</th>
                <th>Описание</th>
                <th>Версия</th>
                <th>Акт.</th>
            </tr>
        </tfoot>
    </table>
    <button class="btn btn-primary button-control input-control" data-action="update_modules"><span class="glyphicon glyphicon-ok"></span> Сохранить</button>
</div>