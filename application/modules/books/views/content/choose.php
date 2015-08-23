<div class="admin-box">
    <h3><?php echo "Chọn kiểu tài liệu" ?></h3>
    <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal" enctype= multipart/form-data'); ?>
    <fieldset>

        <div class="control-group <?php echo form_error('content') ? 'error' : ''; ?>">
            <?php echo form_label("Tiêu đề" . lang('bf_form_label_required'), 'content', array('class' => 'control-label')); ?>
            <div class='controls'>
                <input required class="span9" type='text' name='header' maxlength="50" />
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

        <div class="form-actions">
            <input type="submit" name="save" class="btn btn-primary" value="<?php echo lang('books_compose'); ?>"/>
            <?php echo lang('bf_or'); ?>
            <?php echo anchor(SITE_AREA . '/content/books', lang('books_cancel'), 'class="btn btn-warning"'); ?>

        </div>
    </fieldset>
    <?php echo form_close(); ?>
</div>
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