<?php
function is_image($type) {
    return (in_array($type, array("png", "jpg", "jpeg")));
}

$validation_errors = validation_errors();

if ($validation_errors) :
    ?>
    <div class="alert alert-block alert-error fade in">
        <a class="close" data-dismiss="alert">&times;</a>
        <h4 class="alert-heading">Please fix the following errors:</h4>
        <?php echo $validation_errors; ?>
    </div>
<?php
endif;

if (isset($books)) {
    $books = (array)$books;
}
$id = isset($books['book_id']) ? $books['book_id'] : '';
if ($book_type == "file"){
    $display = "none";
    if (is_object ($book_content)){

        if (is_image($book_content->file_type)) {
            $image_link = base_url() . $book_content->filename;
        }
        $book_content = null;
    }

} else {
    $display = "block";
}
?>
<h3><?php echo lang('books_compose') ?></h3>
<div class="row-fluid">
    <div class="span2" style="background: gainsboro">
        <fieldset>
            <div style="padding-top: 5px; padding-left: 50px">
                heading 1
            </div>
        </fieldset>
    </div>
    <div class="span10">
        <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal" enctype= multipart/form-data'); ?>
        <fieldset>
            <div style="display: <?php echo $display=="block"?"none":$display ?>">
                <input type="file" name="userfile">
                <?php
                    if ($display == "none" && isset($image_link))
                        echo "<img src='{$image_link}'>";
                ?>
                <input type="submit" name="upload" class="btn btn-primary" value="<?php echo lang('books_upload'); ?>"/>
            </div>
            <div style="display: <?php echo $display ?>">
                <?php echo form_textarea(array('name' => 'content', 'id' => 'content', 'rows' => '20', 'style' => 'width: 90%;', 'value' => set_value('content', isset($book_content) ? htmlspecialchars_decode($book_content) : ''), 'class' => 'tinymce')) ?>
                <span class='help-inline'><?php echo form_error('content'); ?></span>
            </div>

            <div class="form-actions" style="display: <?php echo $display ?>">
                <input type="submit" name="save" class="btn btn-primary" value="<?php echo lang('books_compose'); ?>"/>
                <?php echo lang('bf_or'); ?>
                <?php echo anchor(SITE_AREA . '/content/books', lang('books_cancel'), 'class="btn btn-warning"'); ?>
            </div>
        </fieldset>
        <?php echo form_close(); ?>
    </div>
</div>