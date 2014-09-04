<?php
    $month = (isset($_GET['month'])) ? $_GET['month'] : date('m');
?>

<div id="calendar_events">
    <form methot="GET">
        <input type="hidden" name="page" value="<?=get_current_key(); ?>" />
        <select name="month" onchange="this.form.submit()">
            <?php
                foreach(get_calendar_months() as $key=>$item) {
                    echo '<option value="' .$key . '"' . (($key == $month) ? ' selected' : '') . '>' . $item . '</option>';
                }
            ?>
        </select>
    </form>

    <?php
        show_calendar_events(array(
            'month' => $month,
            'links' => false
        ));
    ?>
</div>