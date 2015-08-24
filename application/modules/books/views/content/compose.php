<?php $id = isset($book_id) ? $book_id : ''; ?>
<h3><?php echo lang('books_compose') ?></h3>
<div class="row-fluid">
    <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal" enctype= multipart/form-data'); ?>
    <?php if (isset($book_content) && is_array($book_content) && count($book_content)): ?>
        <div class="span3">
            <h4>Tiêu đề</h4>
            <ul class="sortable">
                <?php foreach ($book_content as $key => $content): ?>
                    <?php if ($content["id"] == $selected_content): ?>
                        <li id="<?php echo $content["id"] ?>">
                            <input name="header"
                                   type="text" id="<?php echo $content["id"] ?>"
                                   value="<?php echo $content["header"] ?>"
                                   class="header active"/>
                            <a href="<?php echo site_url(SITE_AREA . '/content/books/remove_content/' . $id . '/' . $content["id"]) ?>"
                               onclick="return confirm('Bạn chắc chắn muốn xóa nội dung này?');">
                                <i class="icon icon-remove"></i>
                            </a>
                        </li>
                    <?php else: ?>
                        <li id="<?php echo $content["id"] ?>">
                            <a class="no-underline"
                               href="<?php echo site_url(SITE_AREA . '/content/books/compose/' . $id . '/' . $content["id"]) ?>">
                                <div class="header">
                                    <?php echo $content["header"] ?>
                                </div>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <a class="no-underline" href="<?php echo site_url(SITE_AREA . '/content/books/choose/' . $id) ?>">
                <div class="btn btn-info" style="margin-top: 15px"><i class="icon icon-white icon-plus"></i>Thêm nội
                    dung
                </div>
            </a>
        </div>
        <?php $this->load->view('books/_compose_part', array("content" => $file_content)); ?>
    <?php else: ?>
        <?php redirect(site_url(SITE_AREA . '/content/books/choose/' . $id)) ?>
    <?php endif; ?>
    <?php echo form_close(); ?>
    <div class="result"></div>
</div>