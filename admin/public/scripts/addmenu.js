$(function () {

    $('#add-menu').on('click', function (e) {
        $('#menu').append('<li>\n' +
            '                    <div class="handle"></div><div class="menu-item">\n' +
            '                        <a href="#" class="delete-menu">\n' +
            '                            <i class="fa fa-times"></i>\n' +
            '                        </a>\n' +
            '                        <input type="text" name="title[]" placeholder="Menü Adı">\n' +
            '                        <input type="text" name="url[]" placeholder="Menü Linki">\n' +
            '                    </div>' +
            '<div class="sub-menu"><ul></ul></div>\n' +
            '                    <a href="#" class="add-submenu btn">Alt Menü Ekle</a>\n' +
            '                </li>');
        e.preventDefault();
    });

    $(document.body).on('click', '.add-submenu', function (e) {
        var index = $(this).closest('li').index();
        $(this).prev('.sub-menu').find('ul').append('<li>\n' +
            '                                <div class="handle"></div><div class="menu-item">\n' +
            '                                    <a href="#" class="delete-menu">\n' +
            '                                        <i class="fa fa-times"></i>\n' +
            '                                    </a>\n' +
            '                                    <input type="text" name="sub_title_' + index + '[]" placeholder="Menü Adı">\n' +
            '                                    <input type="text" name="sub_url_' + index + '[]" placeholder="Menü Linki">\n' +
            '                                </div>\n' +
            '                            </li>');
        e.preventDefault();
    });

    $(document.body).on('click', '.delete-menu', function (e) {
        if ($('#menu li').length === 1) {
            alert('En az 1 menü içeriği kalmak zorundadır!');
        } else {
            $(this).closest('li').remove();
        }
        e.preventDefault();
    });

});