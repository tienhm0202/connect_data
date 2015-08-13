$( document ).ready(function() {
    $('.chosen-select').chosen({
        no_results_text: "<?php echo lang('chosen_not_found'); ?>",
        allow_single_deselect: true
    });
    $('#data-type').change(function(){
        if($('#data-type').val() == "select" || $('#data-type').val() == "radio"){
            $('#select-input').css("display","block");
            $('.select-input-multiple input').removeAttr('disabled');
            $('#single-input').css("display","none");
            $('#single-input input').attr('disabled','disabled');
        }else{
            $('#select-input').css("display","none");
            $('.select-input-multiple input').attr('disabled','disabled');
            $('#single-input').css("display","block");
            $('#single-input input').removeAttr('disabled');
        }
    });
    $('#add-element').click(function(){
        var count; 
        count = $('.select-input-multiple').length + 1;
        if($('#select-input-1').is(':hidden')){
            $('#select-input-1').show();
        }else{
            str = "<div id=\"select-input-"+count+"\" class=\"select-input-multiple\">"+
                        "<?php echo lang('key'); ?><input type=\"text\" name=\"values["+count+"][key]\" required style=\"width: 100px\">"+
                        "<i class=\"icon-arrow-right\"></i>"+
                        "<?php echo lang('val'); ?><input type=\"text\" name=\"values["+count+"][val]\" required style=\"width: 100px\">"+
                    "</div>";
            $('.select-input-multiple:last').after(str);
        }
    });
    $('#del-element').click(function(){
        var count;
        count = $('.select-input-multiple').length;
        if(count > 1){
            $('#select-input-'+count).remove();
        }
        else{
            $('#select-input-'+count).hide();
        }
    });
});