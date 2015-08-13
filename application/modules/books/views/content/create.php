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

if (isset($books))
{
	$books = (array) $books;
}
$id = isset($books['book_id']) ? $books['book_id'] : '';

?>
<div class="admin-box">
	<h3><?php echo lang('books_action_create') ?></h3>
	<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>
		<fieldset>

			<?php 
				echo form_dropdown('books_category_id', $category, set_value('books_category_id', isset($books['category_id']) ? $books['category_id'] : ''), lang('Category Id'). lang('bf_form_label_required'));
			?>

			<div class="control-group <?php echo form_error('title') ? 'error' : ''; ?>">
				<?php echo form_label(lang('Title'). lang('bf_form_label_required'), 'books_title', array('class' => 'control-label') ); ?>
				<div class='controls'>
					<input id='books_title' class="span9" type='text' name='books_title' maxlength="50" value="<?php echo set_value('books_title', isset($books['title']) ? $books['title'] : ''); ?>" />
					<span class='help-inline'><?php echo form_error('title'); ?></span>
				</div>
			</div>
                    
                        <div class="control-group <?php echo form_error('description') ? 'error' : ''; ?>">
				<?php echo form_label(lang('description'), 'description', array('class' => 'control-label') ); ?>
				<div class='controls'>
					<textarea id='description' rows="10" class="span9" name='description' maxlength="50" value="<?php echo set_value('description', isset($books['description']) ? $books['description'] : ''); ?>" ></textarea>
					<span class='help-inline'><?php echo form_error('description'); ?></span>
				</div>
			</div>

			<div class="control-group <?php echo form_error('published') ? 'error' : ''; ?>">
				<?php echo form_label(lang('Published'), '', array('class' => 'control-label', 'id' => 'books_published_label') ); ?>
				<div class='controls' aria-labelled-by='books_published_label'>
					<label class='radio' for='books_published_option1'>
						<input id='books_published_option1' name='books_published' type='radio' class='' value='Y' <?php echo set_radio('books_published', 'option1', TRUE); ?> />
						<?php echo lang('yes') ?>
					</label>
					<label class='radio' for='books_published_option2'>
						<input id='books_published_option2' name='books_published' type='radio' class='' value='N' <?php echo set_radio('books_published', 'option2'); ?> />
						<?php echo lang('no') ?>
					</label>
					<span class='help-inline'><?php echo form_error('published'); ?></span>
				</div>
			</div>

			<div class="control-group <?php echo form_error('tag') ? 'error' : ''; ?>">
				<?php echo form_label(lang('Tag'), 'books_tag', array('class' => 'control-label') ); ?>
				<div class='controls'>
					<input id='books_tag' class="span9" type='text' name='books_tag' maxlength="150" value="<?php echo set_value('books_tag', isset($books['tag']) ? $books['tag'] : ''); ?>" />
					<span class='help-inline'><?php echo form_error('tag'); ?></span>
				</div>
			</div>

			<div class="form-actions">
				<input type="submit" name="save" class="btn btn-primary" value="<?php echo lang('books_action_create'); ?>"  />
				<?php echo lang('bf_or'); ?>
				<?php echo anchor(SITE_AREA .'/content/books', lang('books_cancel'), 'class="btn btn-warning"'); ?>
				
			</div>
		</fieldset>
    <?php echo form_close(); ?>
</div>