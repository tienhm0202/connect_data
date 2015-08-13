$(document).ready(function(){
    $('#checkall').change(function(){
        var allow_change = $(this).attr('checked');
        if (allow_change == 'checked')
        {
            $('.chk_telco_shortcode').attr('checked', 'checked');
            $('#all_proportion').attr('required', 'required');
            $('#shortcode_detail').hide();
            if($('#all_proportion').val().length == 0)
                $('.input_proportion').attr('required', 'required');
            $('#see_detail').show();
            $('#collapse_detail').hide();
        }
        else
        {
            $('.chk_telco_shortcode').removeAttr('checked');
            $('#all_proportion').removeAttr('required');
            $('.input_proportion').removeAttr('required');  
            $('#shortcode_detail').show();
            $('#see_detail').hide();
            $('#collapse_detail').show();
        }
    });
    $(".chk_telco_shortcode").click(function() {
        var value = $(this).val();
        var status = $('#proportion_'+value).attr('required');
        if(status == 'required'){
            $('#proportion_'+value).removeAttr('required');
        } else {
            $('#proportion_'+value).attr('required', 'required');
        }
    });
    $('#all_proportion').change(function(){
        if($('#all_proportion').val().length == 0 && $('#checkall').attr('checked') == 'checked'){
            $('.input_proportion').attr('required', 'required');              
        } else {
            $('.input_proportion').removeAttr('required'); 
        }
    });
    $('#see_detail').click(function(){
        $('#shortcode_detail').show();
        $('#see_detail').hide();
        $('#collapse_detail').show();
        $('#all_proportion').removeAttr('required');
    });
    $('#collapse_detail').click(function(){
        $('#shortcode_detail').hide();
        $('#see_detail').show();
        $('#collapse_detail').hide();
        $('#all_proportion').attr('required', 'required');
        $('#checkall').attr('checked', 'checked');
    });
});
