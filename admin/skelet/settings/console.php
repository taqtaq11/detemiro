<?php
    push_output_message(array(
        'title'  => 'Внимание!',
        'text'   => 'В данной консоли возможно выполнение любого PHP-кода, с использованием особенностей Detemiro.
                    <br />
                    Будьте предельно осторожны.',
        'type'   => 'danger',
        'class'  => 'alert alert-danger',
        'closed' => false
    ));

    $tab = (isset($_POST['input'])) ? true : false;
?>

<div class="content-tags">
    <ul class="nav nav-tabs" role="tablist">
        <li<?=(!$tab)?' class="active"':''?>><a href="#input" role="tab" data-toggle="tab">Ввод</a></li>
        <li<?=($tab)?' class="active"':''?>><a href="#output" role="tab" data-toggle="tab">Вывод</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane<?=(!$tab)?' active':''?>" id="input">
            <div class="code-input">
                <h3>Введите PHP-код</h3>
                <form method="POST">
                    <textarea name="input" class="form-control" rows="10"><?php
                        echo get_glob_content()[0];
                    ?></textarea>
                    <p class="help-block">
                        <span class="glyphicon glyphicon-hand-right"></span> Теги &lt;? ?&gt; вводить не нужно.
                    </p>
                    <p class="help-block">
                        <span class="glyphicon glyphicon-hand-right"></span> Для SQL-запросов используйте глобальную переменную $DETDB.
                    </p>
                    <p class="help-block">
                        <span class="glyphicon glyphicon-hand-right"></span> Фатальные ошибки не предотвращаются.
                    </p>
                    <hr />
                    <button class="btn btn-primary"><span class="glyphicon glyphicon-flash"></span> Выполнить</button>
                </form>
            </div>
        </div>
        <div class="tab-pane<?=($tab)?' active':''?>" id="output">
            <div class="code-output">
                <h3>Результат</h3>
                <form method="POST">
                    <textarea name="output" class="form-control" rows="10"><?php
                        echo get_glob_content()[1];
                    ?></textarea>
                </form>
            </div>
        </div>
    </div>
</div>