<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class FB
{
	private $fb = null;
	
    public function __construct()
    {
        require_once APPPATH.'third_party/php-graph-sdk-5.0.0/src/Facebook/autoload.php';		
    }
	
	
	public function login(){
		//Import the JSON Config file		
		$inputJSON = file_get_contents('fb_config.json');
		$json = json_decode($inputJSON, true);
		//TEST PAGE
		/*
		$this->fb = new Facebook\Facebook([
		  'app_id' => $json['test']['id'],
		  'app_secret' => $json['test']['secret'],
		  'default_graph_version' => 'v3.1',
		]);
		
		$this->fb->setDefaultAccessToken($json['test']['token']);
		*/
		//WORDALIA PAGE
		//Here we perform the login for the Wordalia APP!
		$this->fb = new Facebook\Facebook([
		  'app_id' 		=> $json['Wordalia']['id'],
		  'app_secret' 	=> $json['Wordalia']['secret'],
		  'default_graph_version' => 'v2.8',
		]);

		$this->fb->setDefaultAccessToken($json['Wordalia']['token']);
				
		
	}
	
	public function postPicture($message, $source){
		$data = [
		  'message' => $message,
		  'source' => $this->fb->fileToUpload($source),
		  'scheduled_publish_time'=>time()+(6*60*60),
		  'published'=>false
		];
		
		try {
		  // Returns a `Facebook\FacebookResponse` object
		  $response = $this->fb->post('/me/photos', $data);
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  echo 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}

		$graphNode = $response->getGraphNode();

		return $graphNode['id'];
	}
}

?>