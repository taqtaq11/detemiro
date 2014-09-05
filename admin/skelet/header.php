<!DOCTYPE html>
<html>
<head>
    <title><?php page_title('Админ-панель'); ?></title>
    <?php page_header(); ?>
</head>
<body>
    <header id="main-header" class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="pull-right login-panel">
                <form method="POST" class="form-inline pull-right"><button name="logout" class="btn btn-danger">Выйти</button></form>
                <?php
                    echo '<span>Привет, ' . current_user()->display_name . '</span>';
                ?>
            </div>
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Переключить навигацию</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?=get_page_link('index'); ?>"><?='Админ-панель';?></a>
            </div>
            <nav id="main-nav" class="navbar-collapse collapse" role="navigation">
                <?php apage_navigation(array(
                    'class'      => 'nav navbar-nav navbar-left nav-bootstrap nav-double',
                    'depth'      => 3,
                    'double_max' => 1
                )); ?>
            </nav>
        </div>
    </header>
    <div id="container" class="container-fluid">
        <div class="row">
            <?php get_template('secbar.php');?>
            <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                <h1 class="page-header"><?=$PAGE->title ?></h1>
                <div id="breadcrumbs">
                    <?php breadcrumbs(); ?>
                </div>
                <?php show_output_result_messages(); ?>