<?php
    $user = get_glob_content();
    $groups = get_users_groups('ID, name');
    $default = array('ID', 'login', 'display_name', 'code', 'mail', 'reg_date', 'password', 'salt', 'hash', 'last_activity', 'last_agent', 'last_ip', 'last_place', 'groups_ID', 'rules');
    if($user):
?>
    <h3>Основные параметры</h3>
    <table class="table table-striped table-hover table-bordered">
        <thead>
            <th width="20%">
                Параметр
            </th>
            <th>
                Значение
            </th>
        </thead>
        <tbody>
            <tr>
                <td>ID</td>
                <td><?=$user->ID; ?></td>
            </tr>
            <tr>
                <td>Логин</td>
                <td><?=$user->login; ?></td>
            </tr>
            <tr>
                <td>Оторажаемое имя</td>
                <td><?=$user->display_name; ?></td>
            </tr>
            <tr>
                <td>Код</td>
                <td><?=$user->code; ?></td>
            </tr>
            <tr>
                <td>E-Mail</td>
                <td><?=$user->mail; ?></td>
            </tr>
            <tr>
                <td>Дата регистрации</td>
                <td><?=$user->reg_date; ?></td>
            </tr>
            <tr>
                <td>Последняя активность</td>
                <td><?=$user->last_activity; ?></td>
            </tr>
            <tr>
                <td>Последний юзер-агент</td>
                <td><?=$user->last_agent; ?></td>
            </tr>
            <tr>
                <td>Последнее место</td>
                <td><?=$user->last_place; ?></td>
            </tr>
            <tr>
                <td>Группы</td>
                <td><?php
                    if(is_array($user->groups_ID)) {
                        $last = get_last_key($user->groups_ID);
                        foreach($user->groups_ID as $key=>$group) {
                            $key = array_multi_search($group, $groups, 'ID');
                            if($key !== null) {
                                echo $groups[$key]->name;
                                if($key != $last) {
                                    echo ', ';
                                }
                            }
                        }
                    }
                ?></td>
            </tr>
            <tr>
                <td>Права</td>
                <td><?php
                    if(is_array($user->rules)) echo implode(', ', $user->rules);
                ?></td>
            </tr>
        </tbody>
    </table>
    <?php if(count((array) $user) > count($default)): ?>
        <h3>Дополнительные параметры</h3>
        <table class="table table-striped table-hover table-bordered">
            <thead>
                <th width="20%">
                    Параметр
                </th>
                <th>
                    Значение
                </th>
            </thead>
            <tbody>
                <?php foreach($user as $key=>$value) if(!in_array($key, $default)): ?>
                    <tr>
                        <td><?=$key;?></td>
                        <td><?php
                            if(is_object($value)) $value = (array) $value;
                            if(is_array($value)) echo implode(', ', $value);
                            else echo $value;
                        ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <a href="<?=get_page_link('edit_user')?>&user_id=<?=$user->ID?>" class="btn btn-primary"><span class="glyphicon glyphicon-user"></span> Редактировать пользователя</a>
<?php
    else: 
        echo 'Неверный ID пользователя';
    endif;
?>