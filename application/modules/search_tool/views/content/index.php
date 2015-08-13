<?php
$num_columns = 12;
$has_records = (isset($records) && is_array($records) && count($records)) ? true : false;
?>

<?php echo form_open(SITE_AREA . '/content/search_tool/index', 'method="get"'); ?>
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

    <input type="submit" class="btn btn-primary" value="<?php echo lang('search_tool_actions'); ?>">
</div>
<?php echo form_close(); ?>
<div class="admin-box">
    <h3><?php echo lang('search_tool_books') ?></h3>
    <table class="table table-striped">
        <thead>
        <tr>
            <th><?php echo lang('Title'); ?></th>
            <th><?php echo lang('Published'); ?></th>
            <th><?php echo lang('Tag'); ?></th>
            <th><?php echo lang('books_column_created'); ?></th>
            <th colspan="<?php echo $num_columns?>"></th>
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
                    <td><?php echo anchor(SITE_AREA . '/content/books/read/' . $record->book_id, $record->title); ?></td>
                    <td><?php e($record->published) ?></td>
                    <td><?php e($record->tag) ?></td>
                    <td><?php e($record->created_on) ?></td>
                    <td><?php echo anchor(SITE_AREA . '/content/books/download/' . $record->book_id, '<span class="icon-download-alt"></span>' . lang('books_download')); ?></td>
                    <td><?php echo anchor(SITE_AREA . '/content/collections/add_collection?book_id=' . $record->book_id. '&url='.urlencode(site_url(SITE_AREA. '/content/search_tool/index')), '<span class="icon-plus"></span>' . lang('books_add_to_collection')); ?></td>
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