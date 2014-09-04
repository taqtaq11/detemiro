<?php
    //JQuery UI theme
    add_script(array(
        'code'     => 'jquery_ui_theme',
        'type'     => 'style',
        'link'     => get_file('css/ui/jquery-ui-1.10.0.custom.css'),
        'zone'     => 'header',
        'priority' => 8,
        'category' => 'admin'
    ));

    //AJAX worker
    add_script(array(
        'code'     => 'detwork_plugin',
        'type'     => 'script',
        'link'     => BASE_URL . '/' . ADMIN . INCLUDES . '/js/jquery.detwork.min.js',
        'zone'     => 'footer',
        'priority' => 5,
        'category' => 'admin',
        'function' => function() {
?>
    <script>
        $(function() {
            $('.detwork-control').detwork();
        });
    </script>
<?php
        }
    ));
    add_script(array(
        'code'     => 'detable_plugin',
        'type'     => 'script',
        'link'     => BASE_URL . '/' . ADMIN . INCLUDES . '/js/jquery.detable.min.js',
        'zone'     => 'footer',
        'priority' => 6,
        'category' => 'admin',
        'function' => function() {
?>
    <script>
        $(function() {
            $('.detwork-control').detable();
        });
    </script>
<?php
        }
    ));

    //Select2
    add_script(array(
        'code'     => 'select2_css',
        'type'     => 'style',
        'link'     => get_file('js/select2/select2.css'),
        'zone'     => 'header',
        'priority' => 41,
        'category' => 'admin'
    ));

    add_script(array(
        'code'     => 'select2_bootstrap_css',
        'type'     => 'style',
        'link'     => get_file('js/select2/select2-bootstrap.css'),
        'zone'     => 'header',
        'priority' => 42,
        'category' => 'admin'
    ));

    add_script(array(
        'code'      => 'select2_js',
        'type'      => 'script',
        'link'      => get_file('js/select2/select2.min.js'),
        'zone'      => 'footer',
        'priority'  => 40,
        'category'  => 'admin',
        'function'  => function() {
?>
    <script>
        $(function() {
            $('select.form-control').select2({
                width: 'element',
                containerCssClass: 'skip'
            }).attr('data-custom-get', 'get_select2_value');
            function get_select2_value(input) {
                return ($(input).hasClass('select2-offscreen')) ? $(input).select2('val') : null;
            }
            $.add_function(get_select2_value);
        });
    </script>
<?php
        }
    ));

    //CKEditor
    add_script(array(
        'code'     => 'ckeditor',
        'type'     => 'script',
        'zone'     => 'header',
        'link'     => get_file('js/ckeditor/ckeditor.js'),
        'priority' => 50,
        'category' => 'admin'
    ));

    add_script(array(
        'code'     => 'ckeditor_adapter',
        'type'     => 'script',
        'zone'     => 'header',
        'link'     => get_file('js/ckeditor/adapters/jquery.js'),
        'priority' => 51,
        'category' => 'admin',
        'function' => function() {
?>
    <script>
        $(function() {
            $('textarea.editor').each(function() {
                if($(this).hasClass('editor-mini')) {
                    $(this).ckeditor({
                        height: 100,
                        skin: 'bootstrapck',
                        extraPlugins: 'tab,codesnippet',
                        tabIndex: 4,
                        tabSpaces: 4,
                        toolbar: [
                            {name: 'basicstyles', items : ['Bold','Italic','Underline']},
                            {name: 'links', items: [ 'Link', 'Unlink' ]}
                        ]
                    });
                }
                else {
                    $(this).ckeditor({
                        height:         250,
                        skin:          'bootstrapck',
                        extraPlugins:  'tab,codesnippet',
                        removePlugins: 'about,scayt',
                        tabIndex:       4,
                        tabSpaces:      4
                    })
                }
                $(this).attr('data-custom-get', 'get_ckeditor_value');
            });
            function get_ckeditor_value(input) {
                input = input || 'textarea.editor';
                return ($(input).val());
            }
            $.add_function(get_ckeditor_value);
        });
    </script>
<?php
        }
    ));
?>