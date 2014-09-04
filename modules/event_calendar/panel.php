<?php
    $month = (isset($_GET['month'])) ? $_GET['month'] : date('m');
    $json_link = get_page_link('calendar_event_json', true) . '&month=' . $month;
?>
<div id="calendar_events" class="detwork-control">
    <div class="panel panel-default"><div class="panel-body">
        <a class="btn btn-primary" href="<?=get_page_link('calendar_event_add');?>"><span class="glyphicon glyphicon-time"></span> Добавить событие</a>
        <?php if(check_rule('admin_settings')): ?>
            <a class="btn btn-info" target="_blank" href="<?=$json_link;?>"><span class="glyphicon glyphicon-cog"></span> Получить в формате JSON</a>
        <?php endif; ?>
        <form methot="GET" class="pull-right">
            <input type="hidden" name="page" value="<?=get_current_key(); ?>" />
            <select name="month" class="form-control" onchange="this.form.submit()">
                <?php
                    foreach(get_calendar_months() as $key=>$item) {
                        echo '<option value="' .$key . '"' . (($key == $month) ? ' selected' : '') . '>' . $item . '</option>';
                    }
                ?>
            </select>
        </form>
    </div></div>

    <?php
        show_calendar_events(array(
            'links' => true
        ));
    ?>
</div>