<div class="admin-box">
    <h3><?php echo "Chọn kiểu tài liệu" ?></h3>
    <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal" enctype= multipart/form-data'); ?>
    <fieldset>
        <div class="control-group <?php echo form_error('content') ? 'error' : ''; ?>">
            <?php echo form_label("Kiểu" . lang('bf_form_label_required'), 'content', array('class' => 'control-label')); ?>
            <div class='controls'>
                <?php echo form_radio("doc_type", "doc") ?> Văn bản <br>
                <?php echo form_radio("doc_type", "file") ?> File
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