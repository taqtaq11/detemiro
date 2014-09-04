(function($){
jQuery.fn.detable = function(opt) {
    opt = $.extend({
        sth: 'sth'
    }, opt);

    var doit = function() {
        var selector = $(this);

        if($(selector).find('.table-edit')) {
            $(selector).find('.table-edit').each(function() {
                if(!$(this).get_data('pre-function')) {
                    $(this).attr('data-pre-function', 'table_get_ID');
                }
                if(!$(this).get_data('pre-window')) {
                    $(this).attr('data-pre-window', 'set_window_values');
                }
            });
        }

        if($(selector).find('.table-delete')) {
            $(selector).find('.table-delete').each(function() {
                if(!$(this).get_data('data-window')) {
                    $(this).attr('data-window', 'table-delete');
                }
            });
        }

        if($(selector).find('[data-window="table-delete"]') && !$('#table-delete').length) {
            $(selector).last().append(
                             '<div id="table-delete" class="dialog-basic" title="Внимание!" ' + 
                             'data-pre-window="table_get_ID" data-post-function="table_delete"' +
                             '>Вы действительно хотите удалить элемент?</div>'
                                    );
        }

        function table_get_ID(params) {
            var block = params.button['block'];
            var ID = $(block).get_ID('tr, .field, .parenter', true);
            return ID;
        }
        $.add_function(table_get_ID);

        function table_delete(params) {
            var status = $.get_property(params, 'window', 'action', 'status');
            if($.isset(status) && status == 'success') {
                if($(params.button['block']).parents('.field, .parenter').siblings().length == 0) {
                    location.reload();
                }
                else $(params.button['block']).parents('.field, .parenter').fadeOut(600, function() {
                    $(this).remove();
                });
            }
        }
        $.add_function(table_delete);

        //Установка значений
        function set_window_values(param) {
            if($.isset($.get_property(param, 'button', 'action', 'data')) && $.isset($.get_property(param, 'window', 'block'))) {
                $(param.window['block']).set_input_value(param.button['action']['data']);
            }
        }

        $.add_function(set_window_values);
    };

    return this.each(doit);
};
}) (jQuery);