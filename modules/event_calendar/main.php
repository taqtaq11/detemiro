<?php
    add_apage(array(
        'title'    => 'Календарь событий',
        'code'     => 'calendar_event',
        'parent'   => '',
        'rule'     => 'calendar_event_admin',
        'priority' => 30,
        'category' => 'admin',
        'function' => function() {
            get_module_template('panel.php');
        }
    ));

    add_apage(array(
        'title'    => 'Календарь событий',
        'code'     => 'calendar_event',
        'rule'     => 'public',
        'priority' => 30,
        'category' => 'public',
        'function' => function() {
            get_module_template('public.php');
        }
    ));

    add_apage(array(
        'title'    => 'Календарь событий для обмена',
        'code'     => 'calendar_event_json',
        'rule'     => 'public',
        'priority' => 30,
        'category' => 'public',
        'skelet'   => false,
        'function' => function() {
            show_calendar_events(array(
                'links' => false,
                'show'  => 'json'
            ));
        }
    ));

    add_apage(array(
        'title'    => 'Добавить событие',
        'category' => 'admin',
        'code'     => 'calendar_event_add',
        'parent'   => 'calendar_event',
        'rule'     => 'calendar_event_admin',
        'priority' => -1,
        'function' => function() {
            get_module_template('add.php');
        }
    ));

    add_script(array(
        'type'     => 'script',
        'link'     => get_module_file('js/jquery.datetimepicker.min.js'),
        'priority' => '40',
        'auto'     => 'calendar_event_add',
        'category' => 'admin',
        'zone'     => 'footer',
        'code'     => 'datetimepicker_js'
    ));

    add_script(array(
        'type'     => 'style',
        'link'     => get_module_file('js/jquery.datetimepicker.min.css'),
        'priority' => '35',
        'auto'     => 'calendar_event_add',
        'category' => 'admin',
        'zone'     => 'footer',
        'code'     => 'datetimepicker_css'
    ));
    add_script(array(
        'type'     => 'script',
        'priority' => '49',
        'auto'     => 'calendar_event_add',
        'category' => 'admin',
        'zone'     => 'footer',
        'link'     => 'http://momentjs.com/downloads/moment.min.js',
        'code'     => 'moment_js'
    ));
    add_script(array(
        'type'     => 'script',
        'priority' => '50',
        'auto'     => 'calendar_event_add',
        'category' => 'admin',
        'zone'     => 'footer',
        'code'     => 'datetimepicker_doit',
        'function' =>  function() {
        $option = get_option('calendar_events_range');
    ?>
        <script>
            $(function(){
                var time_start, time_end, min = null, max = null;
                var ID = ($('[name="event_ID"]:first-of-type').length) ? $('[name="event_ID"]:first-of-type').val() : '';

                function check_calendar_events_near() {
                    $('.datepicker-error').remove();
                    if($('.table_show table').length) {
                        $.detmessage('Данное событие пересекается с другими, будьте внимательны!', 'Внимание!', 'warning datepicker-error');
                    }
                }

                <?php if($option) echo 'check_calendar_events_near();'; ?>

                $('.btn-hide').click(function() {
                    $('.table_show').slideToggle();
                });

                
                
                $('#disable_end').change(function() {
                    var t = ($('[name="date_end"]').prop('disabled')) ? false : true;
                    $('[name="date_end"], [name="time_end"]').prop('disabled', t);
                });
                $('#disable_start').change(function() {
                    var t = ($('[name="time_start"]').prop('disabled')) ? false : true;
                    $('[name="time_start"]').prop('disabled', t).val('00:00');
                });

                function show_calendar_events_near() {
                    var send = [time_start, time_end, ID];
                    $.make_action('calendar_event_range_get', send, 'admin', function(res) {
                        $('.table_show').html(res.data);
                        check_calendar_events_near();
                    });
                }

                jQuery('#datetimepicker-start').datetimepicker({
                    lang: 'ru',
                    format:'Y-m-d',
                    formatDate: 'Y-m-d',
                    timepicker: false,
                    onShow: function(ct){
                        if(jQuery('#datetimepicker-end').val()) {
                            max = new Date(jQuery('#datetimepicker-end').val());
                        }
                        else {
                            max = false;
                        }
                        this.setOptions({
                            maxDate: max,
                            maxTime: false
                        });
                    },
                    onClose: function(ct) {
                        time_start = ct;
                        time_end = max;
                        <?php if($option) echo 'show_calendar_events_near();'; ?>
                    }
                });
                jQuery('#datetimepicker-end').datetimepicker({
                    lang: 'ru',
                    format:'Y-m-d',
                    formatDate: 'Y-m-d',
                    timepicker: false,
                    onShow: function(ct) {
                        if(jQuery('#datetimepicker-start').val()) {
                            min = new Date(jQuery('#datetimepicker-start').val());
                        }
                        else {
                            min = false;
                        }
                        this.setOptions({
                            minDate: min,
                            minTime: false
                        })
                    },
                    onClose: function(ct) {
                        time_end = ct;
                        time_start = min;
                        <?php if($option) echo 'show_calendar_events_near();'; ?>
                    }
                });
                jQuery('#timepicker-start').datetimepicker({
                    datepicker: false,
                    format:'H:i',
                    onShow: function(ct){
                        this.setOptions({
                            maxTime: (jQuery('#timepicker-end').val()) ? jQuery('#timepicker-end').val() : false
                        });
                    }
                });
                jQuery('#timepicker-end').datetimepicker({
                    datepicker: false,
                    format:'H:i',
                    onShow: function(ct) {
                        this.setOptions({
                            minTime: (jQuery('#timepicker-start').val()) ? jQuery('#timepicker-start').val() : false
                        })
                    }
                });
            });
        </script>
    <?php
        }
    ));

    add_action(array(
        'code'     => 'calendar_event_settings_tab',
        'rule'     => 'admin_settings',
        'category' => 'admin',
        'auto'     => 'settings',
        'zone'     => 'settings_tabs',
        'priority' => 100,
        'function' => function() {
            echo '<li><a href="#calendar" role="tab" data-toggle="tab">Календарь</a></li>';
        }
    ));

    add_action(array(
        'code'     => 'calendar_event_settings_panel',
        'rule'     => 'admin_settings',
        'category' => 'admin',
        'auto'     => 'settings',
        'zone'     => 'settings_panels',
        'priority' => 100,
        'function' => function() {
            $try = (get_option('calendar_events_range')) ? true : false;
        ?>
            <div class="tab-pane" id="calendar">
                <h3>Настройки календаря</h3>
                <div class="field form-group"><div class="checkbox">
                    <label>
                        <input name="calendar_events_range" type="checkbox" value="true" class="data-control"<?=($try) ? ' checked' : '';?>>
                        <?=_('Проверка перекрёстных событий');?>
                    </label>
                </div></div>
            </div>
        <?php
        }
    ));

    add_action(array(
        'code'     => 'check_calendar_event_settings',
        'rule'     => 'admin_settings',
        'category' => 'admin',
        'zone'     => 'settings_checking',
        'priority' => 100,
        'function' => function($res) {
            if(isset($res['calendar_events_range'])) {
                return update_option('calendar_events_range', 1);
            }
            else {
                return update_option('calendar_events_range', 0);
            }
            return false;
        }
    ));

    add_action(array(
        'code'     => 'calendar_event_proc',
        'category' => 'admin',
        'rule'     => 'calendar_event_admin',
        'function' => 'action_calendar_event_proc',
        'zone'     => 'before_template',
        'auto'     => 'calendar_event_add'
    ));

    function action_calendar_event_proc() {
        global $DETDB, $PAGE;

        $ID = null;

        $custom = array(
            'name'       => '',
            'place'      => '',
            'worker'     => '',
            'date_start' => date('Y-m-d H:i:s'),
            'date_end'   => '',
            'disable_start' => false,
            'disable_end'   => false
        );

        if(isset($_GET['event_id'])) {
            $ID = $_GET['event_id'];

            if($DETDB->isset_cell('calendar_events', $ID)) {
                $PAGE->title = 'Редактировать событие';
                $custom['ID'] = $ID;
            }
            else {
                $ID = null;
            }
        }

        set_glob_content(array(
            'body' => $custom
        ));

        if(isset($_POST['calendar_event_submit'])) {
            if($_POST['name'] && $_POST['worker'] && $_POST['date_start']) {
                $_POST['date_start'] = strtotime($_POST['date_start']);
                if(isset($_POST['time_start']) && $_POST['time_start']) {
                    $_POST['time_start'] = explode(':', date('H:i', strtotime($_POST['time_start'])));
                    for($i=0; $i<1; $i++) {
                        if($_POST['time_start'][$i][0] == '0') {
                            $_POST['time_start'][$i] = substr($_POST['time_start'][$i], 1);
                        }
                    }
                    $_POST['date_start'] += (intval($_POST['time_start'][0])*60 + intval($_POST['time_start'][1])) * 60;
                }
                $_POST['date_start'] = date('Y-m-d H:i', $_POST['date_start']);
                if(isset($_POST['date_end']) && $_POST['date_end']) {
                    $_POST['date_end'] = strtotime($_POST['date_end']);
                    if($_POST['time_end']) {
                        $_POST['time_end'] = explode(':', date('H:i', strtotime($_POST['time_end'])));
                        for($i=0; $i<1; $i++) {
                            if($_POST['time_end'][$i][0] == '0') {
                                $_POST['time_end'][$i] = substr($_POST['time_start'][$i], 1);
                            }
                        }
                        $_POST['date_end'] += (intval($_POST['time_end'][0])*60 + intval($_POST['time_end'][1])) * 60;
                    }
                    $_POST['date_end'] = date('Y-m-d H:i', $_POST['date_end']);
                }

                $custom = set_merge($custom, $_POST);
                $custom['name']   = secure_text($custom['name']);
                $custom['place']  = secure_text($custom['place']);
                $custom['worker'] = secure_text($custom['worker']);
                
                $custom['disable_end']   = ($custom['disable_end']) ? true : false;
                $custom['disable_start'] = ($custom['disable_start']) ? true : false;

                if($custom['disable_end']) {
                    $custom['date_end'] = '';
                }

                $send = $custom;
                unset($send['disable_end'], $send['disable_start']);
                $send['date_params'] = json_val_encode(array(
                    $custom['disable_start'],
                    $custom['disable_end']
                ));

                if(strtotime($custom['date_start']) <= strtotime($custom['date_end']) || $custom['date_end'] == '') {
                    if(!$ID && $DETDB->insert('calendar_events', $send)) {
                        push_output_message(array(
                            'text'  => 'Событие успешно добавлено',
                            'title' => 'Готово!',
                            'class' => 'alert alert-success',
                            'type'  => 'success'
                        ));
                    }
                    elseif($ID && $DETDB->update('calendar_events', $send, "WHERE ID='$ID'")) {
                        push_output_message(array(
                            'text'  => 'Событие успешно обновлено',
                            'title' => 'Готово!',
                            'class' => 'alert alert-success',
                            'type'  => 'success'
                        ));
                        set_glob_content(array(
                            'body' => $custom
                        ));
                    }
                    else {
                        push_output_message(array(
                            'text'  => 'Неизвестная ошибка',
                            'class' => 'alert alert-danger',
                            'type'  => 'error'
                        ));
                        set_glob_content(array(
                            'body' => $custom
                        ));
                    }
                }
                else {
                    push_output_message(array(
                        'text'  => 'Дата начала должна быть меньше или равна дате конца события',
                        'title' => 'Ошибка!',
                        'class' => 'alert alert-danger',
                        'type'  => 'error'
                    ));
                    set_glob_content(array(
                        'body' => $custom
                    ));
                }
            }
            else {
                push_output_message(array(
                    'text'  => 'Заполните все поля',
                    'title' => 'Ошибка!',
                    'class' => 'alert alert-warning',
                    'type'  => 'warning'
                ));
                set_glob_content(array(
                    'body' => $custom
                ));
            }
        }
        else {
            if($ID && $res = (array) $DETDB->select('calendar_events', '*', true, "WHERE ID='$ID'")) {
                $custom = set_merge($custom, $res);
                if($res['date_params'] && check_json($res['date_params'])) {
                    $res['date_params'] = json_decode($res['date_params'], true);
                    if(count($res['date_params']) == 2) {
                        $custom['disable_start'] = $res['date_params'][0];
                        $custom['disable_end']   = $res['date_params'][1];
                    }
                }
            }
            set_glob_content(array(
                'body' => $custom
            ));
        }
    }

    add_action(array(
        'code'     => 'ajax_delete_calendar_event',
        'category' => 'admin',
        'rule'     => 'calendar_event_admin',
        'function' => function($params) {
            global $DETDB;
            if(isset($params['window']['pre_window']['value']) && $params['window']['pre_window']['type'] == 'ID' && $DETDB->delete('calendar_events', $params['window']['pre_window']['value'])) {
                echo ajax_make_res('success', 'Событие успешно удалено', 'Успех!');
            }
            else {
                echo ajax_make_res('error', 'Произошла неизвестная ошибка', 'Ошибка!');
            }
        }
    ));

    add_action(array(
        'code'     => 'ajax_calendar_event_range_get',
        'category' => 'admin',
        'rule'     => 'calendar_event_admin',
        'function' => function($params=null) {
            $params = (array) $params;
            $copy = $params;

            if($params[0]) {
                $params[0] = date('Y-m-d H:i', strtotime($copy[0]) - 36000);
            }
            else {
                $params[0] = date('Y-m-d H:i', strtotime($copy[1]) - 36000);
            }

            if($params[1]) {
                $params[1] = date('Y-m-d H:i', strtotime($copy[1]) + 36000);
            }
            else {
                $params[1] = date('Y-m-d H:i', strtotime($copy[0]) + 36000);
            }

            $ID = ($params[2]) ? $params[2] : '';

            show_calendar_events(array(
                'limit'      => 15,
                'start'      => $params[0],
                'end'        => $params[1],
                'type'       => 'range',
                'exclude_ID' => $ID
            ));
        }
    ));

    function get_calendar_months() {
        return array('1'=> 'Январь', '2'=> 'Февраль', '3'=> 'Март', '4'=> 'Апрель', '5'=> 'Май', '6'=> 'Июнь', '7'=> 'Июль', '8'=> 'Август', '9'=> 'Сентябрь', '10'=> 'Октябрь', '11'=> 'Ноябрь', '12'=> 'Декабрь');
    }

    function show_calendar_events($params = array()) {
        global $DETDB;

        $custom = array(
            'links'      => false,     //отображение админских ссылок
            'type'       => 'month',   //month / range
            'month'      => ((isset($_GET['month']) && is_numeric($_GET['month'])) ? $_GET['month'] : date('m')), //месяц
            'year'       => ((isset($_GET['year']) && is_numeric($_GET['year'])) ? $_GET['year'] : date('Y')),
            'start'      => '',        //начало диапазона
            'end'        => '',        //конец диапазон
            'show'       => 'table',   // table / json
            'exclude_ID' => null,
            'limit'      => 100
        );

        $query = null;

        if(is_merged($params)) {
            $custom = set_merge($custom, $params);

            if($custom['start']) $custom['start'] = date('Y-m-d H:i', strtotime($custom['start']));
            if($custom['end'])   $custom['end'] = date('Y-m-d H:i', strtotime($custom['end']));
            $custom['year']  = (is_numeric($custom['year']) && checkdate('01', '01', $custom['year'])) ? $custom['year'] : date('Y');
            $custom['month'] = (is_numeric($custom['month']) && checkdate($custom['month'], '01', $custom['year'])) ? $custom['month'] : date('m');

            if($custom['type'] == 'month') {
                $query = array(
                    array(
                        'param' => 'MONTH(date_start)',
                        'value' => $custom['month']
                    ),
                    array(
                        'param' => 'YEAR(date_start)',
                        'value' => $custom['year']
                    )
                );
            }
            else {
                $query = array(
                    array(
                        'param'    => 'date_end',
                        'value'    => $custom['start'],
                        'relation' => '>='
                    ),
                    array(
                        'param'    => 'date_start',
                        'value'    => $custom['end'],
                        'relation' => '<='
                    )
                );
            }

            if($custom['exclude_ID'] && !is_array($custom['exclude_ID'])) {
                $custom['exclude_ID'] = array($custom['exclude_ID']);
            }
            if($custom['exclude_ID']) {
                foreach($custom['exclude_ID'] as $item) {
                    $query[] = array(
                        'param'    => 'ID',
                        'value'    => $item,
                        'relation' => '!='
                    );
                }
            }
        }

        $events = ($query) ? $DETDB->select(array(
            'table'      => 'calendar_events',
            'cols'       => '*',
            'cond'       =>  $query,
            'order_by'   => 'date_start',
            'order_type' => 'ASC',
            'limit'      => $custom['limit']
        )) : null;
    ?>
        <?php if($events && count($events) > 0 && $custom['show'] == 'table'): ?>
            <table class="table table-striped table-hover table-bordered" width="100%">
                <thead>
                    <tr>
                        <th width="7%">Дата</th>
                        <th width="7%">Время</th>
                        <th>Наименование  мероприятия</th>
                        <th width="18%">Место проведения</th>
                        <th width="14%">Ответственный</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($events as $item): ?>
                        <?php
                            $start = strtotime($item->date_start);
                            $end   = strtotime($item->date_end);
                        ?>
                        <tr class="field">
                            <td>
                                <?php
                                    echo date('d', $start);
                                    if($end && $end > 0 && date('d', $start) != date('d', $end)) echo ' - ' . date('d', $end);
                                ?>
                            </td>
                            <td>
                                <?php
                                    if($item->date_params && check_json($item->date_params)) {
                                        $item->date_params = json_decode($item->date_params, true);
                                        if(count($item->date_params) == 2) {
                                            $item->date_params = !($item->date_params[0]);
                                        }
                                    }
                                    else {
                                        $item->date_params = true;
                                    }
                                    if($start && $item->date_params) {
                                        echo date('G.i', $start);
                                        if($end && $end > 0 && $start && $end != $start) {
                                            echo ' - ' . date('G.i', $end);
                                        }
                                    }
                                ?>
                            </td>
                            <td>
                                <p><?=$item->name;?></p>
                            <?php if($custom['links']): ?>
                                <p class="controls">
                                    <a href="<?=get_page_link('calendar_event_add').'&event_id='. $item->ID;?>">Редактировать</a> |
                                    <a class="table-delete button-control" data-set-action="delete_calendar_event" data-id="<?=$item->ID;?>">Удалить</a>
                                </p>
                            <?php endif;?>
                            </td>
                            <td><?=$item->place;?></td>
                            <td><?=$item->worker;?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Дата</th>
                        <th>Время</th>
                        <th>Наименование  мероприятия</th>
                        <th>Место проведения</th>
                        <th>Ответственный</th>
                    </tr>
                </tfoot>
            </table>
        <?php elseif($custom['show'] == 'json'): ?>
            <?=json_val_encode($events); ?>
        <?php else: ?>
            <p>Нет событий, выбранных за данный период.</p>
        <?php endif; ?>
    <?php

    }
?>