<div class="admin-box">
    <h3><?php echo "Chọn kiểu tài liệu" ?></h3>
    <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal" enctype= multipart/form-data'); ?>
    <fieldset>
        <div class="control-group <?php echo form_error('content') ? 'error' : ''; ?>">
            <?php echo form_label("Kiểu" . lang('bf_form_label_required'), 'content', array('class' => 'control-label')); ?>
            <div class='controls'>
                <?php echo form_radio("dog", "owned", $st?false:true, 'onclick="gau_gau(2)"') ?> Sử dụng tài liệu cá nhân <br>
                <?php echo form_radio("dog", "social", $st?true:false, 'onclick="gau_gau(1)"') ?> Sử dụng tài liệu cộng đồng
            </div>
        </div>
    </fieldset>
    <div id="owned" style="border: solid 1px gainsboro;">
        <?php $this->load->view('books/_choose_owned'); ?>
    </div>
    <div id="social" style="border: solid 1px gainsboro;  display: none">
        <?php echo $search; ?>
    </div>

    <div class="form-actions">
        <input type="submit" name="save" class="btn btn-primary" value="<?php echo lang('books_compose'); ?>"/>
        <?php echo lang('bf_or'); ?>
        <?php echo anchor(SITE_AREA . '/content/books', lang('books_cancel'), 'class="btn btn-warning"'); ?>
    </div>

    <?php echo form_close(); ?>
</div>
<script>
    function gau_gau(s) {
        if (s == 2) {
            $("#owned").show();
            $("#header").attr("required", "required");
            meo_meo(2);
            $("#social").hide();
        } else {
            $("#owned").hide();
            $("#header").removeAttr("required");
            $("#meo-con").removeAttr("required");
            $("#social").show();
        }
    }
</script>