<?php

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

if (isset($collections))
{
	$collections = (array) $collections;
}
$id = isset($collections['collection_id']) ? $collections['collection_id'] : '';

?>
<div class="admin-box">
	<h3><?php echo lang('collections_edit') ?></h3>
	<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>
		<fieldset>

			<div class="control-group <?php echo form_error('collection_name') ? 'error' : ''; ?>">
				<?php echo form_label(lang('collection_name'), 'collections_collection_name', array('class' => 'control-label') ); ?>
				<div class='controls'>
					<input id='collections_collection_name' type='text' name='collections_collection_name' maxlength="50" value="<?php echo set_value('collections_collection_name', isset($collections['collection_name']) ? $collections['collection_name'] : ''); ?>" />
					<span class='help-inline'><?php echo form_error('collection_name'); ?></span>
				</div>
			</div>

			<div class="control-group <?php echo form_error('publish') ? 'error' : ''; ?>">
				<?php echo form_label(lang('published'), 'collections_publish', array('class' => 'control-label') ); ?>
				<div class='controls'>
					<label class='checkbox' for='collections_publish'>
						<input type='checkbox' id='collections_publish' name='collections_publish' value='1' <?php echo (isset($collections['publish']) && $collections['publish'] == 1) ? 'checked="checked"' : set_checkbox('collections_publish', 1); ?>>
						<span class='help-inline'><?php echo form_error('publish'); ?></span>
					</label>
				</div>
			</div>

            <?php if(isset($collections['publish']) && $collections['publish'] == 1): ?>
                <div class="control-group">
                    <?php $url = site_url(SITE_AREA.'/content/collections/share/'.$collections['collection_id']) ?>
                    <label class="control-label">Link chia sẻ:</label>
                    <div class='controls'>
                        <a href="<?php echo $url ?>"><?php echo $url ?></a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(isset($book_list) && is_array($book_list) && count($book_list)): ?>
            <div class="well">
            <h4>Tài liệu trong tủ sách</h4>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th></th>
                    <th><?php echo lang('Title'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($book_list as $book): ?>
                    <?php echo genFormCollection('collection-'.$book->book_id, $book->title, "removeCollectionItem('collection-{$book->book_id}')", 'book_id[]', $book->book_id); ?>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <?php endif; ?>

			<div class="form-actions">
				<input type="submit" name="save" class="btn btn-primary" value="<?php echo lang('collections_action_edit'); ?>"  />
				<?php echo anchor(SITE_AREA .'/content/collections', lang('collections_cancel'), 'class="btn btn-warning"'); ?>
				
			<?php if ($this->auth->has_permission('Collections.Content.Delete')) : ?>
				<button type="submit" name="delete" class="btn btn-danger" id="delete-me" onclick="return confirm('<?php e(js_escape(lang('collections_delete_confirm'))); ?>'); ">
					<span class="icon-trash icon-white"></span>&nbsp;<?php echo lang('collections_delete_record'); ?>
				</button>
			<?php endif; ?>
			</div>
		</fieldset>
    <?php echo form_close(); ?>
</div>
<script>
    function removeCollectionItem(id){
        $('#'+id).remove();
    }
</script>