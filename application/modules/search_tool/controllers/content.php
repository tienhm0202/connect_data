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

		$this->auth->restrict('Search_Tool.Content.View');
		$this->lang->load('search_tool');
        $this->lang->load('books/books');
        $this->load->model('books/books_model', null, true);
        $this->load->library('pagination');
		
		Template::set_block('sub_nav', 'content/_sub_nav');

		Assets::add_module_js('search_tool', 'search_tool.js');
	}

	//--------------------------------------------------------------------


	/**
	 * Displays a list of form data.
	 *
	 * @return void
	 */
	public function index()
	{
        $condition = $like = array();
        Template::set('category', $this->books_model->get_cat_list());

        $config = $this->pager;
        $config['use_page_numbers'] = TRUE;
        $config['base_url'] = site_url(SITE_AREA . '/content/search_tool/index');
        $config['total_rows'] = $this->books_model->count_all();
        $config['per_page'] = $this->settings_lib->item('site.list_limit');
        $config['uri_segment'] = 5;
        $config['suffix'] = '?' . http_build_query($_GET, '', "&");
        $config['first_url'] = $config['base_url'] . $config['suffix'];
        $this->pagination->initialize($config);

        $offset = ($this->uri->segment(5, 1) - 1) * $config['per_page'];

        if($this->input->get()){
            if($this->input->get('category_id')){
                $condition['category_id'] = $this->input->get('category_id');
            }
            if($this->input->get('title')){
                $like['title'] = $this->input->get('title');
            }
            Template::set('books', $this->input->get());
        }


        $records = $this->books_model->search_books($this->current_user->id, 1, $offset, $this->settings_lib->item('site.list_limit'), $condition, $like);
        Template::set('records', $records);

        Template::set('toolbar_title', lang('search_tool_manage'));
		Template::render();
	}

	//--------------------------------------------------------------------

}