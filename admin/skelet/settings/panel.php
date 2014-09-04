<?php
    $params = get_glob_content();
?>

<form method="POST">
    <ul class="nav nav-tabs" role="tablist">
        <li class="active"><a href="#main" role="tab" data-toggle="tab">Основное</a></li>
        <li><a href="#secure" role="tab" data-toggle="tab">Безопасность</a></li>
        <?php actions_zone('settings_tabs') ?>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="main">
            <h3>Основные настройки</h3>
            <div class="row">
                <div class="field form-group col-lg-4">
                    <label><?=_('Имя сайта');?></label>
                    <input name="site_name" type="text" class="form-control data-control" value="<?=$params['site_name']; ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="field form-group col-lg-4">
                    <label><?=_('Основная тема');?></label>
                    <?php
                        $templates = get_templates();

                        if(count($templates) > 0): 
                    ?>
                        <select class="form-control data-control" name="current_template">
                            <?php
                                foreach($templates as $item) {
                                    echo '<option value="' . $item['path'] .'"' . (($item['path'] == $params['current_template']) ? ' selected' : '') . '>' . $item['name'] . '</option>';
                                }
                            ?>
                        </select>
                    <?php else: ?>
                        <input name="current_template" type="text" class="form-control data-control" value="<?=$params['current_template'];?>" required>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row">
                <div class="field form-group col-lg-4">
                    <label><?=_('Группа по-умолчанию');?></label>
                    <?php
                        $groups = get_users_groups();
                        $current = $params['default_group'];

                        if(count($groups) > 0): 
                    ?>
                        <select class="form-control data-control" name="default_group">
                            <?php
                                foreach($groups as $item) {
                                    echo '<option value="' . $item->ID .'"' . (($item->ID == $current) ? ' selected' : '') . '>' . $item->name . '</option>';
                                }
                            ?>
                        </select>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="secure">
            <h3>Настройки безопасности входа в админ-панель</h3>
            <div class="field form-group"><div class="checkbox">
                <label>
                    <input name="login_ip" type="checkbox" value="true" class="data-control"<?=($params['login_ip']) ? ' checked' : '';?>>
                    <?=_('Проверка IP-адреса');?>
                </label>
            </div></div>
            <div class="field form-group"><div class="checkbox">
                <label>
                    <input name="login_agent" type="checkbox" value="true" class="data-control"<?=($params['login_agent']) ? ' checked' : '';?>>
                    <?=_('Проверка юзер-агента');?>
                </label>
            </div></div>
            <hr />
            <h3>Cookie</h3>
            <div class="row">
                <div class="field form-group col-lg-4">
                    <label><?=_('Время жизни Cookie профиля (в мин)');?></label>
                    <input name="cookie_login_live" type="number" class="form-control data-control" value="<?=$params['cookie_login_live']; ?>" required>
                </div>
            </div>
            <?php actions_zone('settings_secure') ?>
        </div>
        <?php actions_zone('settings_panels') ?>
    </div>
    <hr />
    <button name="submit" type="submit" class="btn btn-primary" data-action="settings_update"><span class="glyphicon glyphicon-ok"></span> <?=_('Сохранить');?></button>
</form>