<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Books_model extends BF_Model
{

    protected $table_name = "books";
    protected $key = "book_id";
    protected $soft_deletes = false;
    protected $date_format = "datetime";
    protected $log_user = FALSE;
    protected $set_created = true;
    protected $set_modified = true;
    protected $created_field = "created_on";
    protected $modified_field = "modified_on";

    /*
      Customize the operations of the model without recreating the insert, update,
      etc methods by adding the method names to act as callbacks here.
     */
    protected $before_insert = array();
    protected $after_insert = array();
    protected $before_update = array();
    protected $after_update = array();
    protected $before_find = array();
    protected $after_find = array();
    protected $before_delete = array();
    protected $after_delete = array();

    /*
      For performance reasons, you may require your model to NOT return the
      id of the last inserted row as it is a bit of a slow method. This is
      primarily helpful when running big loops over data.
     */
    protected $return_insert_id = TRUE;
    // The default type of element data is returned as.
    protected $return_type = "object";
    // Items that are always removed from data arrays prior to
    // any inserts or updates.
    protected $protected_attributes = array();

    /*
      You may need to move certain rules (like required) into the
      $insert_validation_rules array and out of the standard validation array.
      That way it is only required during inserts, not updates which may only
      be updating a portion of the data.
     */
    protected $validation_rules = array();
    protected $insert_validation_rules = array(
        array(
            "field" => "books_category_id",
            "label" => "Category Id",
            "rules" => "required|max_length[11]"
        ),
        array(
            "field" => "books_title",
            "label" => "Title",
            "rules" => "required|max_length[50]"
        ),
        array(
            "field" => "books_tag",
            "label" => "Tag",
            "rules" => "max_length[150]"
        ),
    );
    protected $skip_validation = FALSE;

    //--------------------------------------------------------------------

    public function delete_book_user($pid)
    {
        $result = $this->db->select('*')
            ->from('book_user')
            ->where('book_id', $pid)
            ->delete();
        return $result;
    }

    public function find_books($user_id, $permission, $offset, $limit)
    {
        $query = "select b.*, cat.category_name from bf_books as b 
                join bf_categories as cat on cat.category_id = b.category_id
                where b.book_id in (select book_id from bf_book_user as bu where bu.user_id = " . $user_id . " and bu.permission = " . $permission . "
                ) limit {$offset}, {$limit}";
        $result = $this->db->query($query);
        if ($result->num_rows() < 1)
            return;
        return $result->result();
    }

    public function search_books($user_id, $permission, $offset, $limit, $condition, $like)
    {
        $where_query = "(b.book_id in (select book_id from bf_book_user as bu where bu.user_id = {$user_id} and bu.permission >= {$permission})
        or b.published = 'Y')";
        $this->db->select('b.*')
            ->from('books as b');
        if (isset($condition) && !empty($condition))
            $this->db->where($condition);
        if (isset($like) && !empty($like))
            $this->db->like($like);
        $result = $this->db->where($where_query)
            ->limit($limit, $offset)
            ->order_by('b.created_on', 'DESC')
            ->get();
        //echo $this->db->last_query(); die;
        if ($result->num_rows() < 1)
            return;
        else
            return $result->result();
    }

    public function count_all()
    {
        $result = $this->db->select('count(*) as total')
            ->get($this->table_name);

        if ($result->num_rows() < 1)
            return 0;
        else
            return $result->row('total');
    }

    public function get_cat_list()
    {
        $cat_list = $this->db->select('*')
            ->get('categories');

        if ($cat_list->num_rows() < 1)
            return;
        return $this->standardlize_array($cat_list->result(), 'category_id', 'category_name');
    }

    public function standardlize_array($array, $key, $value)
    {
        if (isset($array) && is_array($array) && count($array) > 0) {
            if ($key == null || $value == null) {
                foreach ($array as $element) {
                    $new_array[] = $element;
                }
            } else {
                foreach ($array as $element) {
                    $new_array[$element->$key] = $element->$value;
                }
            }
            return $new_array;
        } else {
            return false;
        }
    }

    public function insert_permission($user_id, $book_id, $permission)
    {
        $this->db->insert('book_user', array('user_id' => $user_id, 'book_id' => $book_id, 'permission' => $permission));
    }

    public function get_filename($id)
    {
        $result = $this->db->select('filename, file_type')
            ->where('book_id', $id)
            ->get($this->table_name);

        if ($result->num_rows() < 1)
            return false;
        else{
            return $result->result();
        }

    }

    public function get_content($id)
    {
        $fileinfo = $this->get_filename($id);
        if ($fileinfo[0]->file_type == "html")
            return file_get_contents($fileinfo[0]->filename);
        else
            return $fileinfo[0];
    }

    public function get_author($id)
    {
        $result = $this->db->select('username')
            ->from('users as u')
            ->join('book_user as bu', 'bu.user_id = u.id')
            ->where('bu.book_id', $id)
            ->get();
        if ($result->num_rows() < 1)
            return '';
        else
            return $result->row('username');
    }

    public function get_cat($cat_id)
    {
        $result = $this->db->select('category_name')
            ->where('category_id', $cat_id)
            ->get('categories');
        if ($result->num_rows() < 1)
            return;
        else return $result->row('category_name');
    }

    public function is_published($id)
    {
        $result = $this->db->select('published')
            ->where('book_id', $id)
            ->get($this->table_name);
        if ($result->num_rows() < 1)
            return false;
        else {
            if ($result->row('published') == 'Y') {
                return true;
            } else {
                return false;
            }
        }
    }

    public function check_permission($user_id, $book_id, $permission = 1)
    {
        $result = $this->db->select('permission')
            ->where('user_id', $user_id)
            ->where('book_id', $book_id)
            ->get('book_user');
        if ($result->num_rows() < 1)
            return false;
        else {
            if ($result->row('permission') >= $permission) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function get_permission($user_id, $book_id)
    {
        $result = $this->db->select('permission')
            ->where('user_id', $user_id)
            ->where('book_id', $book_id)
            ->get('book_user');
        if ($result->num_rows() < 1)
            return 0;
        else {
            return $result->row('permission');
        }
    }
}
