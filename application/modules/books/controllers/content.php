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
                redirect(SITE_AREA . '/content/books/compose/' . $insert_id);
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

        $condition = $like = array();
        $condition['book_id !='] = $id;

        $st = false;

        if ($this->input->post("st")) {
            if ($this->input->post('category_id')) {
                $condition['category_id'] = $this->input->post('category_id');
            }
            if ($this->input->post('title')) {
                $like['title'] = $this->input->post('title');
            }
            $st = true;
        }

        $records = $this->books_model->search_books($this->current_user->id, 1, null, null, $condition, $like);

        Template::load_view("st",
            array(
                "records" => $records,
                'category' => $this->books_model->get_cat_list(),
                'books' => $this->input->post()),
            "", false, $search);

        if (empty($id)) {
            Template::set_message(lang('books_invalid_id'), 'error');
            redirect(SITE_AREA . '/content/books');
        }

        if ($this->input->post("save")) {
            if ($this->input->post("got_book")) {
                $this->books_model->clone_content($id, $this->input->post("got_book"));
                redirect(SITE_AREA . '/content/books/compose/' . $id);
            } else {
                $content_id = $this->create_new_content();
                $this->books_model->add_content_to_book($id, $content_id);
                redirect(SITE_AREA . '/content/books/compose/' . $id . '/' . $content_id);
            }
        }

        Assets::add_js($this->load->view('content/meo_con_js', array("st" => $st), true), 'inline');
        Template::set('toolbar_title', "Chọn kiểu tài liệu");
        Template::set('search', $search);
        Template::set('st', $st);
        Template::render();

    }

    public function compose()
    {
        $this->load->helper('book');
        $id = $this->uri->segment(5);
        if (empty($id)) {
            Template::set_message(lang('books_invalid_id'), 'error');
            redirect(SITE_AREA . '/content/books');
        }
        if (!$this->check_available($id, 7)) {
            Template::set_message(lang('restricted'), 'error');
            redirect(SITE_AREA . '/content/books');
        }

        if ($this->input->post()) {
            if ($this->input->post("save")) {
                $content_id = $this->uri->segment(6);
                $update_data["header"] = $this->input->post("header");
                $update_data["content"] = htmlspecialchars($this->input->post("content"));

                $this->books_model->update_content($content_id, $update_data);
            } elseif ($this->input->post('upload')) {
                $config['upload_path'] = 'assets/books/';
                $config['allowed_types'] = 'gif|jpg|png|pdf|doc';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload()) {
                    Template::set_message($this->upload->display_errors(), 'error');
                } else {
                    $data = $this->upload->data();
                    $update_data["filename"] = 'assets/books/' . $data["file_name"];
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

        $content_id = $this->uri->segment(6) ? $this->uri->segment(6) : $content[0]["id"];
        $file_content = $this->books_model->get_content_file($content_id);

        $url = site_url(SITE_AREA . '/content/books/update_content_order/' . $id);

        Assets::add_js(Template::theme_url('js/tiny_mce/jquery.tinymce.js'));
        Assets::add_js(Template::theme_url('js/tiny_mce/tiny_mce.js'));
        Assets::add_js($this->load->view('content/update_header_list_js', array("url" => $url), true), 'inline');
        Assets::add_module_js('books', 'pdf_reader.js');
        Assets::add_module_js('books', 'init_reader.js');
        Assets::add_module_js('books', 'jquery.cookie.js');
        Assets::add_module_css('books', 'books.css');
        Template::set("book_id", $id);
        Template::set('book_content', $content);
        Template::set('selected_content', $content_id);
        Template::set('toolbar_title', lang('books_manage'));
        Template::set('file_content', $file_content);
        Template::render();

    }

    public function download($id)
    {
        if (!$this->check_available($id)) {
            Template::set_message(lang('restricted'), 'error');
            redirect(SITE_AREA . '/content/books');
        }

        # Increase rate by 1
        $this->vote($id, 3);

        $filename = $this->books_model->get_content($id);
        $this->zip_file_and_download($filename);
    }

    private function zip_file_and_download($file_names)
    {
        $archive_file_name = uniqid()."_connect_data.zip";
        $zip = new ZipArchive();
        //create the file and throw the error if unsuccessful
        if ($zip->open($archive_file_name, ZIPARCHIVE::CREATE) !== TRUE) {
            exit("cannot open <$archive_file_name>\n");
        }
        //add each files of $file_name array to archive
        foreach ($file_names as $key => $files) {
            $zip->addFile($files["filename"], "{$key}. {$files["header"]}.{$files["file_type"]}");
            //echo $file_path.$files,$files."

        }
        $zip->close();
        //then send the headers to force download the zip file
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=$archive_file_name");
        header("Content-Transfer-Encoding: binary");
        ob_end_flush();
        @readfile($archive_file_name);
        exit;
    }

    public function read()
    {
        $this->load->helper('book');
        $id = $this->uri->segment(5);
        if (empty($id)) {
            Template::set_message(lang('books_invalid_id'), 'error');
            redirect(SITE_AREA . '/content/books');
        }
        if (!$this->check_available($id, 7)) {
            Template::set_message(lang('restricted'), 'error');
            redirect(SITE_AREA . '/content/books');
        }

        # Increase rate by 1
        $this->vote($id, 1);

        if ($this->input->post()) {
            if ($this->input->post("save")) {
                $content_id = $this->uri->segment(6);
                $update_data["header"] = $this->input->post("header");
                $update_data["content"] = htmlspecialchars($this->input->post("content"));

                $this->books_model->update_content($content_id, $update_data);
            } elseif ($this->input->post('upload')) {
                $config['upload_path'] = 'assets/books/';
                $config['allowed_types'] = 'gif|jpg|png|pdf|doc';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload()) {
                    Template::set_message($this->upload->display_errors(), 'error');
                } else {
                    $data = $this->upload->data();
                    $update_data["filename"] = 'assets/books/' . $data["file_name"];
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

        $content_id = $this->uri->segment(6) ? $this->uri->segment(6) : $content[0]["id"];
        $file_content = $this->books_model->get_content_file($content_id);

        Assets::add_js(Template::theme_url('js/tiny_mce/jquery.tinymce.js'));
        Assets::add_js(Template::theme_url('js/tiny_mce/tiny_mce.js'));
        Assets::add_module_js('books', 'pdf_reader.js');
        Assets::add_module_js('books', 'init_reader.js');
        Assets::add_module_js('books', 'jquery.cookie.js');
        Assets::add_module_css('books', 'books.css');
        Template::set("book_id", $id);
        Template::set('book_content', $content);
        Template::set('selected_content', $content_id);
        Template::set('toolbar_title', lang('books_manage'));
        Template::set('file_content', $file_content);
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

    private function create_new_content()
    {
        $update_data["owner_id"] = $this->auth->user_id();
        if ($this->input->post()) {
            if ($this->input->post('doc_type') == "file") {
                $config['upload_path'] = 'assets/books/';
                $config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|aif|aiff|aac|au|bmp|gsm|mov|mid|midi|mpg|mpeg|mp4|m4a|psd|qt|qtif|qif|qti|snd|tif|tiff|wav|3g2|3pg|flv|mp3|swf|asx|asf|avi|ra|ram|rm|rpm|rv|smi|smil|xaml|doc|docx|ppt|pptx|xls|xlsx';
                $config['encrypt_name'] = true;

                $this->load->library('upload', $config);

                if (!$this->upload->do_upload()) {
                    Template::set_message($this->upload->display_errors(), 'error');
                } else {
                    $data = $this->upload->data();
                    $filename = 'assets/books/' . $data['file_name'];
                    $update_data["filename"] = $filename;
                    $update_data["header"] = $this->input->post('header');
                    $update_data["file_type"] = str_replace(".", "", $data["file_ext"]);
                    Template::set_message("Tải tệp thành công.", "success");
                    return $this->books_model->put_content($update_data);
                }

            } else {
                $content = $this->input->post('content', true);
                $filename = 'assets/books/' . md5($this->input->post('header') . time()) . '.html';
                if ($filename)
                    file_put_contents($filename, $content);
                $update_data["filename"] = $filename;
                $update_data["file_type"] = 'html';
                $update_data["header"] = $this->input->post('header');
                Template::set_message(lang('compose_success'), 'success');
                return $this->books_model->put_content($update_data);
            }
        }
    }

    public function remove_content()
    {
        $book_id = $this->uri->segment(5);
        $content_id = $this->uri->segment(6);

        if (!$book_id or !$content_id)
            Template::set_message("ID không hợp lệ", "error");

        $this->books_model->remove_content($book_id, $content_id);
        Template::set_message("Xóa nội dung thành công", "success");

        redirect(SITE_AREA . '/content/books/compose/' . $book_id);
    }

    public function update_content_order()
    {
        $book_id = $this->uri->segment(5);

        if (!$book_id)
            Template::set_message("ID không hợp lệ", "error");

        if ($this->input->post('neworder')) {
            $this->books_model->update($book_id, array("content" => $this->input->post('neworder')));
        }

        echo json_encode($this->input->post('neworder'));
    }

    private function vote($book_id, $points){
        $this->books_model->increase_rate($book_id, $points);

        return true;
    }

    public function vote_up(){
        $book_id = $this->uri->segment(5);
        $points = $this->uri->segment(6);

        $this->vote($book_id, $points);

        Template::set_message("Vote thành công");
    }

}