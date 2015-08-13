<ul class="nav nav-pills">
    <?php if ($this->auth->has_permission('Site_settings.Settings.View')): ?>
        <li <?php echo ($this->uri->segment(4) !== 'list_site_setting' && $this->uri->segment(4) !== 'create_site_setting'
                && $this->uri->segment(4) != 'list_setting_group' && $this->uri->segment(4) != 'create_setting_group') ? 'class="active"' : '' ?>>
            <a href="<?php echo site_url(SITE_AREA . '/settings/site_settings/') ?>"><?php echo lang('site_settings'); ?></a>
        </li>
    <?php endif; ?>
    <?php if ($this->auth->has_permission('Site_settings.Settings.Create') || $this->auth->has_permission('Site_settings.Settings.Delete')): ?>
        <li <?php echo ($this->uri->segment(4) == 'list_site_setting' || $this->uri->segment(4) == 'create_site_setting') ? 'class="active"' : '' ?> >
            <a href="<?php echo site_url(SITE_AREA . '/settings/site_settings/list_site_setting') ?>" id="manage"><?php echo lang('manage_settings'); ?></a>
        </li>
    <?php endif; ?>
    <?php if ($this->auth->has_permission('Site_settings.Settings.Create') || $this->auth->has_permission('Site_settings.Settings.Delete')): ?>
        <li <?php echo ($this->uri->segment(4) == 'list_setting_group' || $this->uri->segment(4) == 'create_setting_group') ? 'class="active"' : '' ?> >
            <a href="<?php echo site_url(SITE_AREA . '/settings/site_settings/list_setting_group') ?>" id="manage"><?php echo lang('manage_setting_groups'); ?></a>
        </li>
    <?php endif; ?>    
</ul>