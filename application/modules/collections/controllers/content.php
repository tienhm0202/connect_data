<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * content controller
 */
class content extends Admin_Controller
{

    //--------------------------------------------------------------------


    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->auth->restrict('Collections.Content.View');
        $this->load->model('collections_model', null, true);
        $this->lang->load('collections');

        Assets::add_css('flick/jquery-ui-1.8.13.custom.css');
        Assets::add_js('jquery-ui-1.8.13.min.js');
        Assets::add_css('jquery-ui-timepicker.css');
        Assets::add_js('jquery-ui-timepicker-addon.js');
        Template::set_block('sub_nav', 'content/_sub_nav');

        Assets::add_module_js('collections', 'collections.js');
    }

    //--------------------------------------------------------------------


    /**
     * Displays a list of form data.
     *
     * @return void
     */
    public function index()
    {

        // Deleting anything?
        if (isset($_POST['delete'])) {
            $checked = $this->input->post('checked');

            if (is_array($checked) && count($checked)) {
                $result = FALSE;
                foreach ($checked as $pid) {
                    $result = $this->collections_model->delete($pid);
                }

                if ($result) {
                    Template::set_message(count($checked) . ' ' . lang('collections_delete_success'), 'success');
                } else {
                    Template::set_message(lang('collections_delete_failure') . $this->collections_model->error, 'error');
                }
            }
        }

        $records = $this->collections_model->find_all();

        Template::set('records', $records);
        Template::set('toolbar_title', lang('collections_manage'));
        Template::render();
    }

    //--------------------------------------------------------------------


    /**
     * Creates a Collections object.
     *
     * @return void
     */
    public function create()
    {
        $this->auth->restrict('Collections.Content.Create');

        if (isset($_POST['save'])) {
            if ($insert_id = $this->save_collections()) {
                // Log the activity
                log_activity($this->current_user->id, lang('collections_act_create_record') . ': ' . $insert_id . ' : ' . $this->input->ip_address(), 'collections');

                Template::set_message(lang('collections_create_success'), 'success');
                redirect(SITE_AREA . '/content/collections');
            } else {
                Template::set_message(lang('collections_create_failure') . $this->collections_model->error, 'error');
            }
        }
        Assets::add_module_js('collections', 'collections.js');

        Template::set('toolbar_title', lang('collections_manage'));
        Template::render();
    }

    //--------------------------------------------------------------------


    /**
     * Allows editing of Collections data.
     *
     * @return void
     */
    public function edit()
    {
        $id = $this->uri->segment(5);

        if (empty($id)) {
            Template::set_message(lang('collections_invalid_id'), 'error');
            redirect(SITE_AREA . '/content/collections');
        }

        $this->load->helper('collection_item');

        if (isset($_POST['save'])) {
            $this->auth->restrict('Collections.Content.Edit');

            if ($this->save_collections('update', $id)) {
                // Log the activity
                log_activity($this->current_user->id, lang('collections_act_edit_record') . ': ' . $id . ' : ' . $this->input->ip_address(), 'collections');

                Template::set_message(lang('collections_edit_success'), 'success');
                redirect(SITE_AREA . '/content/collections');
            } else {
                Template::set_message(lang('collections_edit_failure') . $this->collections_model->error, 'error');
            }
        } else if (isset($_POST['delete'])) {
            $this->auth->restrict('Collections.Content.Delete');

            if ($this->collections_model->delete($id)) {
                // Log the activity
                log_activity($this->current_user->id, lang('collections_act_delete_record') . ': ' . $id . ' : ' . $this->input->ip_address(), 'collections');

                Template::set_message(lang('collections_delete_success'), 'success');

                redirect(SITE_AREA . '/content/collections');
            } else {
                Template::set_message(lang('collections_delete_failure') . $this->collections_model->error, 'error');
            }
        }
        Template::set('collections', $this->collections_model->find($id));
        Template::set('book_list', $this->collections_model->find_book($id));
        Template::set('toolbar_title', lang('collections_manage'));
        Template::render();
    }

    public function add_collection()
    {
        if ($this->input->get('url')) {
            $url = $this->input->get('url');
        } else {
            $url = SITE_AREA . '/content/collections';
        }

        if ($this->input->get('book_id')) {
            if ($this->input->get('collection_id')) {
                $this->collections_model->add_collections($this->input->get('book_id'), $this->input->get('collection_id'));
                Template::set_message(lang('add_collection_success'), 'success');
                redirect($url);
            } else {
                Template::set('collection_list', $this->collections_model->find_my_collections($this->auth->user_id()));
                Template::set('toolbar_title', lang('collections_manage'));
                Template::set('get', $this->input->get());
                Template::set('url', $url);
                Template::render();
            }
        } else {
            Template::set_message(lang('collections_invalid_id'), 'error');
            redirect($url);
        }
    }

    public function share()
    {
        $id = $this->uri->segment(5);

        $this->load->helper('collection_item');

        if (empty($id)) {
            Template::set_message(lang('collections_invalid_id'), 'error');
            redirect(SITE_AREA . '/content/collections');
        }

        if (!$this->collections_model->is_publish($id)) {
            Template::set_message(lang('collections_not_publish'), 'error');
            redirect(SITE_AREA . '/content/collections');
        }

        Template::set('collections', $this->collections_model->find($id));
        Template::set('book_list', $this->collections_model->find_book($id));
        Template::set('toolbar_title', lang('collections_manage'));
        Template::render();

    }

    //--------------------------------------------------------------------

    //--------------------------------------------------------------------
    // !PRIVATE METHODS
    //--------------------------------------------------------------------

    /**
     * Summary
     *
     * @param String $type Either "insert" or "update"
     * @param Int $id The ID of the record to update, ignored on inserts
     *
     * @return Mixed    An INT id for successful inserts, TRUE for successful updates, else FALSE
     */
    private function save_collections($type = 'insert', $id = 0)
    {
        if ($type == 'update') {
            $_POST['collection_id'] = $id;
            $this->collections_model->saveBook($this->input->post('book_id'), $id);
            unset($_POST['book_id']);
        }

        // make sure we only pass in the fields we want

        $data = array();
        $data['collection_name'] = $this->input->post('collections_collection_name');
        $data['publish'] = $this->input->post('collections_publish');

        if ($type == 'insert') {
            $data['collection_owner'] = $this->auth->user_id();
            $data['created_on'] = date('Y-m-d H:i:s');
            $id = $this->collections_model->insert($data);

            if (is_numeric($id)) {
                $return = $id;
            } else {
                $return = FALSE;
            }
        } elseif ($type == 'update') {
            $data['updated_on'] = date('Y-m-d H:i:s');
            $return = $this->collections_model->update($id, $data);
        }

        return $return;
    }

    //--------------------------------------------------------------------


}