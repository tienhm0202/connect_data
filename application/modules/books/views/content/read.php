<h3><?php echo lang('books_read') ?></h3>
<div class="well">
    <h4><?php echo $books->title ?></h4>
    <p><?php echo $author; ?></p>
    <p><?php echo $books->created_on ?></p>
    <p><?php echo anchor(SITE_AREA . '/content/collections/add_collection?book_id=' . $id. '&url='.urlencode(site_url(SITE_AREA. '/content/books/read/'. $id)), '<span class="icon-plus"></span>' . 'Thêm vào Tủ sách'); ?></p>
</div>
<div class="admin-box">
    <div style="padding-left: 5px; padding-right: 5px">
        <?php echo($doc); ?>
    </div>
    <div class="form-actions">
        <?php echo anchor(SITE_AREA . '/content/books/download/'.$id, '<i class="icon-download-alt icon-white"></i>'.lang('books_download'), 'class="btn btn-info"'); ?>
        <?php echo anchor($url_back, lang('books_back'), 'class="btn btn-warning"'); ?>
    </div>
</div>