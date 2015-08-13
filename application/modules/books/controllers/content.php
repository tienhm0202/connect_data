<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;

$root_dir = str_replace('\modules\books\controllers', '', __DIR__);
require_once $root_dir . '/libraries/PhpWord/Autoloader.php';

/**
 * content controller
 */
class content extends Admin_Controller
{
    //--------------------------------------------------------------------

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->auth->restrict('Books.Content.View');
        $this->load->model('books_model', null, true);
        $this->lang->load('books');
        $this->load->library('pagination');

        Assets::add_css('flick/jquery-ui-1.8.13.custom.css');
        Assets::add_js('jquery-ui-1.8.13.min.js');
        Assets::add_css('jquery-ui-timepicker.css');
        Assets::add_js('jquery-ui-timepicker-addon.js');
        Template::set_block('sub_nav', 'content/_sub_nav');

        Assets::add_module_js('books', 'books.js');
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
                    $result = $this->books_model->delete($pid);
                }

                if ($result) {
                    $this->books_model->delete_book_user($pid);
                    Template::set_message(count($checked) . ' ' . lang('books_delete_success'), 'success');
                } else {
                    Template::set_message(lang('books_delete_failure') . $this->books_model->error, 'error');
                }
            }
        }
        $config = $this->pager;
        $config['use_page_numbers'] = TRUE;
        $config['base_url'] = site_url(SITE_AREA . '/content/books/index');
        $config['total_rows'] = $this->books_model->count_all();
        $config['per_page'] = $this->settings_lib->item('site.list_limit');
        $config['uri_segment'] = 5;
        $config['suffix'] = '?' . http_build_query($_GET, '', "&");
        $config['first_url'] = $config['base_url'] . $config['suffix'];
        $this->pagination->initialize($config);

        $offset = ($this->uri->segment(5, 1) - 1) * $config['per_page'];

        $records = $this->books_model->find_books($this->current_user->id, 7, $offset, $this->settings_lib->item('site.list_limit'));

        Template::set('records', $records);
        Template::set('toolbar_title', lang('books_manage'));
        Template::render();
    }

    //--------------------------------------------------------------------

    /**
     * Creates a books object.
     *
     * @return void
     */
    public function create()
    {
        $this->auth->restrict('Books.Content.Create');

        if (isset($_POST['save'])) {
            if ($insert_id = $this->save_books()) {
                // Log the activity
                log_activity($this->current_user->id, lang('books_act_create_record') . ': ' . $insert_id . ' : ' . $this->input->ip_address(), 'books');

                Template::set_message(lang('books_create_success'), 'success');
                redirect(SITE_AREA . '/content/books/choose/' . $insert_id);
            } else {
                Template::set_message(lang('books_create_failure') . $this->books_model->error, 'error');
            }
        }
        Assets::add_module_js('books', 'books.js');

        Template::set('category', $this->books_model->get_cat_list());
        Template::set('toolbar_title', lang('books_manage'));
        Template::set();
        Template::render();
    }

    //--------------------------------------------------------------------

    /**
     * Allows editing of books data.
     *
     * @return void
     */
    public function edit()
    {
        $id = $this->uri->segment(5);

        if (empty($id)) {
            Template::set_message(lang('books_invalid_id'), 'error');
            redirect(SITE_AREA . '/content/books');
        }

        if (!$this->check_available($id, 2)) {
            Template::set_message(lang('restricted'), 'error');
            redirect(SITE_AREA . '/content/books');
        }

        if (isset($_POST['save'])) {
            $this->auth->restrict('Books.Content.Edit');

            if ($this->save_books('update', $id)) {
                // Log the activity
                log_activity($this->current_user->id, lang('books_act_edit_record') . ': ' . $id . ' : ' . $this->input->ip_address(), 'books');

                Template::set_message(lang('books_edit_success'), 'success');
                redirect(SITE_AREA . '/content/books/');
            } else {
                Template::set_message(lang('books_edit_failure') . $this->books_model->error, 'error');
            }
        } else if (isset($_POST['delete'])) {
            $this->auth->restrict('Books.Content.Delete');

            if ($this->books_model->delete($id)) {
                // Log the activity
                log_activity($this->current_user->id, lang('books_act_delete_record') . ': ' . $id . ' : ' . $this->input->ip_address(), 'books');

                Template::set_message(lang('books_delete_success'), 'success');

                redirect(SITE_AREA . '/content/books');
            } else {
                Template::set_message(lang('books_delete_failure') . $this->books_model->error, 'error');
            }
        }

        Template::set('category', $this->books_model->get_cat_list());
        Template::set('books', $this->books_model->find($id));
        Template::set('toolbar_title', lang('books_manage'));
        Template::render();
    }

    public function choose()
    {
        $id = $this->uri->segment(5);

        if (empty($id)) {
            Template::set_message(lang('books_invalid_id'), 'error');
            redirect(SITE_AREA . '/content/books');
        }

        if ($this->input->post("doc_type")){
            redirect(SITE_AREA . '/content/books/compose/' . $id . '/' .$this->input->post("doc_type"));
        }

        Template::set('toolbar_title', "Chọn kiểu tài liệu");
        Template::render();

    }

    public function compose()
    {
        $id = $this->uri->segment(5);
        $type = $this->uri->segment(6);
        if (empty($id)) {
            Template::set_message(lang('books_invalid_id'), 'error');
            redirect(SITE_AREA . '/content/books');
        }
        if (!$this->check_available($id, 7)) {
            Template::set_message(lang('restricted'), 'error');
            redirect(SITE_AREA . '/content/books');
        }
        Assets::add_js(Template::theme_url('js/tiny_mce/jquery.tinymce.js'));
        if ($this->input->post()) {
            if ($this->input->post('upload')) {
                $config['upload_path'] = 'assets/books/';
                $config['allowed_types'] = 'gif|jpg|png|pdf|doc';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);

                if ( ! $this->upload->do_upload())
                {
                    Template::set_message($this->upload->display_errors(), 'error');
                }
                else
                {
                    $data = $this->upload->data();
                    $update_data["filename"] = 'assets/books/'. $data["file_name"];
                    $update_data["file_type"] = str_replace(".", "", $data["file_ext"]);
                    $this->books_model->update($id, $update_data);
                    print $this->db->last_query();
                    Template::set_message("Tải tệp thành công.", "success");
                }

            } else {
                $content = $this->input->post('content', true);
                $filename = $this->books_model->get_filename($id);
                if ($filename)
                    file_put_contents($filename, $content);
                Template::set_message(lang('compose_success'), 'success');
            }
        }

        if (!isset($content)) {
            $content = $this->books_model->get_content($id);
        }

        Assets::add_js($this->load->view('content/js', null, true), 'inline');
        Template::set('book_content', $content);
        Template::set('book_type', $type);
        Template::set('toolbar_title', lang('books_manage'));
        Template::render();

    }

    public function download($id)
    {
        if (!$this->check_available($id)) {
            Template::set_message(lang('restricted'), 'error');
            redirect(SITE_AREA . '/content/books');
        }
        $filename = $this->books_model->get_filename($id);
        if (isset($id) && $filename) {

            error_reporting(E_ALL);
            define('CLI', (PHP_SAPI == 'cli') ? true : false);
            define('EOL', CLI ? PHP_EOL : '<br />');

            Autoloader::register();
            Settings::loadConfig();

            $writers = array('Word2007' => 'docx', 'ODText' => 'odt', 'RTF' => 'rtf');

            if (CLI) {
                return;
            }
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filename, 'HTML');
            if ($this->write($phpWord, $id, $writers)) {
                Template::set('writters', $writers);
                Template::set('id', $id);
            } else {
                Template::set_message(lang('books_download_failure'), 'error');
            }
            $books = $this->books_model->find($id);
            $book_info['Title'] = $books->title;
            $book_info['Author'] = $this->books_model->get_author($id);
            $book_info['Category Id'] = $this->books_model->get_cat($books->category_id);
            $book_info['description'] = $books->description;
            $book_info['Tag'] = $books->tag;
            Template::set('books', $book_info);
            Template::set('toolbar_title', lang('books_manage'));
            Template::render();
        } else {
            Template::set_message(lang('books_invalid_id'), 'error');
            redirect(SITE_AREA . '/content/books');
        }
    }


    public function read()
    {
        $id = $this->uri->segment(5);
        if (empty($id)) {
            Template::set_message(lang('books_invalid_id'), 'error');
            redirect(SITE_AREA . '/content/books');
        }
        if (!$this->check_available($id, 7)) {
            Template::set_message(lang('restricted'), 'error');
            redirect(SITE_AREA . '/content/books');
        }
        $this->load->library('user_agent');
        $author = $this->books_model->get_author($id);
        Template::set('doc', $this->books_model->get_content($id));
        Template::set('books', $this->books_model->find($id));
        Template::set('author', $author);
        Template::set('url_back', $this->agent->referrer() ? $this->agent->referrer() : SITE_AREA . '/content/books/');
        Template::set('id', $id);
        Template::set('toolbar_title', lang('books_manage'));
        Template::render();
    }
    //--------------------------------------------------------------------
    //--------------------------------------------------------------------
    // !PRIVATE METHODS
    //--------------------------------------------------------------------

    /**
     * Write documents
     *
     * @param \PhpOffice\PhpWord\PhpWord $phpWord
     * @param string $filename
     * @param array $writers
     * @return bool
     */
    private function write($phpWord, $filename, $writers)
    {
        $result = '';

        // Write documents
        foreach ($writers as $writer => $extension) {
            $result .= date('H:i:s') . " Write to {$writer} format";
            if (!is_null($extension)) {
                $xmlWriter = IOFactory::createWriter($phpWord, $writer);
                $xmlWriter->save("assets/books/{$filename}.{$extension}");
            }
        }

        return true;
    }

    private function check_available($id, $permission = 1)
    {
        $this->load->library('users/auth');
        if ($this->auth->is_logged_in()) {
            if ($this->books_model->is_published($id)) {
                return true;
            } else {
                if ($this->books_model->check_permission($this->session->userdata('user_id'), $id, $permission)) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Summary
     *
     * @param String $type Either "insert" or "update"
     * @param Int $id The ID of the record to update, ignored on inserts
     *
     * @return Mixed    An INT id for successful inserts, TRUE for successful updates, else FALSE
     */
    private function save_books($type = 'insert', $id = 0)
    {
        if ($type == 'update') {
            $_POST['book_id'] = $id;
        }

        // make sure we only pass in the fields we want

        $data = array();
        $data['category_id'] = $this->input->post('books_category_id');
        $data['description'] = $this->input->post('description');
        $data['title'] = $this->input->post('books_title');
        $data['filename'] = 'assets/books/' . md5($this->input->post('books_title') . time()) . '.html';
        $data['published'] = $this->input->post('books_published');
        $data['tag'] = $this->input->post('books_tag');

        if ($type == 'insert') {
            $id = $this->books_model->insert($data);
            $this->books_model->insert_permission($this->auth->user_id(), $id, 7);
            file_put_contents($data['filename'], '');

            if (is_numeric($id)) {
                $return = $id;
            } else {
                $return = FALSE;
            }
        } elseif ($type == 'update') {
            $return = $this->books_model->update($id, $data);
        }

        return $return;
    }

    //--------------------------------------------------------------------

}