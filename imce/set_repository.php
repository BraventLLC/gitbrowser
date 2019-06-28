<?php	
	@session_start(0);	
	$treeMenuHTML = '';
	global $base_url;
	if(isset($_COOKIE['branchName']) && $_COOKIE['branchName']!=''){
	  $branchName = trim($_COOKIE['branchName']);
	}else{
	  $branchName = 'master';
	}

	if(isset($_REQUEST['reponame']) && $_REQUEST['reponame'] !=''){		
		$repoNames = trim($_REQUEST['reponames']);
		$userName = trim($_REQUEST['userName']);
		$repoName = trim($_REQUEST['repoName']);
		//$respoData = shell_exec("git checkout CASE-11001 -- FAR-4.2001.dita");
		//echo '<BR><BR>BN =>'.$_COOKIE['branchName'].'<BR>RespoData DATA<BR><pre>';print_r($respoData);//exit;
		//echo '<BR>https://api.github.com/repos/'.$userName.'/'.$repoName.'/git/refs/heads/'.$repoNames;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/repos/'.$userName.'/'.$repoName.'/git/refs/heads/'.$repoNames);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		$headers = array();
		$headers[] = 'application/x-www-form-urlencoded';
		$headers[] = 'cache-control: no-cache';
		$headers[] = 'Authorization: Basic bmlsZXNoeWFzaGNvOllhc2hjbzEyMw==';
		$headers[] = 'User-Agent: '.$branchName;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($ch);		
		curl_close ($ch);
		$respo = json_decode($result);		
		//echo '<BR>DATAS<BR><pre>';print_r($respo);exit;

		$repoSHA = $respo->object->sha;
		/* NOW WE NEED TO CALL THIS TO GET FILES AND FOLDER OF BRANCH */
		$getFilesFolders = "https://api.github.com/repos/".$userName."/".$repoName."/git/trees/".$repoSHA."?recursive=1";
		$ch1 = curl_init();
		curl_setopt($ch1, CURLOPT_URL, $getFilesFolders);
		curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);		
		curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "GET");
		$headers = array();
		$headers[] = 'application/x-www-form-urlencoded';
		$headers[] = 'cache-control: no-cache';
		$headers[] = 'Authorization: Basic bmlsZXNoeWFzaGNvOllhc2hjbzEyMw==';
		$headers[] = 'User-Agent: '.$repoNames;
		curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers);
		$result1 = curl_exec($ch1);
		
		curl_close ($ch1);
		$respo2 = json_decode($result1);
		$totF = count($respo2->tree);

		$treeMenuHTML = '<ul class="php-file-tree">';
		foreach( $respo2->tree as $key => $val){			
			$type = $val->type;
			if($type=='blob'){				
				$treeMenuHTML .= '<li class="pft-file ext-md">
								<a href="javascript:void(0);" onclick="showContentNew('.$key.', '."'".$userName."'".', '."'".$repoName."'".');showRevissions('.$key.');" id="idmylink_'.$key.'" data-name="'.$val->path.'">'.$val->path.'</a>
								<a style="float: right;" href="javascript:void(0);" onclick="call_delete_file('.$val->path.')" class="redcross">X</a>
							</li>';
			}
		}		
		$treeMenuHTML .= '</ul>';
		$_SESSION['branchName'] = $repoNames;	
	}else{
		$_SESSION['branchName'] = 'master';
	}	
	setcookie('branchName', $_SESSION['branchName'], time() + (86400 * 30), "/"); // 86400 = 1 day
	echo $treeMenuHTML;
?>