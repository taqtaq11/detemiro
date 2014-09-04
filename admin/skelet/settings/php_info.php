<?php
    //Bootstrap Style
    ob_start();
    phpinfo();
    $phpinfo = ob_get_clean();

    $phpinfo = preg_replace('#^.*<body>(.*)</body>.*$#s', '$1', $phpinfo);
    $phpinfo = str_replace('module_Zend Optimizer', 'module_Zend_Optimizer', $phpinfo);
    $phpinfo = str_replace('<font', '<span', $phpinfo);
    $phpinfo = str_replace('</font>', '</span>', $phpinfo);
    $phpinfo = str_replace( 'border="0" cellpadding="3"', 'class="table table-bordered table-striped" style="table-layout: fixed;word-wrap: break-word;"', $phpinfo );
    $phpinfo = str_replace('<tr class="h"><th>', '<thead><tr><th>', $phpinfo);
    $phpinfo = str_replace('</th></tr>', '</th></tr></thead><tbody>', $phpinfo);
    $phpinfo = str_replace('</table>', '</tbody></table>', $phpinfo);
    $phpinfo = preg_replace('#>(on|enabled|active)#i', '><span class="text-success">$1</span>', $phpinfo);
    $phpinfo = preg_replace('#>(off|disabled)#i', '><span class="text-error">$1</span>', $phpinfo);
    echo '<div id="phpinfo">';
    echo $phpinfo;
    echo '</div>';
?>