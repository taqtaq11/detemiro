(function ($) {
    //Functions
    $.extend({
        check_json: function(str) {
            var is_json = true;

            try {
               var json = $.parseJSON(str);
            }
            catch(err) {
               is_json = false;
            }

            return is_json;
        },
        size_json: function(str) {
            if(check_json(str)) {
                str = $.parseJSON(str);
            }
            return str.length;
        },
        dump: function(obj) {
            var out = '';

            if(obj && typeof(obj) == 'object'){
                for (var i in obj) {
                    out += i + ': ' + obj[i] + "\n";
                }
            }
            else {
                out = obj;
            }

            alert(out);
        },
        isset: function(data) {
            return (typeof(data) != 'undefined');
        },
        set_merge: function(arr1, arr2, empty) {
            empty = empty || false;

            if(arr1 && arr2) {
                $.each(arr2, function(key, value) {
                    if($.isset(arr1[key]) && (empty == false || empty == true && (arr1[key] === null || arr1[key] === ''))) {
                        arr1[key] = value;
                    }
                });
            }

            return arr1;
        },
        detalert: function(text, title, callback) {
            title = title || 'Внимание!';
            callback = callback || null;
            
            if(jQuery.ui) {
                $('<div></div>').html('<p>' + text + '</p>').dialog({
                    title: title,
                    resizable: false,
                    modal: true,
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
                    open: function() {
                        $('.ui-widget-overlay').hide().fadeIn(300);
                    },
                    buttons: {
                        'OK': function() 
                        {
                            $(this).dialog('close');
                        }
                    },
                    close: function() {
                        if(callback && typeof(callback) === "function") {
                            callback();
                        }
                    }
                });
            }
            else {
                alert(text);
            }
        },
        detconfirm: function(text, title, callback) {
            title = title || 'Внимание!';
            callback = callback || null;
            
            if(jQuery.ui) {
                var t = false;
                $('<div></div>').html('<p>' + text + '</p>').dialog({
                    title: title,
                    resizable: false,
                    modal: true,
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
                    open: function() {
                        $('.ui-widget-overlay').hide().fadeIn(300);
                    },
                    buttons: {
                        'Да': function() 
                        {
                            t = true;
                            $(this).dialog('close');
                        },
                        'Отмена': function() {
                            t = false;
                            $(this).dialog('close');
                        }
                    },
                    close: function() {
                        if(callback && typeof(callback) === "function") {
                            callback(t);
                        }
                        return t;
                    }
                });
            }
            else {
                return (confirm(text));
            }
        },
        detmessage: function(text, title, suff, hide, block) {
            text  = text  || null;
            title = title || null;
            suff  = suff  || null
            block = block || null;
            hide  = hide  || null;
            var custom = {
                'title': 'Внимание!',
                'text': '',
                'suff': 'warning',
                'block': '#output-messages',
                'hide': null
            };

            if(typeof(text) == 'object') {
                custom = $.set_merge(custom, text, false);
            }
            else {
                if(title) {
                    custom.title = title;
                }
                if(text !== null) {
                    custom.text = text;
                }
                if(suff) {
                    custom.suff = suff;
                }
                if(hide !== null) {
                    custom.hide = hide;
                }
                if(block) {
                    custom.block = block;
                }
            }

            if(custom.suff == 'error' || custom.suff == 'loading' || custom.suff == '') {
                custom.suff = 'danger';
            }
            if(custom.suff == 'reload') {
                custom.suff = 'success';
            }

            //var y = $(custom.block).position().top - 35;
            var i = $(custom.block).find('.message-block').length;

            $(custom.block).append(
                $(
                '<div class="message-block message-generate alert alert-' + custom.suff + '" data-message="' + i + '">' + 
                '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span></button>' + 
                '<h4 class="message-title">' + custom.title + '</h4>' + 
                '<div class="messsage-body">' + custom.text + '</div>' +
                '</div>'
                ).hide()
            );
            var message = $(custom.block).find('.message-block[data-message="' + i + '"]');

            $(message).fadeIn(800);

            //window.scrollTo(0, y);

            if(custom.hide !== null) {
                setTimeout(function() {
                    $(message).fadeOut(800, function() {
                        $(this).remove();
                    });
                }, custom.hide);
            }
        },
        add_function: function(fn) {
            if($.isset(FUNCTIONS)) {
                FUNCTIONS[fn.name] = fn;
            }
        },
        isset_function: function(name) {
            return ($.isset(FUNCTIONS) && $.isset(FUNCTIONS[name]));
        },
        call_function: function(name, params, callback) {
            params = params || null;
            callback = callback || null;

            if($.isset_function(name)) {
                if(callback && typeof(callback) == 'function') {
                    callback(FUNCTIONS[name](params));
                }
                else {
                    return FUNCTIONS[name](params);
                }
            }
            else {
                return false;
            }
        },
        preload: function(type) {
            type = type || 'loading';

            if(!$('#background-loader').length || !$('#background-loader .status-block').length) {
                if($('.ui-widget-overlay.ui-front').length) {
                    $('.ui-widget-overlay.ui-front').attr('id', 'background-loader');
                }
                else {
                    $('body').append(
                        '<div id="background-loader" style="display: none"></div>'
                    );
                }
                $('#background-loader').html(
                    '<div class="status-block status-' + type + '"></div>'
                );
            }

            if($.isset(type) && type == 'wait') {
                $('#background-loader').addClass('wait');
            }
            else $.preload_change_status(type);

            if(!$('#background-loader').is(':visible')) {
                $.preload_on();
            }
        },
        preload_change_status: function(type) {
            type = type || 'loading';
            
            var status = $('#background-loader .status-block');

            switch(type) {
                case 'loading':
                    $(status).html('<span class="loading"></span>');
                    break;
                case 'success':
                    $(status).html('<span class="success"></span>');
                    break;
                case 'error':
                    $(status).html('<span class="error"></span>');
                    break;
            }
        },
        preload_off: function(callback) {
            callback = callback || null;

            $('#background-loader').fadeOut(1000, function() {
                if(callback && typeof(callback) === "function") {
                    callback();
                }
                $('#background-loader').remove();
            });
        },
        preload_on: function(callback) {
            callback = callback || null;

            $('#background-loader').fadeIn(1000, function() {
                if(callback && typeof(callback) === "function") {
                    callback();
                }
            });
        },
        form_res: function(data) {
            var res = {
                body: '',
                status: 'loading',
                title: '',
                data: ''
            };

            if($.check_json(data)) {
                data = $.parseJSON(data);
            }

            if(data) {
                if(typeof(data) == 'object' || typeof(data) == 'array') {
                    res = $.set_merge(res, data);
                }
                else if(typeof(data) == 'string') {
                    res.data = data;
                }
            }
            else {
                res.status = 'error';
            }

            if(typeof(data) == 'object' && data.length > 4 ) {
                res.other = {};
                $.each(data, function(key, value) {
                    if(!$.inArray(key, ['body', 'status', 'title', 'data'])) {
                        if(data.length == 5) {
                            res.other = value;
                        }
                        else {
                            res.other['key'] = value;
                        }
                    }
                });
            }
            
            return res;
        },
        make_action: function(name, params, category, callback) {
            params   = params || null;
            category = category || 'public';
            callback = callback || null;

            $.ajax({
                url: '../connect.php',
                type: 'POST',
                async: true,
                data: {
                    action: name,
                    params: JSON.stringify(params),
                    category: category
                },
                beforeSend: function() {
                    $.preload();
                },
                error: function() {
                    $.preload_change_status('error');
                    $.preload_off();
                },
                success: function(res) {
                    res = $.form_res(res);

                    console.log(res);

                    if($.isset(res.status)) {
                        if(res.status == 'success') {
                            $.preload_change_status('success');
                        }
                        else if(res.status == 'error' || res.status == 'danger'){
                            $.preload_change_status('error');
                        }
                        else if(res.status == 'reload') {
                            $.preload_change_status('success');
                            $.preload_off(function() {
                                if(res.status && res.body) {
                                    $.cookie('reload_message', JSON.stringify([res.status, res.body, res.title]), { expires: 1, path: '/'});
                                }
                                location.reload();
                            });
                        }
                    }
                    $.preload_off(function() {
                        if(res.status && res.status != 'loading' && res.body) {
                            $('.message-generate').remove();
                            $.detmessage({
                                'text':  res.body,
                                'title': res.title,
                                'suff':  res.status
                            });
                        }
                        if(callback && typeof(callback) == 'function') {
                            callback(res);
                        }
                    });
                }
            });
        },
        copy: function(obj) {
            return (obj) ? JSON.parse(JSON.stringify(obj)) : null;
        },
        get_property: function() {
            var current = arguments[0];
            for(var i = 1; i < arguments.length; i++) {
                if($.isset(current[arguments[i]])) {
                    current = current[arguments[i]];
                }
                else {
                    return undefined;
                }
            }
            return current;
        }
    });
    var FUNCTIONS = {};

    //Methods
    //Получаю data-*
    $.fn.get_data = function(name, default_value) {
        default_value = default_value || null;

        var type = 'data-' + name;

        if($(this).is('[' + type + ']')) {
            return $(this).attr(type);
        }
        else {
            return default_value;
        }
    };
    //Очищаю values от элементов формы
    $.fn.clear_input_value = function(input_class, clone) {
        if(!$.isset(clone)) {
            clone = true;
        }
        var block = this,
            input_types = 'input, textarea, select';
        input_class = input_class || null;

        if(clone && $(block).find('.clone').length) {
            $(block).find('.clone').remove();
        }
        if(input_class) {
            block = $(block).find(input_class);
        }

        $(block).each(function() {
            var item = ($(this).is(input_types)) ? $(this) : $(this).find(input_types);
            if($.isset(item) && $(item).is(':enabled')) {
                if($(item).is('[type="checkbox"], [type="radio"]')) {
                    $(item).prop('checked', false);
                }
                else if($(item).is('select')) {
                    $(item).prop('selected', false);
                }
                else {
                    $(item).val('');
                }
            }
        });
    };
    //Получаю сформированный результат по форме
    $.fn.get_universal_values = function(input_class) {
        var res = {},
            input_types = 'input, textarea, select';
        input_class = input_class || input_types;

        $(this).find(input_class).not('.skip').each(function() {
            var find = null;
            if($(this).is(input_types)) {
                find = this;
            }
            else {
                find = $(this).find(input_types).not('.skip');
            }

            $(find).each(function() {
                var name = '';
                var value = $(this).get_input_value();

                if($(this).is('[name]')) {
                    name = $(this).attr('name').replace('[]', '');
                }
                else if($(this).is('.must')){
                    name = 'unnamed';
                }

                if(name) if($.isset(res[name])) {
                    if(!$.isArray(res[name])) {
                        res[name] = [res[name]];
                    }
                    res[name].push(value);
                }
                else {
                    res[name] = value;
                }
            });
        });

        return res;
    };
    //Получить значение инпута
    $.fn.get_input_value = function() {
        var value = '',
            req   = null,
            input = this,
            input_types = 'input, textarea, select';

        if($(input).is(input_types)) {
            var getter = null;
            if($(input).is('[data-custom-get]')) {
                getter = $(input).attr('data-custom-get');
                if($.isset_function(getter)) {
                    value = $.call_function(getter, input);
                }
                else {
                    getter = null;
                }
            }
            if(!getter) {
                if($(input).is('[type="radio"], [type="checkbox"]')) {
                    var res = {
                        'on': false,
                        'value': $(input).val()
                    }
                    if($(input).is(':checked')) {
                        res.on = true;
                    }
                    value = res;
                }
                else value = $(input).val();
            }
        }
        else {
            if(req = $(input).find(input_types).length && req > 1) {
                value = [];
            }

            $(input).find(input_types).not('.skip').each(function() {
                if(req > 1) {
                    value.push(get_input_value(this));
                }
                else value = get_input_value(this);
            });
        }

        return value;
    };
    //Получаю ID/code
    $.fn.get_ID = function(parenter, code) {
        parenter = parenter || null;
        if(!$.isset(code)) {
            code = true;
        }
        var res = null,
            block = null;
        if(parenter) {
            block = $(this).parents(parenter).find('[data-id], [data-code]').first();
        }
        else {
            block = $(this);
        }
        if($(block).is('[data-id], [data-code]')) if(res = $(block).get_data('id')) {
            if(code) res = {
                'type':  'ID',
                'value': res
            }
        }
        else if(code && (res = $(block).get_data('code'))) {
            res = {
                'type':  'code',
                'value': res
            }
        }
        return res;
    };
    //Делаю клон блока
    $.fn.make_field_clone = function(parent, subclone) {
        parent   = parent   || '.field',
        subclone = subclone ||'.subfield';
        var input_types = 'input, textarea, select';
        var link = null;
        if($(this).is(input_types)) {
            link = true;
        }

        if($(this).parents(parent).length && $(this).parents(parent).find(subclone).length) {
            parent = $(this).parents(parent);

            var clone = $(parent).find(subclone).last().clone();
            $(clone).addClass('clone').clear_input_value();
            if(!$(clone).find('.clone-remove').length) {
                $(clone).find('label').append(' <a class="clone-remove">[x]</a>');
            }
            $(parent).find(subclone).last().after(clone);

            if(link) {
                return  $(parent).find(subclone).last().find(input_types).last();
            }
            else {
                return  $(parent).find(subclone).last();
            }
        }

        return null;
    };
    //Заполняю инпуты
    $.fn.set_input_value = function(obj, input_class) {
        var input_types = 'input, textarea, select';
        input_class = input_class || input_types;

        $(this).find(input_class).each(function() {
            var find = null;
            if($(this).is(input_types)) {
                find = this;
            }
            else {
                find = $(this).find(input_types);
            }

            $(find).each(function() {
                var name = '';

                if($(this).is('[name]')) {
                    name = $(this).attr('name').replace('[]', '');
                }
                else {
                    name = 'unnamed';
                }

                if($.isset(obj[name]) && obj[name]) {
                    if((typeof(obj[name]) == 'array' || typeof(obj[name]) == 'object') && obj[name].length <= 5) {
                        var sib = $(this),
                            i   = 0,
                            L   = obj[name].length - 1;
                        $.each(obj[name], function(key, value) {
                            if(typeof(value) == 'array' || typeof(value) == 'object') {
                                value = JSON.stringify(value);
                            }
                            $(sib).val(value);
                            if(i < L) {
                                sib = $(sib).make_field_clone();
                            }
                            i++;
                        });
                    }
                    else {
                        if(typeof(obj[name]) == 'array' || typeof(obj[name]) == 'object') {
                            obj[name] = JSON.stringify(obj[name]);
                        }
                        $(this).val(obj[name]);
                    }
                }
            });
        });
    }
})(jQuery);