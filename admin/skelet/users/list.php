<?php
    $users  = get_glob_content();
    $groups = get_users_groups('ID, name');
?>

<div id="users-block" class="detwork-control">
    <div class="panel panel-default">
        <div class="panel-body">
            <b>Упраление пользователями: </b><a class="btn btn-primary" href="<?=get_page_link('edit_user')?>"><span class="glyphicon glyphicon-user"></span> Добавить пользователя</a>
        </div>
    </div>
    <?php
        if(count($users) > 0): 
    ?>
        <table class="table table-striped table-hover table-bordered" width="100%">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="10%">Логин</th>
                    <th>Код</th>
                    <th>Имя</th>
                    <th>Группы</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $item): ?>
                    <tr class="parenter">
                        <td data-id="<?=$item->ID; ?>"><?=$item->ID; ?></td>
                        <td><?=$item->login; ?></td>
                        <td><?=$item->code;  ?></td>
                        <td>
                            <p><?=$item->display_name; ?></p>
                            <p>
                                <a href="<?=get_page_link('user_info')?>&user_id=<?=$item->ID?>">Информация</a>
                                |
                                <a href="<?=get_page_link('edit_user')?>&user_id=<?=$item->ID?>">Редактировать</a>
                                |
                                <a class="button-control table-delete" data-set-action="delete_user">Удалить</a>
                            </p>
                        </td>
                        <td>
                            <?php
                                if(is_array($item->groups_ID)) {
                                    $last = get_last_key($item->groups_ID);
                                    foreach($item->groups_ID as $key=>$group) {
                                        $key = array_multi_search($group, $groups, 'ID');
                                        if($key !== null) {
                                            echo $groups[$key]->name;
                                            if($key != $last) {
                                                echo ', ';
                                            }
                                        }
                                    }
                                }
                            ?>
                        </td>
                    </tr> 
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Логин</th>
                    <th>Код</th>
                    <th>Имя</th>
                    <th>Группа</th>
                </tr>
            </tfoot>
        </table>
        <?php
            pagination_show();
        ?>
    <?php else: ?>
        <p>Нет пользователей!</p>
    <?php endif; ?>
</div>