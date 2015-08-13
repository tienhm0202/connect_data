<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>
<div class="admin-box">
    <ul class="nav nav-tabs" >
        <li <?php echo ($this->uri->segment(4) == 'list_site_setting') ? 'class="active"' : ''; ?>><a href="<?php echo site_url(SITE_AREA . '/settings/site_settings/list_site_setting'); ?>"><?php echo lang('delete_site_setting'); ?></a></li>
        <li <?php echo ($this->uri->segment(4) == 'create_site_setting') ? 'class="active"' : ''; ?>><a href="<?php echo site_url(SITE_AREA . '/settings/site_settings/create_site_setting'); ?>"><?php echo lang('create_site_setting'); ?></a></li>
    </ul>
    <fieldset>
        <?php if ($this->auth->has_permission('Site_settings.Settings.Create')): ?>
            <div class="control-group">
                <label class="control-label"><?php echo lang('setting_group') . ':' ?></label>
                <div class="controls">
                    <select class="chosen-select" name="setting_group">
                        <?php if (isset($list_setting_group) && is_array($list_setting_group) && count($list_setting_group)): ?>
                            <?php foreach ($list_setting_group as $setting): ?>
                                <?php $setname = str_replace('.', '_DOT_', $setting); ?>
                                <?php if (isset($site_setting['setting_group']) && $site_setting['setting_group'] == $setname): ?>
                                    <option value="<?php echo $setname; ?>" selected><?php echo $setting ?></option> 
                                <?php else: ?>
                                    <option value="<?php echo $setname; ?>"><?php echo $setting ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="control-group <?php echo form_error('setting_name') ? 'error' : ''; ?>">
                <label class="control-label"><?php echo lang('setting_name') . ':' ?></label>
                <div class="controls">
                    <input type="text" name="setting_name" required value="<?php echo isset($site_setting['setting_name']) ? $site_setting['setting_name'] : '' ?>">
                    <span class="help-inline"><?php echo form_error('setting_name') ? lang('settings_name_exist') : ''; ?></span>
                </div>
            </div>
            <div class="control-group <?php echo form_error('label') ? 'error' : ''; ?>">
                <label class="control-label"><?php echo lang('label') . ':' ?></label>
                <div class="controls">
                    <input type="text" name="label" required value="<?php echo isset($site_setting['label']) ? $site_setting['label'] : '' ?>">
                    <span class="help-inline"><?php echo form_error('label') ? lang('site_settings_label_exist') : ''; ?></span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label"><?php echo lang('eng') . ':' ?></label>
                <div class="controls">
                    <input type="text" name="eng" required value="<?php echo isset($site_setting['eng']) ? $site_setting['eng'] : '' ?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label"><?php echo lang('vie') . ':' ?></label>
                <div class="controls">
                    <input type="text" name="vie" required value="<?php echo isset($site_setting['vie']) ? $site_setting['vie'] : '' ?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label"><?php echo lang('default_value') . ':' ?></label>
                <div class="controls">
                    <input type="text" name="default_value" required value="<?php echo isset($site_setting['default_value']) ? $site_setting['default_value'] : '' ?>">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label"><?php echo lang('data_type') . ':' ?></label>
                <div class="controls">
                    <select class="chosen-select" name="data_type" id="data-type">
                        <?php foreach ($data_type as $type): ?>
                            <?php if (isset($site_setting['data_type']) && $type == $site_setting['data_type']): ?>
                                <option value="<?php echo $type; ?>" selected><?php echo $type; ?></option>
                            <?php else: ?>
                                <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label"><?php echo lang('values') . ':' ?></label>
                <div class="controls" id="single-input" style="display: block;">
                    <input type="text" name="values" required value="<?php echo isset($site_setting['values']) ? $site_setting['values'] : '' ?>">
                </div>
                <div class="controls" id="select-input" style="display: none;">
                    <div id="controler" style="margin-bottom: 10px">                        
                        <span class="btn btn-info" id="add-element"><i class="icon-plus icon-white"></i><?php echo lang('add_element') ?></span>
                        <span class="btn btn-danger" id="del-element"><i class="icon-minus icon-white"></i><?php echo lang('del_element') ?></span>
                    </div>
                    <div id="select-input-1" class="select-input-multiple">
                        <?php echo lang('key'); ?><input type="text" name="values[1][key]" required style="width: 100px" disabled="disabled"><i class="icon-arrow-right"></i><?php echo lang('val'); ?><input type="text" name="values[1][val]" required style="width: 100px" disabled="disabled">
                    </div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label"><?php echo lang('required') . ':' ?></label>
                <div class="controls">
                    <input type="checkbox" name="required" value="1" <?php echo (isset($site_setting['required']) && ($site_setting['required'] == 1)) ? 'checked' : ''; ?>>
                </div>
            </div>
            <div class="form-actions">
                <input type="submit" name="create" class="btn btn-primary" value="<?php echo lang('create'); ?>" />
            </div>
        <?php else: ?>
            <tr>
                <td colspan="10"><?php if ($this->uri->segment(5, 'null') != 'null') echo lang('site_settings_no_reconds_found');else echo lang('site_settings_no_records'); ?></td>
            </tr>
        <?php endif; ?>
    </fieldset>
</div>
<?php echo form_close(); ?>