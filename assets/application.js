$(document).ready(function() {
    $('.datatable').each(function(index, value) {
        var datatable = value;
        if ($(this).find('tr th').length > 10) {
            var newTable = $(this).clone();
            $(this).after('<div style="overflow-x: scroll" id="newdatatable"></div>');
            $('#newdatatable').append(newTable);
            $('#newdatatable').find('caption').html('&nbsp');
            $('#newdatatable').find('tr').find('th:first,td:first').remove().nextAll().attr("nowrap", "nowrap");
            $(this).wrapAll('<div class="old_datatable" style="float: left"></div>');
            $(this).find('tr').find('th:first,td:first').attr("nowrap", "nowrap").nextAll().remove();

            //hover
            $('#newdatatable td').hover(
                    function() {
                        var col = $(this).index() + 1;
                        var row = $(this).closest('tr').index();
                        $('#newdatatable tr td:nth-child(' + col + ')').not($(this)).addClass('datatable_hover_indirect');
                        $('.old_datatable td').eq(row).addClass('datatable_hover_indirect');
                        $(this).parent().find('*').not($(this)).addClass('datatable_hover_indirect');
                        $(this).addClass('datatable_hover');
                    },
                    function() {
                        var row = $(this).closest('tr').index();
                        $(this).removeClass('datatable_hover');
                        $(this).parent().find('*').removeClass('datatable_hover_indirect');
                        $('#newdatatable tr td:nth-child(' + ($(this).index() + 1) + ')').not($(this)).removeClass('datatable_hover_indirect');
                        $('.old_datatable td').eq(row).removeClass('datatable_hover_indirect');
                    });

        }
    });
});