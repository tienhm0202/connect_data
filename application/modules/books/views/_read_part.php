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
                <?php echo htmlspecialchars_decode($content["content"]) ?>
            <?php elseif ($content["file_type"] == "pdf"): ?>
                <a class="media" href="<?php echo base_url() . $content["filename"] ?>"></a>
            <?php endif; ?>
        </div>

        <div class="form-actions" style="display: <?php echo $display ?>">
            <?php echo anchor(SITE_AREA . '/content/books', lang('books_cancel'), 'class="btn btn-warning"'); ?>
        </div>
    </fieldset>
</div>

