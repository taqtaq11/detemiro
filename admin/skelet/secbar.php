<aside id="secbar" class="col-sm-3 col-md-2 sidebar">
    <?php
        apage_navigation(array(
            'class'      => 'nav nav-sidebar',
            'depth'      => 3,
            'double_max' => 0
        ));

        /*if($PAGE->code == 'index') {
            apage_navigation(array(
                'class'      => 'nav nav-sidebar',
                'depth'      => 2,
                'double_max' => 0
            ));
        }
        else apage_navigation(array(
            'parent'   => apage_parent(),
            'class'    => 'nav nav-sidebar',
            'double_max' => 0
        ));*/
    ?>
</aside>