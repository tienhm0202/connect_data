<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Settings extends Admin_Controller {

    //--------------------------------------------------------------------
    public function __construct() {
        parent::__construct();
        $this->auth->restrict('Site_settings.Settings.View');
        $this->load->model('site_settings_model');
        $this->load->helper('path');
        $this->load->library('form_validation');
        $this->lang->load('setting_groups');
        $this->lang->load('settings');
        $this->lang->load('site_settings');
        $this->config->load('site_settings/settings');
        $this->config->load('site_settings/setting_groups');
        //Template::set_block('sub_nav', 'settings/_sub_nav');
        Assets::add_js('chosen.jquery.min.js');
        Assets::add_css('chosen.css');
        Assets::add_module_css('site_settings', 'site_setting.css');
    }

    //--------------------------------------------------------------------

    /**
     * Remap methods
     *
     * @access public
     *
     * @param string $method Name of the method being requested
     */
    public function _remap($method) {
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            $this->index();
        }
    }

    public function index() {
        if ($this->input->post()) {
            $list = $this->input->post();
            foreach ($list as $setting => $value) {
                $item['name'] = str_replace('_DOT_', '.', $setting);
                if (is_array($value) && count($value)) {
                    $item['value'] = null;
                    foreach ($value as $key => $val) {
                        $item['value'] .= ((empty($item['value'])) ? '' : ',') . $val;
                    }
                }else
                    $item['value'] = $value;
                if ($setting != 'submit')
                    $data[] = $item;
            }
            $this->db->update_batch('settings', $data, 'name');
            Template::set_message(lang('update_success'), 'success');
        }

        $setting_group = $this->config->item('setting_group');
        $settings_info = $this->config->item('setting');
        $settings = array_keys($settings_info);

        foreach ($settings as $setting) {
            $settings_info[$setting]['name'] = $setting;
            $lang = $settings_info[$setting]['label'];
            $lang = explode(':', $lang);
            $settings_info[$setting]['label'] = $lang[1];
            $this->site_settings_model->sync_setting($settings_info[$setting]);
            $settings_info[$setting]['value'] = $this->site_settings_model->get_setting($setting);
            if ($settings_info[$setting]['data_type'] == 'sql-multiple') {
                $role_list = array();
                $array = explode(',', $settings_info[$setting]['value']);
                if (isset($array) && is_array($array) && count($array)) {
                    foreach ($array as $distributor_id) {
                        $role_list[] = $distributor_id;
                    }
                    $settings_info[$setting]['value'] = $role_list;
                }
            }
        }

        $groups = array_keys($setting_group);
        foreach ($groups as $group) {
            $setting_group[$group]['name'] = $group;
            $lang = $setting_group[$group]['title'];
            $lang = explode(':', $lang);
            $setting_group[$group]['title'] = $lang[1];
        }
        $group = implode($groups, ',');
        Template::set('group_list', $group);

        usort($setting_group, "sort_func");

        if (strpos($group, $this->uri->segment(4)) !== false) {
            //Loc theo group
            $group = $this->uri->segment(4);
            $items = array();
            foreach ($settings_info as $setting) {
                if (strtolower($setting['setting_group']) == strtolower($group))
                    $items[] = $setting;
            }
        } else {
            //trang mac dinh
            redirect(site_url(SITE_AREA . '/settings/site_settings/' . $setting_group[0]['name']));
        }

        Assets::add_js($this->load->view('settings/chosen_inline_js', null, true), 'inline');
        Template::set('groups', $setting_group);
        usort($items, "sort_func");
        Template::set('items', $items);

        Template::set('toolbar_title', lang('site_settings'));
        Template::set_view('settings/index');
        Template::render();
    }

    //list site setting to delete
    public function list_site_setting() {
        $config_settings = 'bonfire/modules/site_settings/config';
        $realpath = substr(set_realpath($config_settings, TRUE), 0, -1);
        $file_name = $realpath . '/settings.php';
        $config_settings_content = file_get_contents($file_name);
        $config_settings_info = $this->config->item('setting');

        if ($this->input->post('delete')) {
            $del_setting = true;
            if ($this->input->post('checked') && is_array($this->input->post('checked')) && count($this->input->post('checked'))) {
                foreach ($this->input->post('checked') as $setting) {
                    $setname = str_replace('_DOT_', '.', $setting);
                    $lang_name = str_replace('lang:', '', $config_settings_info[$setname]['label']);
                    $del_setting = $del_setting && $this->delete_a_config($setname, $lang_name, 'site_settings', $file_name);
                }
                if ($del_setting) {
                    Template::set_message(lang('setting_delete_success'), 'success');
                    redirect(site_url(SITE_AREA . '/settings/site_settings/list_site_setting'));
                } else {
                    Template::set_message(lang('setting_delete_failure'), 'error');
                }
            } else {
                Template::set_message(lang('site_setting_checked_empty'), 'error');
            }
        }

        $list_settings = $this->read_a_config($config_settings_content);
        Template::set(array('list_settings' => $list_settings));
        Template::set('toolbar_title', lang('manage_settings'));
        Template::render();
    }

    //create new site setting
    public function create_site_setting() {
        $data_type = array('text', 'textarea', 'password', 'select',
            'radio', 'sql-single', 'sql-multiple');

        $config_settings = 'bonfire/modules/site_settings/config';
        $realpath = substr(set_realpath($config_settings, TRUE), 0, -1);
        $file_name = $realpath . '/setting_groups.php';
        $config_settings_group_content = file_get_contents($file_name);
        $config_settings_info = $this->config->item('setting');
        if ($this->input->post('create')) {
            $this->form_validation->set_rules('setting_name', lang('setting_name'), 'callback_siteSettingUnique[settings]');
            $this->form_validation->set_rules('label', lang('label'), 'callback_labelUnique[site_settings]');
            if ($this->form_validation->run() === FALSE) {
                Template::set('site_setting', $this->input->post());
            } else {
                $setting_group = str_replace('_DOT_', '.', $this->input->post('setting_group'));
                $required = $this->input->post('required') ? 'true' : 'false';
                $config = "\r\n" . '//start:' . $this->input->post('setting_name') . ":\r\n";
                $config .= '$config[\'setting\'][\'' . $this->input->post('setting_name') . '\'] = array(' . "\r\n";
                $config .= "'setting_group' => '" . $setting_group . "',\r\n";
                $config .= "'label' => 'lang:" . $this->input->post('label') . "',\r\n";
                $config .= "'default_value' => '" . $this->input->post('default_value') . "',\r\n";
                $config .= "'data_type' => '" . $this->input->post('data_type') . "',\r\n";
                if ($this->input->post('data_type') == 'radio' || $this->input->post('data_type') == 'select') {
                    $values = $this->input->post('values');
                    $config .= "'values' => array(\r\n";
                    if (isset($values) && is_array($values) && count($values)) {
                        $end_element = end($values);
                        foreach ($values as $key => $value) {
                            if ($key == key($end_element))
                                $config .= "'" . $value['key'] . "' => '" . $value['val'] . "'\r\n";
                            else
                                $config .= "'" . $value['key'] . "' => '" . $value['val'] . "',\r\n";
                        }
                    }
                    $config .= "),\r\n";
                } else {
                    $values = $this->input->post('values') ? ("'" . $this->input->post('values') . "'") : 'null';
                    $config .= "'values' => " . $values . ",\r\n";
                }

                $config .= "'required' => " . $required . ",\r\n";
                $config .= "'sort_order' => " . count($config_settings_info) . "\r\n";
                $config .= ');' . "\r\n";
                $config .= '//end:' . $this->input->post('setting_name') . ':';
                if ($this->input->post('label')) {
                    $config_settings = 'bonfire/modules/site_settings/language';
                    $realpath_lang = substr(set_realpath($config_settings, TRUE), 0, -1);

                    if (is_writable($realpath_lang . '/english/site_settings_lang.php') &&
                            is_writable($realpath_lang . '/vietnamese/site_settings_lang.php')) {

                        $lang_eng = "\r\n" . '$lang[\'' . $this->input->post('label') . '\']="' . $this->input->post('eng') . '";';
                        $eng = file_put_contents($realpath_lang . '/english/site_settings_lang.php', $lang_eng, FILE_APPEND);

                        $lang_vie = "\r\n" . '$lang[\'' . $this->input->post('label') . '\']="' . $this->input->post('vie') . '";';
                        $vie = file_put_contents($realpath_lang . '/vietnamese/site_settings_lang.php', $lang_vie, FILE_APPEND);
                        if ($eng && $vie) {
                            if (is_writable($realpath . '/settings.php')) {
                                $create = file_put_contents($realpath . '/settings.php', $config, FILE_APPEND);
                                if ($create) {
                                    Template::set(lang('create_success', 'success'));
                                    redirect(site_url(SITE_AREA . '/settings/site_settings/list_site_setting'));
                                } else {
                                    Template::set(lang('create_success', 'error'));
                                }
                            } else {
                                $message = lang('file_is_not_writable');
                                $message = str_replace('_FILE_', 'config/settings.php', $message);
                                Template::message($message, 'error');
                            }
                        }
                    } else {
                        $message = lang('file_is_not_writable');
                        $message = str_replace('_FILE_', $realpath_lang, $message);
                        Template::message($message, 'error');
                    }
                }
            }
        }

        $list_setting_group = $this->read_a_config($config_settings_group_content);

        Assets::add_js($this->load->view('settings/chosen_inline_js', null, true), 'inline');
        Template::set(array('list_setting_group' => $list_setting_group, 'data_type' => $data_type));
        Template::set('toolbar_title', lang('manage_settings'));
        Template::render();
    }

    //list setting group to delete
    public function list_setting_group() {
        $config_settings = 'application/modules/site_settings/config';
        $realpath = substr(set_realpath($config_settings, TRUE), 0, -1);
        $file_name = $realpath . '/setting_groups.php';
        $config_settings_content = file_get_contents($file_name);
        $config_settings_info = $this->config->item('setting_group');

        if ($this->input->post('delete')) {
            $del_setting = true;
            if ($this->input->post('checked') && is_array($this->input->post('checked')) && count($this->input->post('checked'))) {
                foreach ($this->input->post('checked') as $setting) {
                    $setname = str_replace('_DOT_', '.', $setting);
                    $lang_name = str_replace('lang:', '', $config_settings_info[$setname]['title']);
                    $del_setting = $del_setting && $this->delete_a_config($setname, $lang_name, 'setting_groups', $file_name);
                }
                if ($del_setting) {
                    Template::set_message(lang('setting_delete_success'), 'success');
                    redirect(site_url(SITE_AREA . '/settings/site_settings/list_setting_group'));
                } else {
                    Template::set_message(lang('setting_delete_failure'), 'error');
                }
            } else {
                Template::set_message(lang('site_setting_checked_empty'), 'error');
            }
        }

        $list_settings = $this->read_a_config($config_settings_content);
        Template::set(array('list_settings' => $list_settings));
        Template::set('toolbar_title', lang('manage_setting_groups'));
        Template::render();
    }

    //create new setting group
    public function create_setting_group() {
        $config_settings = 'bonfire/modules/site_settings/config';
        $realpath = substr(set_realpath($config_settings, TRUE), 0, -1);
        $file_name = $realpath . '/setting_groups.php';
        $config_settings_group_content = file_get_contents($file_name);
        $config_settings_info = $this->config->item('setting_group');
        $sort_order = count($config_settings_info);
        if ($this->input->post('create')) {
            $this->form_validation->set_rules('setting_group_name', lang('setting_name'), 'callback_siteSettingUnique[setting_groups]');
            $this->form_validation->set_rules('title', lang('label'), 'callback_labelUnique[setting_groups]');
            if ($this->form_validation->run() === FALSE) {
                Template::set('setting_group', $this->input->post());
            } else {
                $config = "\r\n" . '//start:' . $this->input->post('setting_group_name') . ":\r\n";
                $config .= '$config[\'setting_group\'][\'' . $this->input->post('setting_group_name') . '\'] = array(' . "\r\n";
                $config .= "'title' => 'lang:" . $this->input->post('setting_group_name') . "',\r\n";
                $config .= "'sort_order' => " . $sort_order . "\r\n";
                $config .= ');' . "\r\n";
                $config .= '//end:' . $this->input->post('setting_group_name') . ':';
                if ($this->input->post('title')) {
                    $config_settings = 'bonfire/modules/site_settings/language';
                    $realpath_lang = substr(set_realpath($config_settings, TRUE), 0, -1);

                    if (is_writable($realpath_lang . '/english/setting_groups_lang.php') &&
                            is_writable($realpath_lang . '/vietnamese/setting_groups_lang.php')) {

                        $lang_eng = "\r\n" . '$lang[\'' . $this->input->post('title') . '\']="' . $this->input->post('eng') . '";';
                        $eng = file_put_contents($realpath_lang . '/english/setting_groups_lang.php', $lang_eng, FILE_APPEND);

                        $lang_vie = "\r\n" . '$lang[\'' . $this->input->post('title') . '\']="' . $this->input->post('vie') . '";';
                        $vie = file_put_contents($realpath_lang . '/vietnamese/setting_groups_lang.php', $lang_vie, FILE_APPEND);
                        if ($eng && $vie) {
                            if (is_writable($realpath . '/setting_groups.php')) {
                                $create = file_put_contents($realpath . '/setting_groups.php', $config, FILE_APPEND);
                                if ($create) {
                                    Template::set(lang('create_success', 'success'));
                                    redirect(site_url(SITE_AREA . '/settings/site_settings/list_setting_group'));
                                } else {
                                    Template::set(lang('create_success', 'error'));
                                }
                            } else {
                                $message = lang('file_is_not_writable');
                                $message = str_replace('_FILE_', 'config/setting_groups.php', $message);
                                Template::message($message, 'error');
                            }
                        } else {
                            $message = lang('file_is_not_writable');
                            $message = str_replace('_FILE_', $realpath_lang, $message);
                            Template::message($message, 'error');
                        }
                    } else {
                        $message = lang('file_is_not_writable');
                        $message = str_replace('_FILE_', $realpath_lang, $message);
                        Template::message($message, 'error');
                    }
                }
            }
        }

        $list_setting_group = $this->read_a_config($config_settings_group_content);

        Template::set(array('list_setting_group' => $list_setting_group));
        Template::set('toolbar_title', lang('manage_setting_groups'));
        Template::render();
    }

    private function read_a_config($content) {
        $pattern = '/start:(.*?):/';
        preg_match_all($pattern, $content, $matches);
        if (count($matches[1]))
            return $matches[1];
        return;
    }

    private function delete_a_config($setname, $lang, $type, $filename) {
        $content = file($filename, FILE_IGNORE_NEW_LINES);
        $new_content = $content;
        $startline = $endline = 0;
        foreach ($content as $key => $line) {
            if ($line == '//start:' . $setname . ':') {
                $startline = $key;
            }
            if ($line == '//end:' . $setname . ':') {
                $endline = $key;
            }
            if ($startline && $endline) {
                for ($i = $startline; $i <= $endline; $i++) {
                    unset($new_content[$i]);
                }
                $startline = $endline = 0;
            }
        }
        if (is_writable($filename))
            $del_config = file_put_contents($filename, implode("\r\n", $new_content));
        else {
            $message = lang('file_is_not_writable');
            $message = str_replace('_FILE_', 'config/' . $type . '.php', $message);
            Template::message($message, 'error');
            return false;
        }

        $config_settings = 'bonfire/modules/site_settings/language';
        $realpath = substr(set_realpath($config_settings, TRUE), 0, -1);

        // create english
        $lang_english = file($realpath . '/english/' . $type . '_lang.php', FILE_IGNORE_NEW_LINES);
        foreach ($lang_english as $key => $eng) {
            if (stripos($eng, $lang)) {
                $lang_to_del = $key;
                break;
            }
        }
        if (isset($lang_to_del)) {
            unset($lang_english[$lang_to_del]);
            if (is_writable($realpath . '/english/' . $type . '_lang.php'))
                $del_eng = file_put_contents($realpath . '/english/' . $type . '_lang.php', implode("\r\n", $lang_english));
            else {
                $message = lang('file_is_not_writable');
                $message = str_replace('_FILE_', '/lang/english/' . $type . '_lang.php', $message);
                Template::message($message, 'error');
                return false;
            }
        }

        //create vietnamese
        $lang_vietnamese = file($realpath . '/vietnamese/' . $type . '_lang.php', FILE_IGNORE_NEW_LINES);
        foreach ($lang_vietnamese as $key => $vie) {
            if (stripos($vie, $lang)) {
                $lang_to_del = $key;
                break;
            }
        }
        if (isset($lang_to_del)) {
            unset($lang_vietnamese[$lang_to_del]);
            if (is_writable($realpath . '/vietnamese/' . $type . '_lang.php'))
                $del_vie = file_put_contents($realpath . '/vietnamese/' . $type . '_lang.php', implode("\r\n", $lang_vietnamese));
            else {
                $message = lang('file_is_not_writable');
                $message = str_replace('_FILE_', '/lang/vietnamese/' . $type . '_lang.php', $message);
                Template::message($message, 'error');
                return false;
            }
        }

        return ($del_config && $del_eng && $del_vie);
    }

    public function siteSettingUnique($str, $file) {
        $config_settings = 'bonfire/modules/site_settings/config';
        $realpath = substr(set_realpath($config_settings, TRUE), 0, -1);
        $file_name = $realpath . '/' . $file . '.php';
        $config_settings_content = file_get_contents($file_name);
        $list_settings = $this->read_a_config($config_settings_content);
        foreach ($list_settings as $setting) {
            if ($setting == $str) {
                Template::set_message(lang($file . '_name_exist'), 'error');
                return false;
            }
        }
        return true;
    }

    public function labelUnique($str, $file) {
        $config_settings = 'bonfire/modules/site_settings/language';
        $realpath = substr(set_realpath($config_settings, TRUE), 0, -1);
        $file_name = $realpath . '/english/' . $file . '_lang.php';
        $config_settings_content = file($file_name);
        foreach ($config_settings_content as $key => $lang) {
            if (stripos($lang, $str)) {
                Template::set_message(lang($file . '_label_exist'), 'error');
                return false;
            }
        }
        return true;
    }

}

function sort_func($item1, $item2) {
    if ($item1['sort_order'] == $item2['sort_order'])
        return 0;
    return ($item1['sort_order'] < $item2['sort_order']) ? -1 : 1;
}