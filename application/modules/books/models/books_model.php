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
        $this->db->where($where_query);
        if ($offset && $limit)
            $this->db->limit($limit, $offset);
        $result = $this->db->order_by('b.rate', 'DESC')->get();
        //echo $this->db->last_query(); die;
        if ($result->num_rows() < 1)
            return false;
        else
            return $result->result();
    }

    public function increase_rate($book_id, $points){
        $query = "UPDATE bf_books SET rate = rate + {$points} WHERE book_id = {$book_id}";
        return $this->db->query($query);
    }

    public function clone_content($dest_book, $source_book)
    {
        $source_content = $this->db->select("content")
            ->where('book_id', $source_book)
            ->get($this->table_name);

        $dest_content = $this->db->select("content")
            ->where('book_id', $dest_book)
            ->get($this->table_name);

        $content_new = $dest_content->row("content") . "|" . $source_content->row("content");
        $content_new_array = explode("|", $content_new);
        $content_standardized = implode("|", array_unique($content_new_array));

        $this->increase_rate($source_book, 5);

        $this->db->update($this->table_name, array('content' => $content_standardized), array('book_id' => $dest_book));

        return true;
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
        return $this->standardize_array($cat_list->result(), 'category_id', 'category_name');
    }

    public function standardize_array($array, $key, $value)
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

    public function get_content_file($content_id)
    {
        $result = $this->db->select("*")
            ->where("id", $content_id)
            ->get("content");

        if ($result->num_rows() < 1)
            return false;

        $result = $result->result_array()[0];

        if ($result["file_type"] == "html")
            $result["content"] = file_get_contents($result["filename"]);
        else
            $result["content"] = "";

        return $result;
    }

    public function insert_permission($user_id, $book_id, $permission)
    {
        $this->db->insert('book_user', array('user_id' => $user_id, 'book_id' => $book_id, 'permission' => $permission));
    }

    public function get_filename($id)
    {
        $result = $this->db->select('filename, file_type')
            ->where('id', $id)
            ->get('content');

        if ($result->num_rows() < 1)
            return false;
        else {
            return $result->result();
        }

    }

    public function add_content_to_book($book_id, $content_id)
    {
        $result = $this->db->select("content")
            ->where('book_id', $book_id)
            ->get($this->table_name);

        $content_list = $result->row('content');
        $data['content'] = $content_list . '|' . $content_id;

        $this->db->update('books', $data, array('book_id' => $book_id));

        return true;
    }

    public function put_content($data)
    {
        $data["created_on"] = date("Y-m-d H:i:s");
        if ($this->db->insert('content', $data)) {
            return $this->db->insert_id();
        }

    }

    public function get_content_order($id)
    {
        $result = $this->db->select("content")
            ->where('book_id', $id)
            ->get($this->table_name);

        if ($result->num_rows() < 1)
            return false;

        return explode("|", $result->row('content'));

    }

    public function get_content($id)
    {
        $list_content = $this->get_content_order($id);
        $content = $this->db->select("*")
            ->where_in("id", $list_content)
            ->get("content");

        if ($content->num_rows() < 1)
            return false;
        $content = $content->result_array();

        foreach ($list_content as $content_id) {
            foreach ($content as $item) {
                if ($item["id"] == $content_id) {
                    $new_content[] = $item;
                    break;
                }
            }
        }

        return $new_content;

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

    public function remove_content($book_id, $content_id){
        $old_content = explode("|",$this->get_book_content($book_id));
        if(($key = array_search($content_id, $old_content)) !== false) {
            unset($old_content[$key]);
        }
        $new_content = implode("|", $old_content);

        $this->db->update($this->table_name, array('content' => $new_content), array('book_id' => $book_id));
        return true;
    }

    public function get_book_content($book_id){
        $result = $this->db->select("content")
            ->where('book_id', $book_id)
            ->get($this->table_name);

        return $result->row('content');
    }

    public function update_content($content_id, $update_data){
        $filename = $this->get_filename($content_id);
        $realname = $filename[0]->filename;

        file_put_contents($realname, $update_data["content"]);

        unset($update_data["content"]);

        return $this->db->update("content", $update_data, array("id" => $content_id));
    }
}
