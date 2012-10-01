$(document).ready(function() {
    $('#Upload_pdfa').bind('click', function() {
        var hide = $('#pdfa_select');

        if($('#Upload_pdfa:checked').val() == 1) {
            hide.removeClass('hide');
        } else {
           hide.addClass('hide');
           $('#Upload_pdfa_convert_1').attr('checked', 'checked');
        }
    });
});