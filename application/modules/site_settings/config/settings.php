<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//start:general_settings.errors_report_to:
$config['setting']['general_settings.errors_report_to'] = array(
    'setting_group' => 'general_settings',
    'label' => 'lang:errors_report_to',
    'default_value' => 'datls@thinknet.vn|quydm@thinknet.vn',
    'data_type' => 'text',
    'values' => null,
    'required' => false,
    'sort_order' => 1,
);
/*
//end:general_settings.errors_report_to:
//start:general_settings.default_distributor:
$config['setting']['general_settings.default_distributor'] = array(
    'setting_group' => 'general_settings',
    'label' => 'lang:default_distributor',
    'default_value' => 1,
    'data_type' => 'sql-multiple',
    'values' => 'select role_id, role_name from bf_roles',
    'required' => false,
    'sort_order' => 2,
);

//end:api.log_level:
//start:revenue.realtime:
$config['setting']['revenue.realtime'] = array(
'setting_group' => 'general_settings',
'label' => 'lang:revenue_realtime',
'default_value' => '60',
'data_type' => 'text',
'values' => '60',
'required' => false,
'sort_order' => 3
);)*/
//end:revenue.realtime: