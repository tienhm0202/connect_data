<?php
function is_image($type)
{
    return (in_array($type, array("png", "jpg", "jpeg")));
}

if (is_image($content["file_type"])) {
    $display = "none";
    $image_link = base_url() . $content["filename"];

} else {
    $display = "block";
}
?>

<div class="span9">
    <h4>Nội dung</h4>
    <fieldset>
        <div style="display: <?php echo $display == "block" ? "none" : $display ?>">
            <input type="file" name="userfile">
            <?php
            if ($display == "none" && isset($image_link))
                echo "<img src='{$image_link}'>";
            ?>
            <input type="submit" name="upload" class="btn btn-primary" value="<?php echo lang('books_upload'); ?>"/>
        </div>
        <div style="display: <?php echo $display ?>">
            <?php if ($content["file_type"] == "html"): ?>
                <?php echo form_textarea(array('name' => 'content', 'id' => 'content', 'rows' => '20', 'style' => 'width: 90%;', 'value' => set_value('content', isset($content["content"]) ? htmlspecialchars_decode($content["content"]) : ''), 'class' => 'tinymce')) ?>
                <span class='help-inline'><?php echo form_error('content'); ?></span>
            <?php elseif ($content["file_type"] == "pdf"): ?>
                <a class="media" href="<?php echo base_url().$content["filename"] ?>"></a>
            <?php endif; ?>
        </div>

        <div class="form-actions" style="display: <?php echo $display ?>">
            <input type="submit" name="save" class="btn btn-primary" value="<?php echo "Lưu lại"; ?>"/>
            <?php echo lang('bf_or'); ?>
            <?php echo anchor(SITE_AREA . '/content/books', lang('books_cancel'), 'class="btn btn-warning"'); ?>
        </div>
    </fieldset>
</div>

