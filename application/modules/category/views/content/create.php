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

if (isset($category))
{
	$category = (array) $category;
}
$id = isset($category['category_id']) ? $category['category_id'] : '';

?>
<div class="admin-box">
	<h3><?php echo lang('category_create') ?></h3>
	<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>
		<fieldset>

			<div class="control-group <?php echo form_error('category_name') ? 'error' : ''; ?>">
				<?php echo form_label(lang('category_name'). lang('bf_form_label_required'), 'category_category_name', array('class' => 'control-label') ); ?>
				<div class='controls'>
					<input id='category_category_name' type='text' name='category_category_name' maxlength="150" value="<?php echo set_value('category_category_name', isset($category['category_name']) ? $category['category_name'] : ''); ?>" />
					<span class='help-inline'><?php echo form_error('category_name'); ?></span>
				</div>
			</div>

			<div class="form-actions">
				<input type="submit" name="save" class="btn btn-primary" value="<?php echo lang('category_action_create'); ?>"  />
				<?php echo lang('bf_or'); ?>
				<?php echo anchor(SITE_AREA .'/content/category', lang('category_cancel'), 'class="btn btn-warning"'); ?>
				
			</div>
		</fieldset>
    <?php echo form_close(); ?>
</div>