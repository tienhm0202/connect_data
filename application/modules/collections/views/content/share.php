<div class="admin-box">
	<h3><?php echo lang('collections').' '.$collections->collection_name ?></h3>
	<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>
		<fieldset>
            <?php if(isset($book_list) && is_array($book_list) && count($book_list)): ?>
            <div class="well">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th><?php echo lang('Title'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($book_list as $book): ?>
                    <tr>
                        <td><?php echo $book->book_id ?></td>
                        <td><a href="<?php echo site_url(SITE_AREA.'/content/books/read/'.$book->book_id) ?>"><?php echo $book->title ?></a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php endif; ?>

			<div class="form-actions">
				<?php echo anchor(SITE_AREA .'/content/collections', lang('collections_cancel'), 'class="btn btn-warning"'); ?>
			</div>
		</fieldset>
</div>