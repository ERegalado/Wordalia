<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Word_type_model extends CI_Model {  

  public function __construct()
  {
		// Call the CI_Model constructor
		parent::__construct();
  }

  /*--------------------------------------------------------------------------*/
  /*  add ==> Insert a word type	 	 										*/
  /*  $data : Array containing the info of the point							*/
  /*																			*/
  /*--------------------------------------------------------------------------*/
  function add($data){
	$this->db->insert('word_types',$data);
	return $this->db->insert_id();
  }
  
  /*--------------------------------------------------------------------------*/
  /*  update ==> Updates a word		 	 										*/
  /*  $data : Array containing the info of the word							*/
  /*																			*/
  /*--------------------------------------------------------------------------*/
  function update($wordTypeId,$data){
	$this->db->where(array('word_type_id' => $wordTypeId));
	$this->db->update('word_types',$data);
	return $this->db->insert_id();
  }


  
  /*-----------------------------------------------------------------------------*/
  /*  getWord ==> Gets the word matching the parameter							 */
  /*  $word : String containing the word							 			 */
  /*-----------------------------------------------------------------------------*/
  function getWordTypes(){
	return $this->db->get_where('word_types', array('is_active' => 1 ))->result_array();
  }
  
}
