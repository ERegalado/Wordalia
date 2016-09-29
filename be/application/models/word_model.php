<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Word_model extends CI_Model {  

  public function __construct()
  {
		// Call the CI_Model constructor
		parent::__construct();
  }

  /*--------------------------------------------------------------------------*/
  /*  add ==> Insert a word		 	 										*/
  /*  $data : Array containing the info of the point							*/
  /*																			*/
  /*--------------------------------------------------------------------------*/
  function add($data){
	$this->db->insert('words',$data);
	return $this->db->insert_id();
  }
  
  /*--------------------------------------------------------------------------*/
  /*  update ==> Updates a word		 	 										*/
  /*  $data : Array containing the info of the word							*/
  /*																			*/
  /*--------------------------------------------------------------------------*/
  function update($wordId,$data){
	$this->db->where(array('word_id' => $wordId));
	$this->db->update('words',$data);
	return $this->db->insert_id();
  }

  /*--------------------------------------------------------------------------*/
  /*  getByDate ==> Gets All the data registered		 	 										*/
  /*  $data : Array containing the info of the point							*/
  /*																			*/
  /*--------------------------------------------------------------------------*/
  function getByDate($date){
	  // $sql = "select w.word,w.definition,w.examples, tr.WORD_TRANSLATION from wd_words w
		// LEFT JOIN wd_translations tr on w.word_id = tr.word_id
		// where tr.lang_id_to = 2 LIMIT 1";
		// return $this->db->get_where('words', array('date_published' => $date, 'is_active' => 1 ))->row_array();		
	$this->db->select('wd_words.word_id,word, definition,examples,word_translation');
	$this->db->join('wd_translations', 'wd_translations.word_id = wd_words.word_id', 'left');
	$this->db->where(array('date_published' => $date, 'is_active' => 1,'lang_id_to' => 2 ));	
	return $this->db->get('words')->row_array();
  }
  
  /*-----------------------------------------------------------------------------*/
  /*  getFirstUnpublishedWord ==> Gets the first word with date_published = null */
  /*  $data : Array containing the info of the point							 */
  /*  @Deprecated																			 */
  /*-----------------------------------------------------------------------------*/
  function getFirstUnpublishedWord(){	
	return $this->db->get_where('words', array('date_published' => null, 'is_active' => 1 ))->row_array();
  }
  
  
  
}
