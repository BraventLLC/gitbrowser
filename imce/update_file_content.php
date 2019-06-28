<?php
if(isset($_REQUEST['filePath']) && $_REQUEST['filePath'] !=''){
	
	$file = fopen('./'.$_REQUEST['filePath'],"w");
	$res = fwrite($file,$_REQUEST['content']);
	
		

	shell_exec("git add /var/www/html/gitoperations/github-php-client/*");
	shell_exec("git commit -m 'your commit message'");
	shell_exec("git push origin master");
	//$repo->commit('Some commit message');
	//die('IMIN22');
	//$repo->push('origin', 'master');


	/*$respAdd = shell_exec("git add --all");
	sleep(1);
	
	$respCommit = shell_exec('git commit -m "from server"');	
	sleep(3);

	
	$respPush = shell_exec('git push -u origin master');	
	sleep(3);*/
	echo 1;
	//echo '<BR><pre>';print_r($file);
	//exit;
	//echo fwrite($file,"Hello World. Testing!");

	//echo 'File=>'.file_put_contents(trim($_REQUEST['filePath']));
}else{
	echo 'Please select file.';
}