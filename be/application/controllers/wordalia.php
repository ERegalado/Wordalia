<?php defined('BASEPATH') OR exit('No direct script access allowed');
//Local URL: http://localhost/wordalia/be/index.php/wordalia/word_get
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Wordalia extends REST_Controller
{
	function __construct()
    {
        // Construct our parent class
        parent::__construct();
        
        // Configure limits on our controller methods. Ensure
        // you have created the 'limits' table and enabled 'limits'
        // within application/config/rest.php
        $this->methods['user_get']['limit'] = 500; //500 requests per hour per user/key
        $this->methods['user_post']['limit'] = 100; //100 requests per hour per user/key
        $this->methods['user_delete']['limit'] = 50; //50 requests per hour per user/key
		//Load the word model
		$this->load->model('word_model');
    }
	
		
	 function index(){echo "Current time is: ".date('Y-m-d H:i:s');}
    
	    
    function word_get()
    {		
		$date = (!$this->get('pDate'))?date('Y-m-d'):$this->get('pDate');       		
        $word = $this->word_model->getByDate($date);
    	
        if($word){
			$this->response(['word' => $word, 'gPDate' => $this->get('pDate') /*,'query' => $this->db->last_query() */], 200); // 200 being the HTTP response code			
        }else{
			$this->response(array('error' => 'Word not found' /*,'query' => $this->db->last_query() */), 404); //OK, At least we tried it.
		}
    }
	
	function new_word_post(){ //New word gets a new word from the database and publish it to the website and to facebook
        $word = $this->word_model->getByDate($date = date('Y-m-d H:i:s'));
		if (!isset($word)){ //Continue only if today's word is not set
			//1. Get the first word randomly (non-deterministic)
			$word = $this->word_model->getByDate(null);
			if ($word){
				//2. Update the word in the DB
				$data = array('date_published' => $date,'modified_by' => 'wd_job', 'modification_date' => $date);
				$this->word_model->update($word['word_id'],$data); //CASE SENSITIVE
				//3. Create the image
				$this->_createWordImage($word);
				//4. Schedule FB post
				$this->_postToFB($word['word']); //Here the word gets posted to facebook
				//5. Return the response
				$this->response(['success' => 1], 200);
			}else{$this->response(['success' => 0], 200);}
		}else{$this->response(['success' => 0], 200);}
	}
	
	function _createWordImage($word){
		header('Content-Type: image/jpeg');

		$img = $this->_LoadJpeg('res/imgs/templates/template-'.date('m').'.jpg');

		$text_color = imagecolorallocate($img, 255, 255, 255);
		$colorGray = imagecolorallocate($img, 185, 185, 185);
		$wdTypeColor = imagecolorallocate($img, 204, 235, 255);
		$font = 'res/fonts/BebasNeue Regular.otf';
		$font2 = 'res/fonts/arial.ttf';
		$ariali = 'res/fonts/ariali.ttf';
		define('MAX_CHAR_PER_LINE',73);
		define('MAX_CHAR_PER_LINE_EX',64);
		$titleSize = 72;
		$wdTypeSize = 16;
		$textSize = 24;		
		$exOffset = 22;
		$trOffset = 50;
		$titlePos = array('x' => 84, 'y' => 160);
		$wdTypePos = array('x' => 84, 'y' => 260 );
		$defPos = array('x' => 84, 'y' => 268);		
		$exPos = array('x' => 220, 'y' => $defPos['y'] +$exOffset);
		$trPos = array('x' => 395, 'y' => $exPos['y'] +$trOffset);
		
		//1. Write the title and the type of the word
		//1.1 Write the title 
		imagettftext($img, $titleSize, 0, $titlePos['x'], $titlePos['y'], $text_color, $font, $word['word']);
		//1.2 Write the type of the word
		imagettftext($img, $wdTypeSize, 0, $wdTypePos['x'], $wdTypePos['y'], $wdTypeColor, $ariali, $word['word_type']);
		// imagettftext($img, $titleSize, 0, $titlePos['x'], $titlePos['y'], $text_color, $font, 'PURPORT');
		//2. Write the lines of the definition
		$lines = [];		
		$this->_splitDef($word['definition'], $lines, 0,MAX_CHAR_PER_LINE);
		$i =1;
		foreach ($lines as $line) {
			imagettftext($img, $textSize, 0, $defPos['x'] - (($i>1)?6:0), $defPos['y'] + 35*$i++, $text_color, $font2, $line);
		}
		//3.1. Write the word "Example"
		imagettftext($img, $textSize, 0, $defPos['x'], $defPos['y'] +$exOffset + 35*$i--, $colorGray, $font2, 'Example:');
		//3.2. Write the real Example
		$lines2 = [];
		$this->_splitDef($word['examples'], $lines2, 0,MAX_CHAR_PER_LINE_EX);
		$j =1;
		foreach ($lines2 as $line) {
			imagettftext($img, $textSize, 0, $exPos['x'], $exPos['y'] + 35*($i + $j++) , $text_color, $font2, $line);
		}
		//4.1. Write the words "Translation to Spanish"
		imagettftext($img, $textSize, 0, $defPos['x'], $exPos['y'] + 35*($i + $j) +$trOffset, $colorGray, $font2, 'Traducido al español:');
		//4.2. Write the real translation
		imagettftext($img, $textSize, 0, $trPos['x'], $trPos['y'] + 35*($i + $j) , $text_color, $font2, $word['word_translation']);
		imagejpeg($img,FCPATH.'/res/imgs/words/'.date('Y-m-d').'.jpg');
		imagedestroy($img);
	}
	
	function _postToFB($wordStr){		
		$this->load->library('FB'); //Using the SDK for PHP
		$this->fb->login();
		$this->fb->postPicture('Word of the Day: '.$wordStr,FCPATH.'/res/imgs/words/'.date('Y-m-d').'.jpg');
	}
	
	
	/*************************************************************************************************/
	/** AUXILIAR FUNCTIONS **/
	/*************************************************************************************************/
	function _getLines(){
		
	}
	
	function _splitDef($def, &$lines, $i, $charsPerLine){		
		if (strlen($def) > $charsPerLine){
			//Split the definition into several pieces of maxCharPerLine each
			//Find the first space before char
			$rDef = substr($def,0,$charsPerLine);
			$lastSpace = strrpos($rDef,' ');
			//Split the word in the position of the lastSpace
			$lines[$i++] = substr($def,0,$lastSpace);
			$remainingText = substr($def,$lastSpace,strlen($def));
			$this->_splitDef($remainingText, $lines, $i,$charsPerLine);
		}else{
			$lines[$i++] = substr($def,0,strlen($def));
		}			
	}

	function _LoadJpeg($imgname)
	{
		/* Attempt to open */
		$im = @imagecreatefromjpeg($imgname);

		/* See if it failed */
		if(!$im)
		{
			/* Create a black image */
			$im  = imagecreatetruecolor(150, 30);
			$bgc = imagecolorallocate($im, 255, 255, 255);
			$tc  = imagecolorallocate($im, 0, 0, 0);

			imagefilledrectangle($im, 0, 0, 150, 30, $bgc);

			/* Output an error message */
			imagestring($im, 1, 5, 5, 'Error loading ' . $imgname, $tc);
		}

		return $im;
	}

	
}