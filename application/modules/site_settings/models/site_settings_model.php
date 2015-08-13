<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Site_settings_model extends BF_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    //--------------------------------------------------------------------

    public function sync_setting($setting)
    {
        //Kiem tra xem trong db da co setting nay chua, neu chua thi insert vao db
        if($this->settings_lib->item($setting['name']) === false){
            $info = array(
                'name' => $setting['name'],
                'module' => 'site_settings',
                'value' => $setting['default_value']
            );
            $this->db->insert('settings',$info);
        }
    }

    //--------------------------------------------------------------------

    public function get_setting($setting_name)
    {
        $value = $this->db->select('*')
                            ->where('name',$setting_name)
                            ->get('settings');
        if($value->num_rows()<1) return false;
        $value = $value->result_array();
        return $value[0]['value'];
    }
}
