<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Csv extends CI_Controller {
	
	
	function __construct(){
		parent::__construct();
		if ($this->session->userdata('Credentials')!=Credentials || $this->session->userdata(Level)!=0) redirect('index.php/admin/logout');		
	}	
		
	public function words()
	{
        if ($this->session->userdata('Credentials')!=Credentials || $this->session->userdata(Level)!=0) redirect('index.php/admin/logout');
		//If POST then the user is trying to insert a new transfer
		if ($this->input->post()):						
			$config['upload_path'] = './res/uploads/';
			$config['allowed_types'] = 'csv';
			$config['max_size']	= '10240';	//10MB max file		
			$this->load->library('upload', $config);
			$fileName = 'wd_words';
			$modelName = '';
			if ( ! $this->upload->do_upload($fileName))
			{
				$data['error'] = $this->upload->display_errors();
			}
			else
			{
				$this->load->library('csvreader');
				$this->load->helper('curl');
				$this->load->helper('word_parser');
				$fileInfo = $this->upload->data();
				$result =   $this->csvreader->parse_file($fileInfo['full_path']);
				$wordsData = array();
				$i = 0;
				if (array_key_exists('WORD',$result[0])):
					foreach($result as $row):
						$wordsData[$i++] =  parseWord($row['WORD']);
					endforeach;
				endif;
				if (empty($wordsData)) $wordsData['0'] =  array('error'=>'Missing header "WORD"');
				// var_dump($wordsData);
				
				//Create a downloadable CSV --------------------------------------------------------------------------------
				//After the automatic retrieval of the information, it should always have a revision.
				$this->load->helper('download');
				$fp = fopen('php://output', 'w');

				$name = 'data.csv';
				// force_download($name, $data);
				header('Content-Description: File Transfer');
				header('Content-Type: application/csv');
				header('Content-Disposition: attachment; filename='.$name);
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				
				foreach ($wordsData as $fields) {
					fputcsv($fp, $fields);
				}

				$data = file_get_contents('php://output'); // Read the file's contents
				
				header('Content-Length: ' . filesize($data));
				fclose($fp);
				
				/*if($this->_insert_csv($result,$modelName,$csvConfig,$defaultValues)):	
					$this->session->set_flashdata('msg', 'Carga Realizada exitosamente.');
					redirect('index.php/cargas');
				else: 
					$data['error'] = 'El archivo CSV contiene errores y no pudo ser cargado.';
				endif;		*/		
			}
		else:
			$data['mainView'] =  'inner/csv/words';
			$this->load->view('template/wrapper', $data);  
		endif;
	}
	
	public function bulk_load()
	{
        if ($this->session->userdata('Credentials')!=Credentials || $this->session->userdata(Level)!=0) redirect('index.php/admin/logout');
		//If POST then the user is trying to insert a new transfer
		if ($this->input->post()):						
			$config['upload_path'] = './res/uploads/';
			$config['allowed_types'] = 'csv';
			$config['max_size']	= '10240';	//10MB max file		
			$this->load->library('upload', $config);
			$fileName = 'wd_load';
			if ( ! $this->upload->do_upload($fileName))
			{
				$data['error'] = $this->upload->display_errors();
			}
			else
			{				
				$fileInfo = $this->upload->data();
				$this->load->library('parseCSV');
				$csv = new parseCSV();
				$csv->auto($fileInfo['full_path']);
				if($this->_insert_csv($csv)):	
					$this->session->set_flashdata('msg', 'Successfully loaded the words to the DB.');
					//redirect('index.php/csv/bulk_load');
				else: 
					$data['error'] = 'The file contains errors. Please verify that it contains the headers: WORD,DEFINITION,WORD_TYPE_ID,EXAMPLES,WORD_TRANSLATION';
				endif;
			}
		endif;
		
		
        $data['mainView'] =  'inner/csv/bulk_load';
        $this->load->view('template/wrapper', $data);  
	}
	
	function _insert_csv($csv){
		$data = array();
		$i=0;
		$this->load->model('word_model');
		$this->load->model('word_type_model');
		$wTypes = $this->word_type_model->getWordTypes();
		$wordTypes = array();
		foreach($wTypes as $wType){
			$wordTypes[$wType['WORD_TYPE_ID']] = strtoupper($wType['WORD_TYPE']);
		}
		if (in_array('WORD_TYPE_ID',$csv->titles) and in_array('WORD',$csv->titles) and in_array('DEFINITION',$csv->titles) and in_array('EXAMPLES',$csv->titles) and in_array('WORD_TRANSLATION',$csv->titles)):
			$this->db->trans_start();
			foreach($csv->data as $row):
				// echo 'type id '.$row['WORD_TYPE_ID'].' array'.array_search(strtoupper($row['WORD_TYPE_ID']),$wordTypes);
				$wordInDB = $this->word_model->getWord($row['WORD']);
				if (empty($wordInDB)):
					//Insert the word
					$dataRowWord = array(
						'WORD_TYPE_ID'		=> array_search(strtoupper($row['WORD_TYPE_ID']),$wordTypes), 
						'LANGUAGE_ID'		=> 1, //Default to english
						'WORD'				=> $row['WORD'],
						'DEFINITION'		=> $row['DEFINITION'],
						'EXAMPLES'			=> $row['EXAMPLES'],
						'DATE_PUBLISHED'	=> NULL, //Make sure the date published is null
						'CREATED_BY'		=> $this->session->userdata(idUser),
						'CREATION_DATE'		=> date('Y-m-d'),
						'IS_ACTIVE'			=> 1 //Default to active
					);
					$wordId = $this->word_model->add($dataRowWord);
					//Insert the translation
					$dataRowTrans = array(
						'WORD_ID'			=> $wordId, //Must be set after the word is inserted
						'LANG_ID_TO'		=> 2, //Default to spanish
						'WORD_TRANSLATION'	=> $row['WORD_TRANSLATION']
					);
					$this->word_model->addTranslation($dataRowTrans);
				endif;				
			endforeach;
			$this->db->trans_complete();
			
			if ($this->db->trans_status() === FALSE)
			{
				return 0;
			} 
			return 1;
		else:		
			return 0;
		endif;
	}
	
	public function dd()
	{
        $this->load->helper('download');
		$this->load->model('locationModel');
		$this->load->model('userModel');
		// $list = $this->locationModel->getLocations();
		$list = $this->userModel->getMASEmployees();
		$fp = fopen('php://output', 'w');

		foreach ($list as $fields) {
			fputcsv($fp, $fields);
		}

		$data = file_get_contents('php://output'); // Read the file's contents
		$name = 'data.csv';
		// force_download($name, $data);
		header('Content-Description: File Transfer');
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename='.$name);
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($data));
		fclose($fp);
	}
	
	
}        