<?php $id = isset($book_id) ? $book_id : ''; ?>
<h3><?php echo lang('books_compose') ?></h3>
<div class="row-fluid">
    <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal" enctype= multipart/form-data'); ?>
    <?php if (isset($book_content) && is_array($book_content) && count($book_content)): ?>
        <div class="span3">
            <h4>Tiêu đề</h4>
            <?php foreach ($book_content as $key => $content): ?>
                <?php if ($content["id"] == $selected_content): ?>
                    <input name="<?php echo $content["id"] ?>[header]" type="text" id="<?php echo $content["id"] ?>"
                           value="<?php echo $content["header"] ?>"
                           class="header active"/>
                <?php else: ?>
                    <a class="no-underline" href="<?php echo site_url(SITE_AREA . '/content/books/compose/' . $id . '/' . $content["id"]) ?>">
                        <div class="header"><?php echo $content["header"] ?></div>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
            <div class="header">Add more</div>
        </div>
        <?php $this->load->view('books/_compose_part', array("content" => $file_content)); ?>
    <?php else: ?>

    <?php endif; ?>
    <?php echo form_close(); ?>
</div>