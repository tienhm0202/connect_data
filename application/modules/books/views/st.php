<?php
$num_columns = 12;
$has_records = (isset($records) && is_array($records) && count($records)) ? true : false;
?>
<div class="form-inline well">
    <select name="category_id" class="chzn" data-placeholder="<?php echo lang('Category Id'); ?>">
        <?php if (isset($category) && is_array($category) && count($category)): ?>
            <option value=""><?php echo lang('Category Id'); ?></option>
            <?php foreach ($category as $category_id => $category_name): ?>
                <option value="<?php echo $category_id ?>" <?php echo isset($books['category_id'])&&$books['category_id']==$category_id?'selected':'' ?>>
                    <?php echo $category_name; ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
    <input type='text' name='title'
           value="<?php echo set_value('title', isset($books['title']) ? $books['title'] : ''); ?>"
           placeholder="<?php echo lang('Title') ?>"/>

    <input type="submit" name="st" class="btn btn-primary" value="<?php echo "Tìm kiếm" ?>">
</div>
<div class="admin-box">
    <table class="table table-striped">
        <thead>
        <tr>
            <th></th>
            <th><?php echo lang('Title'); ?></th>
            <th><?php echo lang('Published'); ?></th>
            <th><?php echo lang('Tag'); ?></th>
            <th><?php echo lang('books_column_created'); ?></th>
        </tr>
        </thead>
        <?php if ($has_records) : ?>
            <tfoot>
            <?php if ($this->pagination->create_links() != '') : ?>
                <tr>
                    <td colspan="<?php echo $num_columns?>"><?php echo $this->pagination->create_links(); ?></td>
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
                    <td><?php echo form_radio("got_book", $record->book_id) ?></td>
                    <td><?php echo $record->title; ?></td>
                    <td><?php e($record->published) ?></td>
                    <td><?php e($record->tag) ?></td>
                    <td><?php e($record->created_on) ?></td>
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
</div>