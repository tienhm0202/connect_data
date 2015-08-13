<?php

$validation_errors = validation_errors();

if ($validation_errors) :
    ?>
    <div class="alert alert-block alert-error fade in">
        <a class="close" data-dismiss="alert">&times;</a>
        <h4 class="alert-heading">Please fix the following errors:</h4>
        <?php echo $validation_errors; ?>
    </div>
<?php endif;?>

<div class="admin-box">
    <h3>Thêm vào tủ sách</h3>
    <?php echo form_open($this->uri->uri_string(), 'class="form-horizontal" method="get"'); ?>
    <fieldset>
        <div class="well ">
            <?php if(isset($get) && is_array($get) && count($get)): ?>
                <?php foreach($get as $key => $value): ?>
                    <?php echo form_hidden($key, $value); ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if(isset($collection_list) && is_array($collection_list)&& count($collection_list)): ?>
                <?php foreach($collection_list as $collection): ?>
                    <?php echo form_radio('collection_id', $collection->collection_id).$collection->collection_name ?><br>
                <?php endforeach; ?>
            <?php else: ?>
                <?php echo lang('collections_no_records') ?>
                <a href="<?php echo site_url(SITE_AREA.'/content/collections/create') ?>"<?php echo lang('collections_create');?></a>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <input type="submit" name="save" class="btn btn-primary" value="Thêm vào tủ sách"  />
            <?php echo anchor($url, lang('collections_cancel'), 'class="btn btn-warning"'); ?>

        </div>
    </fieldset>
    <?php echo form_close(); ?>
</div>