<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>
<div class="admin-box">
    <ul class="nav nav-tabs" >
        <li <?php echo ($this->uri->segment(4) == 'list_setting_group') ? 'class="active"' : ''; ?>><a href="<?php echo site_url(SITE_AREA . '/settings/site_settings/list_setting_group'); ?>"><?php echo lang('delete_setting_group'); ?></a></li>
        <li <?php echo ($this->uri->segment(4) == 'create_setting_group') ? 'class="active"' : ''; ?>><a href="<?php echo site_url(SITE_AREA . '/settings/site_settings/create_setting_group'); ?>"><?php echo lang('create_setting_group'); ?></a></li>
    </ul>

    <fieldset>
        <?php if ($this->auth->has_permission('Site_settings.Settings.Create')): ?>
            <div class="control-group <?php echo form_error('setting_group_name') ? 'error' : ''; ?>">
                <label class="control-label"><?php echo lang('setting_name') . ':' ?></label>
                <div class="controls">
                    <input type="text" id="setting_group_name" name="setting_group_name" required value="<?php echo isset($setting_group['setting_group_name']) ? $setting_group['setting_group_name'] : ''; ?>">
                    <span class="help-inline"><?php echo form_error('setting_group_name') ? lang('setting_groups_name_exist') : ''; ?></span>
                </div>
            </div>
            <div class="control-group <?php echo form_error('title') ? 'error' : ''; ?>">
                <label class="control-label"><?php echo lang('label') . ':' ?></label>
                <div class="controls">
                    <input type="text" id="title" name="title" required value="<?php echo isset($setting_group['title']) ? $setting_group['title'] : ''; ?>">
                    <span class="help-inline"><?php echo form_error('title') ? lang('setting_groups_label_exist') : ''; ?></span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label"><?php echo lang('eng') . ':' ?></label>
                <div class="controls">
                    <input type="text" name="eng" required value="<?php echo isset($setting_group['eng']) ? $setting_group['eng'] : ''; ?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label"><?php echo lang('vie') . ':' ?></label>
                <div class="controls">
                    <input type="text" name="vie" required value="<?php echo isset($setting_group['vie']) ? $setting_group['vie'] : ''; ?>">
                </div>
            </div>
            <div class="form-actions">
                <input id="submit_btn" type="submit" name="create" class="btn btn-primary" value="<?php echo lang('create'); ?>" />
            </div>
        <?php else: ?>
            <tr>
                <td colspan="10"><?php if ($this->uri->segment(5, 'null') != 'null') echo lang('site_settings_no_reconds_found');else echo lang('site_settings_no_records'); ?></td>
            </tr>
        <?php endif; ?>
    </fieldset>
</div>
<?php echo form_close(); ?>