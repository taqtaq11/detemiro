(function($){
jQuery.fn.detwork = function() {
    var doit = function() {
        //Preparing
        var handler = '../includes/basic-ajax.php',
            selector = $(this).add($(this).find('.dialog-basic')),
            input_types = 'input, select, textarea'

        $(this).find('.button-control').parents('form').submit(function() {
            return false;
        });

        console.log(selector);

        //Получение параметров
        function get_params(block) {
            block = block || selector;

            var res = {
                action:        '',
                input:         '',
                window:        '',
                set:           '',
                custom_getter: '',
                pre_function:  '',
                post_function: '',
                pre_window:    '',
                extra:         ''
            }

            if($(block).length) {
                if($(block).is('[data-window]')) {
                    res.window = $(block).attr('data-window');

                    if(!$('#' + res.window).length) {
                        res.window = '';
                    }
                }

                res.action        = $(block).get_data('action', '');
                res.input         = $(block).get_data('input', '');
                res.set           = $(block).get_data('set-action', '');
                res.custom_getter = $(block).get_data('custom-getter', '');
                res.pre_function  = $(block).get_data('pre-function', '');
                res.post_function = $(block).get_data('post-function', '');
                res.pre_window    = $(block).get_data('pre-window', '');
                res.parent        = $(block).get_data('parent', '');
                res.extra         = $(block).get_data('extra', '');

                if(!res.input && $(block).hasClass('input-control')) {
                    res.input = '.data-control';
                }

                if(res.set && $('#' + res.window).length) {
                    $('#' + res.window).attr('data-action', res.set);
                }
                if(res.pre_window && $('#' + res.window).length) {
                    $('#' + res.window).attr('data-pre-window', res.pre_window);
                }
            }

            return res;
        }

        //Обработка кликов
        $(this).on('click', '.button-control', function() {
            var send = {
                    'button': {
                        'block': this,
                        'input': null,
                        'extra': ''
                    }
                },
                times = null,
                ways = [],
                params = get_params(this);

            if(params.extra) {
                send.button['extra'] = params.extra;
            }

            //Получаю значения
            if(params.input || params.custom_getter) {
                if(params.custom_getter && isset_function(params.custom_getter)) {
                    times = $.call_function(params.custom_getter, send);
                }
                if(times === null && params.input) {
                    times = $(selector).get_universal_values(params.input);
                }
                if($.isset(times)) {
                    send.button['input'] = times;
                }
            }

            //Вызываю pre-функцию обработки
            if(params.pre_function) {
                times = $.call_function(params.pre_function, send);
                if($.isset(times)) {
                    send.button['pre'] = times;
                }
            }

            ways[0] = function() {
                //Вызываю post-функцию обработки
                if(params.post_function) {
                    times = $.call_function(params.post_function, send.button);
                    if($.isset(times)) {
                        send.button['post'] = times;
                    }
                }

                //Вызваю окшно
                if(params.window) {
                    //Аналогично
                    var window = $('#' + params.window);
                    params = get_params(window);

                    send['window'] = {
                        'block': window,
                        'input': null,
                        'extra': ''
                    };
                    if(params.extra) {
                        send.window['extra'] = params.extra;
                    }

                    $(window).dialog({
                        resizable: false,
                        modal: true,
                        open: function() {
                            $('.ui-widget-overlay').hide().fadeIn(300);

                            if(params.pre_window) {
                                times = $.call_function(params.pre_window, send);
                                if($.isset(times)) {
                                    send.window['pre_window'] = times;
                                }
                            }
                        },
                        show: {
                            effect: 'fade',
                            duration: 300
                        },
                        beforeClose: function(){
                            $('body').append($('.ui-widget-overlay').clone());
                            if(!$('.ui-widget-overlay').is('#background-loader')) {
                                $('.ui-widget-overlay').fadeOut(300, function() {
                                    $('.ui-widget-overlay').remove();
                                });
                            }
                        },
                        close: function() {
                            if(params.input) {
                                $(window).clear_input_value(params.input);
                            }
                        },
                        buttons: {
                            'OK': function() 
                            {
                                if(params.input || params.custom_getter) {
                                    times = null;
                                    if(params.custom_getter && isset_function(params.custom_getter)) {
                                        times = $.call_function(params.custom_getter, send);
                                    }
                                    if(times === null && params.input) {
                                        times = $(window).get_universal_values(params.input);
                                    }
                                    if($.isset(times)) {
                                        send.window['input'] = times;
                                    }
                                }
                                if(params.pre_function) {
                                    times = $.call_function(params.pre_function, send);
                                    if($.isset(times)) {
                                        send.window['pre'] = times;
                                    }
                                }

                                ways[1] = function() {
                                    if(params.post_function) {
                                        times = $.call_function(params.post_function, send);
                                        if($.isset(times)) {
                                            send.window['post'] = times;
                                        }
                                    }

                                    $(window).dialog('close');
                                }

                                if(params.action) {
                                    times = {};
                                    $.each(send, function(group, item) {
                                        times[group] = {};
                                        $.each(item, function(key, value) {
                                            if(!(value instanceof HTMLElement) && key != 'block') {
                                                times[group][key] = value;
                                            }
                                        });
                                    });
                                    $.make_action(params.action, times, 'admin', function(data) {
                                        if($.isset(data)) send.window['action'] = data;
                                        ways[1]();
                                    });
                                }
                                else {
                                    ways[1]();
                                }
                            },
                            'Отмена': function() {
                                $(this).dialog('close');
                            }
                        }
                    });
                }
            }

            //Выполняю экшн
            if(params.action) {
                times = {};
                $.each(send, function(group, item) {
                    times[group] = {};
                    $.each(item, function(key, value) {
                        if(!(value instanceof HTMLElement) && key != 'block') {
                            times[group][key] = value;
                        }
                    });
                });
                $.make_action(params.action, times, 'admin', function(data) {
                    if($.isset(data)) send.button['action'] = data;
                    ways[0]();
                });
            }
            else {
                ways[0]();
            }
        });

        //Копирование поля
        $(selector).on('click', '.button-more', function() {
            $(this).make_field_clone();
        });

        $(selector).on('click', '.clone-remove', function() {
            $(this).parents('.clone').remove();
        });

        //end doit
    };

    return this.each(doit);
};
}) (jQuery);