<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Collections_model extends BF_Model {

	protected $table_name	= "collections";
	protected $key			= "collection_id";
	protected $soft_deletes	= false;
	protected $date_format	= "datetime";

	protected $log_user 	= FALSE;

	protected $set_created	= false;
	protected $set_modified = false;

	/*
		Customize the operations of the model without recreating the insert, update,
		etc methods by adding the method names to act as callbacks here.
	 */
	protected $before_insert 	= array();
	protected $after_insert 	= array();
	protected $before_update 	= array();
	protected $after_update 	= array();
	protected $before_find 		= array();
	protected $after_find 		= array();
	protected $before_delete 	= array();
	protected $after_delete 	= array();

	/*
		For performance reasons, you may require your model to NOT return the
		id of the last inserted row as it is a bit of a slow method. This is
		primarily helpful when running big loops over data.
	 */
	protected $return_insert_id 	= TRUE;

	// The default type of element data is returned as.
	protected $return_type 			= "object";

	// Items that are always removed from data arrays prior to
	// any inserts or updates.
	protected $protected_attributes = array();

	/*
		You may need to move certain rules (like required) into the
		$insert_validation_rules array and out of the standard validation array.
		That way it is only required during inserts, not updates which may only
		be updating a portion of the data.
	 */
	protected $validation_rules 		= array(
		array(
			"field"		=> "collections_collection_name",
			"label"		=> "Collection Name",
			"rules"		=> "max_length[50]"
		),
		array(
			"field"		=> "collections_publish",
			"label"		=> "Publish",
			"rules"		=> "max_length[11]"
		),
	);
	protected $insert_validation_rules 	= array();
	protected $skip_validation 			= FALSE;

	//--------------------------------------------------------------------

    public function find_book($id){
        $query = "SELECT b.book_id, b.title FROM bf_books AS b WHERE b.book_id IN (SELECT book_id FROM bf_collection_book WHERE collection_id = {$id})";

        $book_list = $this->db->query($query);

        if($book_list->num_rows() < 1){
            return false;
        }
        return $book_list->result();
    }

    public function find_my_collections($user){
        $collections = $this->db->select('collection_id, collection_name')
            ->where('collection_owner', $user)
            ->get('collections');

        if($collections->num_rows() < 1)
            return;
        else
            return $collections->result();
    }

    public function add_collections($book_id, $colleciton_id){
        if($this->existed_collection($book_id, $colleciton_id)){
            return true;
        } else {
            $data['book_id'] = $book_id;
            $data['collection_id'] = $colleciton_id;
            
            return $this->db->insert('collection_book', $data);
        }
    }

    public function existed_collection($book_id, $collection_id){
        $select = $this->db->select('*')
            ->where('book_id', $book_id)
            ->where('collection_id', $collection_id)
            ->get('collection_book');

        if($select->num_rows() < 1)
            return false;
        else return true;
    }

    public function saveBook($book, $collection_id){
        $this->deleteBook($collection_id);

        foreach($book as $book_id){
            $data_batch[] = array(
                'book_id' => $book_id,
                'collection_id' => $collection_id
            );

        }

        return $this->db->insert_batch('collection_book', $data_batch);
    }

    public function deleteBook($collection_id){
        return $this->db->delete('collection_book', array('collection_id' => $collection_id));
    }

    public function is_publish($id){
        $query = $this->db->select('publish')
            ->where('collection_id', $id)
            ->get('collections');

        if($query->num_rows() < 1)
            return false;
        return $query->row('publish') == 1;
    }
}
