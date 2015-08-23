<fieldset>
    <div class="control-group <?php echo form_error('content') ? 'error' : ''; ?>">
        <?php echo form_label("Tiêu đề" . lang('bf_form_label_required'), 'content', array('class' => 'control-label')); ?>
        <div class='controls'>
            <input id="header" required class="span9" type='text' name='header' maxlength="50" />
            <span class='help-inline'><?php echo form_error('header'); ?></span>
        </div>
    </div>

    <div class="control-group <?php echo form_error('content') ? 'error' : ''; ?>">
        <?php echo form_label("Kiểu" . lang('bf_form_label_required'), 'content', array('class' => 'control-label')); ?>
        <div class='controls'>
            <?php echo form_radio("doc_type", "file", true, 'onclick="meo_meo(2)"') ?> File <br>
            <?php echo form_radio("doc_type", "doc", false, 'onclick="meo_meo(1)"') ?> Văn bản
        </div>
    </div>
    <div class="control-group meo-meo">
        <?php echo form_label("Chọn file" . lang('bf_form_label_required'), 'content', array('class' => 'control-label')); ?>
        <div class='controls'>
            <input id="meo-con" type="file" name="userfile" />
        </div>
    </div>
</fieldset>
<script>
    function meo_meo(s){
        if(s==1) {
            $(".meo-meo").hide()
            $("#meo-con").removeAttr("required")
        } else {
            $(".meo-meo").show()
            $("#meo-con").attr("required", "required")
        }

    }
</script>