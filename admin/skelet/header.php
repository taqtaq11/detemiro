<!DOCTYPE html>
<html>
<head>
    <title><?php page_title('Админ-панель'); ?></title>
    <?php page_header(); ?>
</head>
<body>
    <header id="main-header" class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div id="login-panel" class="pull-right detwork-control"><ul class="nav navbar-nav navbar-right nav-bootstrap">
                <?php $my_url = (check_rule('admin_users')) ? get_page_link('user_info') . '&user_id=' . current_user('ID') : '#'; ?>
                <li id="login-panel" class="item-settings dropdown">
                    <a href="<?=$my_url;?>" class="dropdown-toggle" data-toggle="dropdown"><?=current_user('display_name');?></a>
                    <ul class="subnav dropdown-menu" data-level="1">
                        <?php if($my_url != '#'):?>
                            <li><a href="<?=$my_url;?>">Информация</a></li>
                            <li><a href="<?=get_page_link('edit_user') . '&user_id=' . current_user('ID');?>">Настройка</a></li>
                        <?php endif; ?>
                        <li><a class="button-control" data-action="logout">Выйти</a></li>
                    </ul>
                </li>
            </ul></div>
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Переключить навигацию</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
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