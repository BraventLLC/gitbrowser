<?php	
	global $base_url;
	include('./vendor/autoload.php');
	$client = new \Github\Client();

	if(isset($_COOKIE['branchName']) && $_COOKIE['branchName']!=''){
	  $branchName = trim($_COOKIE['branchName']);
	}else{
	  $branchName = 'master';
	}
	
	if(isset($_REQUEST['fpath']) && $_REQUEST['fpath'] !=''){		
		$repoName = trim($_REQUEST['repoName']);
		$filePath = str_replace('./'.$repoName.'/', '', $_REQUEST['fpath']);
		$userName = trim($_REQUEST['userName']);
		$repoName = trim($_REQUEST['repoName']);
		
		$commits = $client->api('repos')->commits()->all('BraventLLC', $repoName, array('sha' => $branchName, 'path' => $filePath));

		/*echo '<BR><BR><pre>';print_r($commits);exit;
		echo 'https://api.github.com/repos/'.$userName.'/'.$repoName.'/commits?path='.$filePath;
		$ch = curl_init();		
		curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/repos/'.$userName.'/'.$repoName.'/commits?path='.$filePath);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		$headers = array();
		$headers[] = 'application/x-www-form-urlencoded';
		$headers[] = 'cache-control: no-cache';
		$headers[] = 'Authorization: Basic QnJhdmVudExMQzphODBhMzQ3NjcxM2VmMjg2NmMyNzVhOGEzMjk1NGVkNzFiZTI3ODg4';
		$headers[] = 'User-Agent: '.$branchName;

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($ch);
		if (curl_errno($ch)) {
		  echo 'Error:' . curl_error($ch);
		}
		curl_close ($ch);
		$commits = json_decode($result);*/	
		//echo '<BR><BR><pre>';print_r($commits);exit;
 		$tot = count($commits);
        if(isset($tot) && $tot>0){          	
          	for($i=0;$i<$tot;$i++){
          	?>
        	<li>
              <div style="float:left;width:100%;">              
                <div class="timeline_title_row">
					<p style="display: flex;">
						<label class="CustCheck">                                
						    <input type="checkbox" name="revname[]" id="revname" value="<?php echo $commits[$i]['sha'];?>">
						    <span class="checkmark"></span>
						</label>                    
						<img src="<?php echo $commits[$i]['author']['avatar_url'];?>" width="20" height="20">
						<span><?php echo $commits[$i]['commit']['author']['name'];?></span> 
					</p>
                  	<a href="<?php echo $commits[$i]->html_url;?>" target="_blank">
                  		<?php echo date("d M Y", strtotime($commits[$i]['commit']['author']['date']));?>
                  	</a>               
                </div>
                <p style="clear: both;">
                	<a href="<?php echo $commits[$i]['html_url'];?>" target="_blank">
                		<?php echo $commits[$i]['commit']['message'];?>
                	</a>
                </p>
              </div>
        	</li>
        <?php }
          }else{
            echo "<li>No, commits found.</li>";
          }     
		//echo '<pre>';print_r($result);
	}else{
		echo 'Please select file.';
	}
?>