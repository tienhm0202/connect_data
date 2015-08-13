<?php echo form_open($this->uri->uri_string(), 'class="form-horizontal"'); ?>
<div class="admin-box">
    <ul class="nav nav-tabs" >
        <!-- doan code tu sinh ra so tab theo so luong setting_group -->
        <!-- <li <?php echo (strpos($group_list, $this->uri->segment(4)) === false) ? 'class="active"' : ''; ?>><a href="<?php echo site_url(SITE_AREA . '/settings/site_settings/'); ?>"><?php echo lang('site_settings_all'); ?></a></li> -->
        <?php if (isset($groups) && count($groups) > 0): ?>
            <?php foreach ($groups as $group): ?>
                <li <?php echo ($this->uri->segment(4, 'index') == $group['name']) ? 'class="active"' : ''; ?>><a href="<?php echo site_url(SITE_AREA . '/settings/site_settings/' . $group['name']); ?>"><?php echo lang($group['title']); ?></a></li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <fieldset>
        <?php if ($this->auth->has_permission('Site_settings.Settings.Edit') && isset($items) && count($items) > 0): ?>
            <?php foreach ($items as $item): ?>
                <?php $item['name'] = str_replace('.', '_DOT_', $item['name']); ?>
                <div class="control-group">
                    <?php echo form_label(lang($item['label']), $item['name'], array('class' => "control-label")); ?>
                    <div class='controls'>
                        <?php
                        switch ($item['data_type']) {
                            case 'text': {
                                    ?>
                                    <input id="<?php echo $item['name']; ?>" class="span6" type="text" <?php echo ($item['required']) ? "required" : ""; ?> name="<?php echo $item['name']; ?>" maxlength="255" value="<?php echo set_value('value', isset($item['value']) ? $item['value'] : ''); ?>" />
                                    <?php
                                    break;
                                }
                            case 'textarea': {
                                    ?>
                                    <textarea class="span5" id="<?php echo $item['name']; ?>" name="<?php echo $item['name']; ?>" rows="3" ><?php echo set_value('value', isset($item['value']) ? $item['value'] : ''); ?></textarea>
                                    <?php
                                    break;
                                }
                            case 'password': {
                                    ?>
                                    <input type="password" name="<?php echo $item['name']; ?>" <?php echo ($item['required']) ? "required" : ""; ?> id="<?php echo $item['name']; ?>" value="<?php echo set_value('value', isset($item['value']) ? $item['value'] : ''); ?>" />
                                    <?php
                                    break;
                                }
                            case 'select': {
                                    ?>
                                    <select id="<?php echo $item['name']; ?>" name="<?php echo $item['name']; ?>" >
                                        <?php if (isset($item['values']) && count($item['values']) > 0): ?>
                                            <?php $values = array_keys($item['values']); ?>
                                            <?php foreach ($values as $value): ?>
                                                <?php
                                                if (strpos($item['values'][$value], 'lang:') !== false) {
                                                    $lang = $item['values'][$value];
                                                    $lang = explode(':', $lang);
                                                    $lang = lang($lang[1]);
                                                } else {
                                                    $lang = $item['values'][$value];
                                                }
                                                ?>
                                                <option <?php if ($value == $item['value']) echo 'selected = "selected"';else echo ""; ?> value="<?php echo $value; ?>"><?php echo $lang; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif ?>
                                    </select>
                                    <?php
                                    break;
                                }
                            case 'radio': {
                                    ?>
                                    <?php if (isset($item['values']) && count($item['values']) > 0): ?>
                                        <?php $values = array_keys($item['values']); ?>
                                        <?php foreach ($values as $value): ?>
                                            <?php
                                            if (strpos($item['values'][$value], 'lang:') !== false) {
                                                $lang = $item['values'][$value];
                                                $lang = explode(':', $lang);
                                                $lang = lang($lang[1]);
                                            } else {
                                                $lang = $item['values'][$value];
                                            }
                                            ?>
                                            <input type="radio" name="<?php echo $item['name']; ?>" id="<?php echo $item['name']; ?>" value="<?php echo $value; ?>" /><label class="help-inline"><?php echo $lang; ?></label>
                                        <?php endforeach; ?>
                                    <?php endif ?>
                                    <?php
                                    break;
                                }
                            case 'sql-single': {
                                    if (isset($item['values']) && count($item['values']) > 0) {
                                        $results = $this->db->query($item['values']);
                                        if ($results->num_rows() < 1) {
                                            ?>
                                            <select>
                                                <option value=""><?php echo lang('no_role') ?></option>                                                   
                                            </select>
                                            <?php
                                        } else {
                                            $results = $results->result();
                                            ?>
                                            <select id="<?php echo $item['name']; ?>" name="<?php echo $item['name']; ?>" multiple class="chosen-select">
                                                <?php foreach ($results as $result) {
                                                    ?>
                                                    <option <?php if ($result->role_id == $item['value']) echo 'selected = "selected"';else echo ""; ?> value="<?php echo $result->role_id ?>"><?php echo $result->role_name ?></option>
                                                    <?php
                                                }
                                                echo '</select>';
                                            }
                                        }
                                        break;
                                    }
                                case 'sql-multiple': {
                                        if (isset($item['values']) && count($item['values']) > 0) {
                                            $results = $this->db->query($item['values']);
                                            if ($results->num_rows() < 1) {
                                                ?>
                                                <select>
                                                    <option value=""><?php echo lang('no_role') ?></option>                                                   
                                                </select>
                                                <?php
                                            } else {
                                                $results = $results->result();
                                                ?>
                                                <select id="<?php echo $item['name']; ?>" name="<?php echo $item['name'] . '[]'; ?>" multiple class="chosen-select">
                                                    <?php foreach ($results as $result) {
                                                        ?>
                                                        <?php if (is_array($item['value']) && count($item['value'])): ?>
                                                            <?php $last_item = end($item['value']); ?>
                                                            <?php foreach ($item['value'] as $role_id): ?>
                                                                <?php if ($result->role_id == $role_id): ?>
                                                                    <option selected = "selected" value="<?php echo $result->role_id ?>"><?php echo $result->role_name ?></option>
                                                                    <?php break; ?>
                                                                <?php elseif ($role_id == $last_item): ?>
                                                                    <option value="<?php echo $result->role_id ?>"><?php echo $result->role_name ?></option>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                        <?php
                                                    }
                                                    echo '</select>';
                                                }
                                            }
                                            break;
                                        }
                                }
                                ?>
                                </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="form-actions">
                                <input id="submit_btn" type="submit" name="submit" class="btn btn-primary" value="<?php echo lang('site_settings_submit'); ?>" />
                            </div>
                        <?php else: ?>
                            <tr>
                                <td colspan="10"><?php if ($this->uri->segment(5, 'null') != 'null') echo lang('site_settings_no_reconds_found');else echo lang('site_settings_no_records'); ?></td>
                            </tr>
                        <?php endif; ?>
                        </fieldset>
                        </div>
                        <?php echo form_close(); ?>