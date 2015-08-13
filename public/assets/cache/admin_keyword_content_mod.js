
a = $('#checkall').attr('checked');
if(a != 'checked' ){
    $('#url_all').attr('disabled','true')
}
$('#shortcode_mask').change(function(){
    $('#form_shortcode').submit();
});

$('#checkall').change(function(){
    var allow_change = $(this).attr('checked');

    if (allow_change == 'checked')
    {
        $('.chk_url_shortcode').attr('checked', 'checked');
        $('#url_all').removeAttr('disabled');
    }
    else
    {
        $('.chk_url_shortcode').removeAttr('checked');
        $('#url_all').attr('disabled','true');
    }
});

$('#url_all').keyup(function(){
    var allow_change = $('#checkall').attr('checked');

    if (allow_change == 'checked')
    {
        $('.url_service').val($('#url_all').val()) ;
    }
});
//
//    $("input:radio[name=checked]").click(function() {
//        var value = $(this).val();
//        $("input[name=url_service]").removeAttr('required');
//        $("input[name=url_service]").removeAttr('value');
//        $("input[name=url_service]").attr('disabled', 'disabled');
//        $("#url_shortcode_"+value).attr('required','required');
//        $("#url_shortcode_"+value).removeAttr('disabled');
//    });

