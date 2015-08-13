<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>
<div class="admin-box">
    <ul class="nav nav-tabs" >
        <li <?php echo ($this->uri->segment(4) == 'list_setting_group') ? 'class="active"' : ''; ?>><a href="<?php echo site_url(SITE_AREA . '/settings/site_settings/list_setting_group'); ?>"><?php echo lang('delete_setting_group'); ?></a></li>
        <li <?php echo ($this->uri->segment(4) == 'create_setting_group') ? 'class="active"' : ''; ?>><a href="<?php echo site_url(SITE_AREA . '/settings/site_settings/create_setting_group'); ?>"><?php echo lang('create_setting_group'); ?></a></li>
    </ul>
    <table class="table table-striped">
        <thead>
            <tr>
                <?php if ($this->auth->has_permission('Site_settings.Settings.Delete') && isset($list_settings) && is_array($list_settings) && count($list_settings)) : ?>
                    <th class="column-check"><input class="check-all" type="checkbox" /></th>
                <?php endif; ?>
                <th><?php echo lang('site_setting_name'); ?></th>
            </tr>            
        </thead>
        <?php if (isset($list_settings) && is_array($list_settings) && count($list_settings)): ?>
            <tbody>
                <?php foreach ($list_settings as $setting): ?>
                    <?php $value_setting = str_replace('.', '_DOT_', $setting); ?>
                    <tr>
                        <?php if ($this->auth->has_permission('Site_settings.Settings.Delete')) : ?>
                            <td><center><input type="checkbox" name="checked[]" value="<?php echo $value_setting; ?>"/></center></td>
                <?php endif; ?>
                <td><?php echo $setting; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>               
            <tfoot>
                <tr>
                    <?php if (has_permission('Site_settings.Settings.Delete')): ?>
                        <td colspan="2">
                            <?php echo lang('with_selected'); ?><input id="submit_btn" type="submit" name="delete" class="btn btn-danger" value="<?php echo lang('site_settings_delete'); ?>" onclick="return confirm('<?php echo lang('setting_delete_confirm'); ?>')" />
                        </td>
                    <?php endif; ?>
                </tr>
            </tfoot>
        <?php else: ?>
            <tfoot>
                <tr>
                    <td colspan="10"><?php if ($this->uri->segment(5, 'null') != 'null') echo lang('site_settings_no_reconds_found');else echo lang('site_settings_no_records'); ?></td>
                </tr>
            </tfoot>
        <?php endif; ?>
    </table>
</div>
<?php echo form_close(); ?>