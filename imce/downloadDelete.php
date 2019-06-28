<?php	
	global $base_url;
	if(isset($_COOKIE['branchName']) && $_COOKIE['branchName']!=''){
	  $branchName = trim($_COOKIE['branchName']);
	}else{
	  $branchName = 'master';
	}
	$repositoryUserName = variable_get('imce_settings_repouname');
	$repositoryRepository = variable_get('imce_settings_repository');
	echo '<pre>';print_r($_REQUEST);exit;
	if(isset($_REQUEST['repoURL']) && $_REQUEST['repoURL'] !=''){
		  /* Get ref params from github */
		  $getRefURL = trim($_REQUEST['repoURL']);
		  $ch = curl_init();
		  curl_setopt($ch, CURLOPT_URL, $getRefURL);
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        
		  $headers = array();
		  $headers[] = 'Content-Type: application/json';
		  $headers[] = 'Authorization: Basic bmlsZXNoeWFzaGNvOllhc2hjbzEyMw==';
		  $headers[] = 'User-Agent: '.$branchName;
		  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");        
		  $result = curl_exec($ch);
		  if (curl_errno($ch)) {
		      echo 'Error:' . curl_error($ch);
		  }
		  curl_close ($ch);    
		  $compareResult = json_decode($result);


		  /* Download a file */
		  $reference = $compareResult->ref;
		  $path = 'folder2/sanju123.php';		  
		  $fileContent = $client->api('repo')->contents()->download($repositoryUserName, $repositoryRepository, $path, $reference);
		  header('Content-Description: File Transfer');
		  header('Content-Type: application/octet-stream');
		  header('Content-Disposition: attachment; filename="'.basename($path).'"');
		  header('Expires: 0');
		  header('Cache-Control: must-revalidate');
		  header('Pragma: public');
		  header('Content-Length: ' . filesize($path));
		  flush(); // Flush system output buffer
		  echo 1;
	}else{
		echo 0;
	}
?>