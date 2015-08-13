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
	<h3><?php echo lang('collections_create') ?></h3>
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

			<div class="form-actions">
				<input type="submit" name="save" class="btn btn-primary" value="<?php echo lang('collections_action_create'); ?>"  />
				<?php echo anchor(SITE_AREA .'/content/collections', lang('collections_cancel'), 'class="btn btn-warning"'); ?>
				
			</div>
		</fieldset>
    <?php echo form_close(); ?>
</div>