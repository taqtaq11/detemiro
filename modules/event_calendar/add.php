<?php
    $event = get_glob_content();
    $try = get_option('calendar_events_range');
?>

<?php if($try): ?>
    <div id="show_graphic">
        <h3>Ближайшие мероприятия в диапазоне 10 часов <a class="btn-hide btn btn-warning btn-xs pull-right">скрыть</a></h3>
        <div class="table_show">
        <?php
            if($event) {
                $start_date = date('Y-m-d H:i', strtotime($event['date_start']) - 36000);

                $end_date   = ($event['date_end'] && $event['date_end'] > 0) ? date('Y-m-d H:i', strtotime($event['date_end']) + 36000) : date('Y-m-d H:i', strtotime($event['date_start']) + 36000);

                show_calendar_events(array(
                    'limit'      => 15,
                    'exclude_ID' => ((isset($event['ID'])) ? $event['ID'] : ''),
                    'start'      => $start_date,
                    'end'        => $end_date,
                    'type'       => 'range'
                ));
            }
        ?>
        </div>
    </div>
    <br />
<?php endif; ?>

<div id="add_event">
    <form method="POST" action="index.php?page=<?=get_current_key() . ((isset($event['ID'])) ? '&event_id=' . $event['ID'] : '');?>">
        <fieldset>
            <legend>Общая информация</legend>
            <div class="row">
                <div class="field form-group col-lg-8">
                    <label>Наименование  мероприятия</label>
                    <textarea name="name" class="editor editor-mini form-control" rows="5"><?=$event['name'];?></textarea>
                </div>
            </div>
            <div class="row">
                <div class="field form-group col-lg-8">
                    <label>Место проведения</label>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-list-alt"></i></span>
                        <input type="text" name="place" class="form-control" value="<?=$event['place'];?>" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="field form-group col-lg-8">
                    <label>Ответственный</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <textarea name="worker" class="form-control" required><?=$event['worker'];?></textarea>
                        </div>
                </div>
            </div>
        </fieldset>
        <fieldset>
            <legend>Даты</legend>
                <div class="row">
                    <div class="field form-group col-lg-4">
                        <label>Дата начала</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                            <input id="datetimepicker-start" class="datetimepicker form-control" name="date_start" type="text"  value="<?=date('Y-m-d', strtotime($event['date_start']));?>" required />
                        </div>
                    </div>
                    <div class="field form-group col-lg-4">
                        <label>Время начала</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                            <input id="timepicker-start" class="datetimepicker form-control" name="time_start" type="text"  value="<?=date('H:i', strtotime($event['date_start']));?>" required />
                        </div>
                    </div>
                </div>
                <?php if($try): ?><p class="help-block">После обновления даты ниже вы увидите ближайшие мероприятия для новой даты.</p><?php endif;?>
                <div class="row">
                    <div class="field form-group col-lg-4"><div class="checkbox">
                        <label id="disable_start"><input type="checkbox" value="off" name="disable_start" <?=($event['disable_start']) ? 'checked' : '';?>/> Использовать только дату</label>
                    </div></div>
                </div>
                <hr />
                <div class="row">
                    <div class="field form-group col-lg-4">
                        <label>Дата конца</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                            <input id="datetimepicker-end" class="datetimepicker form-control" name="date_end" type="text" value="<?=($event['date_end'] && $event['date_end'] > 0) ? date('Y-m-d', strtotime($event['date_end'])) : '';?>" <?=($event['disable_end']) ? 'disabled' : '';?>/>
                        </div>
                    </div>
                    <div class="field form-group col-lg-4">
                        <label>Время конца</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                            <input id="timepicker-end" class="datetimepicker form-control" name="time_end" type="text"  value="<?=($event['date_end'] && $event['date_end'] > 0) ? date('H:i', strtotime($event['date_end'])) : '';?>" <?=($event['disable_end']) ? 'disabled' : '';?>/>
                        </div>
                    </div>
                </div>
                <?php if($try): ?><p class="help-block">После обновления даты ниже вы увидите ближайшие мероприятия для новой даты.</p><?php endif;?>
                <div class="row">
                    <div class="field form-group col-lg-4"><div class="checkbox">
                        <label id="disable_end"><input type="checkbox" value="off" name="disable_end" <?=($event['disable_end']) ? 'checked' : '';?>/> Отключить дату конца</label>
                    </div></div>
                </div>
        </fieldset>
        <hr />
        <button name="calendar_event_submit" type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span>  <?=(isset($event['ID'])) ? 'Обновить' : 'Добавить';?></button>
        <?php if(isset($event['ID'])): ?>
            <input type="hidden" name="event_ID" value="<?=$event['ID'];?>" />
        <?php endif; ?>
    </form>
</div>