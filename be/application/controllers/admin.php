<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	/*--------------------------------------------------------------------------*/
	/*  __construct ==> Call the Model constructor 								*/
	/*																			*/
	/*--------------------------------------------------------------------------*/
	/*
	function __construct(){
		parent::__construct(); 
		$realm = "Restricted area";

		//user => password
		$users = array('admin' => 'mypass', 'guest' => 'guest');


		if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
		    header('HTTP/1.1 401 Unauthorized');
		    header('WWW-Authenticate: Digest realm="'.$realm.
		           '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');

		    die('<h1>Forbidden</h1>');
		}


		// analyze the PHP_AUTH_DIGEST variable
		if (!($data = $this->_http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) ||
		    !isset($users[$data['username']]))
		    die('Wrong Credentials!');


		// generate the valid response
		$A1 = md5($data['username'] . ':' . $realm . ':' . $users[$data['username']]);
		$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
		$valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

		if ($data['response'] != $valid_response)
		    die('Wrong Credentials!');

		// ok, valid username & password
		echo 'You are logged in as: ' . $data['username']; 

	}
	
	// function to parse the http auth header
	function _http_digest_parse($txt)
	{
	    // protect against missing data
	    $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
	    $data = array();
	    $keys = implode('|', array_keys($needed_parts));

	    preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

	    foreach ($matches as $m) {
	        $data[$m[1]] = $m[3] ? $m[3] : $m[4];
	        unset($needed_parts[$m[1]]);
	    }

	    return $needed_parts ? false : $data;
	}*/
	
	function __construct(){
		parent::__construct();
		//if ($this->session->userdata('Credentials')!=Credentials || $this->session->userdata(Level)!=0) redirect('index.php/admin');
	}
	

	/*--------------------------------------------------------------------------**
	**  index ==> Initializes an user's session 								**
	**	$username => Username													**
	**	$password => User's password											**
	**																			**
	** 	NOTE: The params are received with POST									**
	**																			**
	**--------------------------------------------------------------------------*/
	public function index(){		
		if ($this->input->post()):
			$this->load->model('userModel');
			$user = $this->userModel->login($this->input->post('username'),md5($this->input->post('password')));
			var_dump($user);
			if (!empty($user)): //The info is correct!
				$this->session->set_userdata(idUser,$user[idUser]);
				$this->session->set_userdata(Level,$user[Level]);
				$this->session->set_userdata('Credentials',Credentials);
				//Redirect!
				redirect('index.php/admin/home');
			else:
				$data['error'] = 1;
			endif;
		endif;		
		$this->load->view('inner/sign_in');
	}
	
	/*--------------------------------------------------------------------------*/
	/*  logout ==> Terminates an user's session									*/	
	/*																			*/
	/*--------------------------------------------------------------------------*/
	public function logout(){
		$this->session->unset_userdata(idUser);
		$this->session->unset_userdata(Level);
		$this->session->unset_userdata(name);
		$this->session->sess_destroy();
		
		redirect(base_url());
	}
	
	public function home(){
		if ($this->session->userdata('Credentials')!=Credentials || $this->session->userdata(Level)!=0) redirect('index.php/admin');
		$this->load->model('word_model');
		$this->load->helper('js');
		$data = array(
			'mainView'	=> 'inner/words/main',
			'scripts'	=> jlist(),
			'words'		=>  $this->word_model->getWordsByLang()
		);
		
		$this->load->view('template/wrapper',$data);
	}
	
	public function csv()
	{
        if ($this->session->userdata('Credentials')!=Credentials || $this->session->userdata(Level)!=0) redirect('index.php/logout');
		//If POST then the user is trying to insert a new transfer
		if ($this->input->post()):						
			$config['upload_path'] = './resources/uploads/';
			$config['allowed_types'] = 'csv';
			$config['max_size']	= '10240';	//10MB max file		
			$this->load->library('upload', $config);
			$fileName = '';
			$modelName = '';
			//Common Values for all Loads
			$defaultValues = array(
				'is_active'		=> 1,
				'creation_date' => date('Y-m-d'),
				'created_by'	=> 0
			);
			//Determine which file is being uploaded
			if ($this->input->post('u1')):
				$fileName = 'mas_emps';
				$modelName = 'userModel';
				$csvConfig = array(
					'DUI'			=> 'CODE MAS',
					'name'			=> 'NOMBRE STAFF MAS',
					'position'		=> 'CARGO',
					'location_id'	=> 'CODE UBICACION',
					'username'		=> 'USUARIO',
					'password'		=> 'CLAVE',
					'level'			=> 'ROL'
				);
				// $defaultValues ['level']	= 2;
			elseif ($this->input->post('u2')):
				$fileName = 'locations';
				$modelName = 'locationModel';
				$csvConfig = array(
					'location_id'	=> 'CODE UBICACION',
					'location_name'	=> 'NOMBRE UBICACION'
				);
				$defaultValues ['current_qty']	= 0;
			elseif ($this->input->post('u3')): 
				$fileName = 'ilc_emps';
				$modelName = 'employeesModel';
				$csvConfig = array(
					'DUI'			=> 'CODE EMPLEADO',
					'employee_name'	=> 'NOMBRE EMPLEADO',
					'position'		=> 'CARGO',
					'location_id'	=> 'CODE UBICACION'					
				);
				$defaultValues ['balance']	= 0;
			endif;
			if ( ! $this->upload->do_upload($fileName))
			{
				$data['error'] = $this->upload->display_errors();
			}
			else
			{
				$this->load->library('csvreader');
				$fileInfo = $this->upload->data();
				$result =   $this->csvreader->parse_file($fileInfo['full_path']);
				if($this->_insert_csv($result,$modelName,$csvConfig,$defaultValues)):	
					$this->session->set_flashdata('msg', 'Carga Realizada exitosamente.');
					redirect('index.php/cargas');
				else: 
					$data['error'] = 'El archivo CSV contiene errores y no pudo ser cargado.';
				endif;				
			}
		endif;
		
		
        $data['mainView'] =  'admin/uploads';
        $this->load->view('template/wrapper', $data);  
	}
	
	function _insert_csv($csvResult, $modelName, $config,$defaultValues){
		$data = array();
		$i=0;
		$this->load->model($modelName);
		$this->db->trans_start();
		foreach($csvResult as $row):
			$dataRow = array();
			$rowKeys = array_keys($row);
			while($col_name = current($config)):				
				if (in_array($col_name,$rowKeys)) $dataRow[key($config)]= (key($config)=='password')?md5($row[$col_name]):$row[$col_name];
				next($config);
			endwhile;
			reset($config);
			//Merge Default values
			 $dataRow = array_merge($dataRow,$defaultValues);
			$data[$i++] = $dataRow;			
			$this->$modelName->add($dataRow);
		endforeach;
		$this->db->trans_complete();		
		if ($this->db->trans_status() === FALSE)
		{
			return 0;
		} 
		return 1;
	}
	
	/*
	public function index()
	{
        $this->load->library('csvreader');
        $result =   $this->csvreader->parse_file('./resources/uploads/test.csv');
		var_dump($result);
        $data['csvData'] =  $result;
        $data['mainView'] =  'admin/uploads';
        $this->load->view('template/wrapper', $data);  
	}*/
	
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