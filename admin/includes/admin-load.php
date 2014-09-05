<?php
    //Main part
    require_once(ABSPATH . '/' . ADMIN . INCLUDES . '/admin-template.php');
    require_once(ABSPATH . '/' . ADMIN . INCLUDES . '/admin-base-pages.php');
    require_once(ABSPATH . '/' . ADMIN . INCLUDES . '/admin-base-scripts.php');
    require_once(ABSPATH . '/' . ADMIN . INCLUDES . '/admin-base-actions.php');

    //Sec part
    require_once(ABSPATH . '/' . ADMIN . INCLUDES . '/admin-settings.php');
    require_once(ABSPATH . '/' . ADMIN . INCLUDES . '/admin-users.php');
    require_once(ABSPATH . '/' . ADMIN . INCLUDES . '/admin-modules.php');
    require_once(ABSPATH . '/' . ADMIN . INCLUDES . '/admin-detblocks.php');

    //Last part
    get_template('main.php');
?>