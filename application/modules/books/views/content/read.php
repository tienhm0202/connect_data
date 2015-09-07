<?php $id = isset($book_id) ? $book_id : ''; ?>
<h3><?php echo lang('books_compose') ?></h3>
<div class="row-fluid">
    <div class="span3">
        <h4>Tiêu đề</h4>
    </div>
<!--    <div class="span9" style="text-align: right">-->
<!--        <div class="btn btn-success" onclick="vote_up()"><i class="icon icon-white icon-thumbs-up"></i> Thích</div>-->
<!--    </div>-->
</div>
<div class="row-fluid">
    <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal" enctype= multipart/form-data'); ?>
    <?php if (isset($book_content) && is_array($book_content) && count($book_content)): ?>
        <div class="span3">
            <ul class="sortable">
                <?php foreach ($book_content as $key => $content): ?>
                    <?php if ($content["id"] == $selected_content): ?>
                        <li id="<?php echo $content["id"] ?>">
                            <input name="header"
                                   type="text" id="<?php echo $content["id"] ?>"
                                   value="<?php echo $content["header"] ?>"
                                   class="header active"/>
                        </li>
                    <?php else: ?>
                        <li id="<?php echo $content["id"] ?>">
                            <a class="no-underline"
                               href="<?php echo site_url(SITE_AREA . '/content/books/read/' . $id . '/' . $content["id"]) ?>">
                                <div class="header">
                                    <?php echo $content["header"] ?>
                                </div>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php $this->load->view('books/_read_part', array("content" => $file_content)); ?>
    <?php else: ?>
        <?php redirect(site_url(SITE_AREA . '/content/books/choose/' . $id)) ?>
    <?php endif; ?>
    <?php echo form_close(); ?>
    <div class="result"></div>
</div>
<!---->
<!--<script>-->
    <!--    function vote_up(){-->
<!--        $.get(--><?php //echo json_encode(site_url(SITE_AREA . '/content/books/vote_up/' . $id .'/1')) ?>//)
//    }
//</script>