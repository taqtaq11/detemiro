$(document).ready(function() {
    $('.nav-bootstrap li ul').addClass('dropdown-menu').parent('li').addClass('dropdown');

    $('.nav-bootstrap > li > ul').parent('li').children('a').addClass('dropdown-toggle').attr('data-toggle', 'dropdown').append(' <span class="caret"></span>');

    $('.nav-bootstrap > li > ul ul').parent('li').addClass('dropdown-submenu');
});