<?php		

	include('./vendor/autoload.php');
	$client = new \Github\Client();
	global $base_url;
	if(isset($_COOKIE['branchName']) && $_COOKIE['branchName']!=''){
	  $branchName = trim($_COOKIE['branchName']);
	}else{
	  $branchName = 'master';
	}

	if(isset($_REQUEST['fpath']) && $_REQUEST['fpath'] !=''){
		$userName = trim($_REQUEST['userName']);
		$repoName = trim($_REQUEST['repoName']);
		$filePath = str_replace('./'.$repoName.'/', '', $_REQUEST['fpath']);		

		$commits = $client->api('repos')->commits()->all('BraventLLC', 'fartest', array('sha' => $branchName, 'path' => $filePath));
		$fileSha = $commits[0]['sha'];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/repos/'.$userName.'/'.$repoName.'/contents/'.$filePath);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		$headers = array();
		$headers[] = 'application/x-www-form-urlencoded';
		$headers[] = 'cache-control: no-cache';
		$headers[] = 'Authorization: Basic bmlsZXNoeWFzaGNvOllhc2hjbzEyMw==';
		$headers[] = 'User-Agent: '.$branchName;

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($ch);
		if (curl_errno($ch)) {
		  echo 'Error:' . curl_error($ch);
		}

		curl_close ($ch);
		$result = json_decode($result);		
		$path = $result->path;
		$content = base64_decode($result->content);
		echo $path."@@@@".$fileSha."@@@@".$content;
	}else{
		echo 'Please select file.';
	}
?>