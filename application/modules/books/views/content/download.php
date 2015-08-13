<div class="admin-box">
    <h3><?php echo lang('books_download') ?></h3>

    <div class="form-horizontal">
        <fieldset>
            <dl class="dl-horizontal">
            <?php if (isset($books) && is_array($books) && count($books)): ?>
                <?php foreach ($books as $book_module => $book_info): ?>
                    <div class="control-group">
                        <dt><?php echo lang($book_module); ?></dt>
                        <dd><?php echo $book_info ?></dd>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </dl>
            <div class="form-actions">
                <?php if (isset($writters) && isset($id)): ?>
                    <?php foreach ($writters as $name => $extension): ?>
                        <a href='<?php echo base_url('assets/books/' . $id . '.' . $extension); ?>'
                           class='btn btn-primary'><?php echo $name ?></a>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php echo anchor(SITE_AREA . '/content/books', lang('books_back'), 'class="btn btn-warning"'); ?>
            </div>
        </fieldset>
    </div>
</div>