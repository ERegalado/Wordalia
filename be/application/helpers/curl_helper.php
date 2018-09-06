<?php 
// Method: POST, PUT, GET etc
// Data: array("param" => "value") ==> index.php?param=value

function CallAPI($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    // curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    // curl_setopt($curl, CURLOPT_USERPWD, "admin:1234");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
	
	//Add the content headers
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		'Content-Type: Content-Type: application/x-www-form-urlencoded',
		'Content-Length: 0' 
	));
	
	// curl_setopt($curl, CURLOPT_REFERER, 'https://www.domain.com/');
	curl_setopt($curl, CURLOPT_AUTOREFERER, true); 
    // curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_VERBOSE, 1);
	curl_setopt( $curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	curl_setopt($curl, CURLOPT_TIMEOUT_MS, 300000);
    $result = curl_exec($curl);
	    
	
	/* DEBUG THE CURL */
	
	/*if(curl_errno($curl)){
		echo 'Curl error: ' . curl_error($curl);
	}
	echo 'Curl Info:';
	print_r(curl_getinfo($curl));*/

	curl_close($curl);
	
    return $result;
}


?>