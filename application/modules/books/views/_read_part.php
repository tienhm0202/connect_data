<?php
function is_image($type)
{
    return (in_array($type, array("png", "jpg", "jpeg", "gif")));
}

function is_microsoft($type){
    return (in_array($type, array("doc", "docx", "ppt", "pptx", "xls", "xlsx")));
}

function is_video($type){
    return (in_array($type, array("aif","aiff","aac","au","bmp","gsm","mov","mid","midi","mpg","mpeg","mp4","m4a","psd","qt","qtif","qif","qti","snd","tif","tiff","wav","3g2","3gp")));
}

function is_audio($type){
    return (in_array($type, array("mp3", "wav", "ogg")));
}
?>

<div class="span9">
    <h4>Nội dung</h4>
    <fieldset>
        <div>
            <?php if ($content["file_type"] == "html"): ?>
                <?php echo htmlspecialchars_decode($content["content"]) ?>
            <?php elseif (is_image($content["file_type"])): ?>
                <img src='<?php echo base_url() . $content["filename"] ?>'>
            <?php elseif (is_microsoft($content["file_type"])): ?>
                <iframe src="http://docs.google.com/gview?url=<?php echo base_url(). $content["filename"] ?>&embedded=true" style="width:100%; height:600px;" frameborder="0"></iframe>
            <?php elseif (is_video($content["file_type"])): ?>
                <video width="480" height="320" controls="controls">
                    <source src="<?php echo base_url(). $content["filename"] ?>" type="video/<?php echo $content["file_type"] ?>">
                </video>
            <?php elseif (is_audio($content["file_type"])): ?>
                <audio controls>
                    <source src="<?php echo base_url(). $content["filename"] ?>" type="audio/<?php echo $content["file_type"] ?>">
                </audio>
            <?php else: ?>
                <a class="media" href="<?php echo base_url() . $content["filename"] ?>"></a>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <?php echo anchor(SITE_AREA . '/content/books', lang('books_cancel'), 'class="btn btn-warning"'); ?>
        </div>
    </fieldset>
</div>
