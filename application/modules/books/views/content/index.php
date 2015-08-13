<?php

$num_columns = 9;
$can_delete = $this->auth->has_permission('Books.Content.Delete');
$can_edit = $this->auth->has_permission('Books.Content.Edit');
$has_records = isset($records) && is_array($records) && count($records);

?>
<div class="admin-box">
    <h3><?php echo lang('books_manage') ?></h3>
    <?php echo form_open($this->uri->uri_string()); ?>
    <table class="table table-striped">
        <thead>
        <tr>
            <?php if ($can_delete && $has_records) : ?>
                <th class="column-check"><input class="check-all" type="checkbox"/></th>
            <?php endif; ?>

            <th><?php echo lang('Title'); ?></th>
            <th><?php echo lang('Category Id'); ?></th>
            <th><?php echo lang('Published'); ?></th>
            <th><?php echo lang('Tag'); ?></th>
            <th><?php echo lang('books_column_created'); ?></th>
            <th colspan="3"></th>
        </tr>
        </thead>
        <?php if ($has_records) : ?>
            <tfoot>
            <?php if ($this->pagination->create_links() != '') : ?>
                <tr><td colspan="12"><?php echo $this->pagination->create_links(); ?></td></tr>
            <?php endif; ?>
            <?php if ($can_delete) : ?>
                <tr>
                    <td colspan="<?php echo $num_columns; ?>">
                        <?php echo lang('bf_with_selected'); ?>
                        <input type="submit" name="delete" id="delete-me" class="btn btn-danger"
                               value="<?php echo lang('bf_action_delete'); ?>"
                               onclick="return confirm('<?php e(js_escape(lang('books_delete_confirm'))); ?>')"/>
                    </td>
                </tr>
            <?php endif; ?>
            </tfoot>
        <?php endif; ?>
        <tbody>
        <?php
        if ($has_records) :
            foreach ($records as $record) :
                ?>
                <tr>
                    <?php if ($can_delete) : ?>
                        <td class="column-check"><input type="checkbox" name="checked[]"
                                                        value="<?php echo $record->book_id; ?>"/></td>
                    <?php endif; ?>

                    <td><?php echo anchor(SITE_AREA . '/content/books/read/' . $record->book_id, $record->title); ?></td>
                    <td><?php e($record->category_name) ?></td>
                    <td><?php e($record->published) ?></td>
                    <td><?php e($record->tag) ?></td>
                    <td><?php e($record->created_on) ?></td>
                    <td><?php echo anchor(SITE_AREA . '/content/books/edit/' . $record->book_id, '<span class="icon-edit"></span>' . lang('books_edit')); ?></td>
                    <td><?php echo anchor(SITE_AREA . '/content/books/compose/' . $record->book_id, '<span class="icon-pencil"></span>' . lang('books_compose')); ?></td>
                    <td><?php echo anchor(SITE_AREA . '/content/books/download/' . $record->book_id, '<span class="icon-download-alt"></span>' . lang('books_download')); ?></td>
                </tr>
            <?php
            endforeach;
        else:
            ?>
            <tr>
                <td colspan="<?php echo $num_columns; ?>">No records found that match your selection.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    <?php echo form_close(); ?>
</div>