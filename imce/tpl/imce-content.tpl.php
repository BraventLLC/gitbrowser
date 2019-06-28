<?php
global $base_url;
$modPath = drupal_get_path('module', 'imce');
include_once($modPath ."/php_file_tree.php");
include_once($modPath . '/vendor/autoload.php');

$client = new \Github\Client();





$msg = "";
$nmsg = "";
$errorMsg = "";
$compareResult = "";

/* GET DETAILS FROM ADMIN CONFIG */
$repositoryName     = variable_get('imce_settings_repourl');
$repositoryUserName = variable_get('imce_settings_repouname');
$repositoryPassword = variable_get('imce_settings_password');

$repositoryRepository = variable_get('imce_settings_repository');
$repositoryUserEmail = variable_get('imce_settings_repouemail');
$repositoryDirNameFN = variable_get('imce_settings_filedirname');



/*$commitMessage = 'DEL FILE for testing';
$path = 'source/FAR-52.209-12.dita';
$branch = 'CASE-11003';

$committer = array('name' => $repositoryUserName, 'email' => $repositoryUserEmail);
echo "<BR>".$repositoryUserName.", ".$repositoryRepository.", ".$path.", ".$branch;
$oldFile = $client->api('repo')->contents()->show($repositoryUserName, $repositoryRepository, $path, $branch);

echo '<BR>DELETE RESPONSE <BR><pre>';print_r($oldFile);

echo "<BR><BR>".$repositoryUserName.", ".$repositoryRepository.", ".$path.", ".$commitMessage.", ".$oldFile['sha'].", ".$branch.", ".$committer;

$fileInfo = $client->api('repo')->contents()->rm($repositoryUserName, $repositoryRepository, $path, $commitMessage, $oldFile['sha'], $branch, $committer);

echo '<BR><BR> RESPONSE <BR><pre>';print_r($fileInfo);exit;*/
/*$commits = $client->api('repos')->commits()->all('BraventLLC', 'fartest', array('sha' => 'CASE-11001', 'path' => 'source/FAR-4.2001.dita'));

echo '<BR> COMMITS RESPONSE <BR><pre>';print_r($commits[0]['sha']);exit;*/

/*$repositoryName = "https://github.com/BraventLLC/fartest.git";
$repositoryUserName = "BraventLLC";
$repositoryPassword = "a80a3476713ef2866c275a8a32954ed71be27888";
$repositoryRepository = "fartest";
$repositoryUserEmail = "ayas.kant@gmail.com";
$repositoryDirNameFN = "fartest";*/


/*$repositoryName = "https://github.com/github/hub.git";
$repositoryUserName = "rtomayko";
$repositoryPassword = "";
$repositoryRepository = "tilt";
$repositoryUserEmail = "ayas.kant@gmail.com";
$repositoryDirNameFN = "hub";*/


if(isset($_COOKIE['branchName']) && $_COOKIE['branchName']!=''){
  $branchName = trim($_COOKIE['branchName']);
}else{
  $branchName = 'master';
}
$respositoryURL = "https://api.github.com/repos/".$repositoryUserName."/".$repositoryRepository."/";
$getRefURL = $respositoryURL."git/refs/heads/".$branchName;

/* REMOVE/DELETE FILE FROM SPECIFIC BRANCH WITH FILE PATH */
if(isset($_REQUEST['deleteFilePath']) && $_REQUEST['deleteFilePath']!=''){    
  $commitMessage = "File ".$_REQUEST['deleteFilePath']." deleted from repository.";  
  $filePath = trim($_REQUEST['getDelRefeURL']);
  $exp = explode('/',$filePath);    
  if(count($exp)>0){
    $tot = (int) (count($exp)-1);
    $filePath = $exp[$tot];
  }else{
    $filePath = $filePath;
  }
  
  $committer     = array('name' => $repositoryUserName, 'email' => $repositoryUserEmail);
  $oldFile       = $client->api('repo')->contents()->show($repositoryUserName, $repositoryRepository, $filePath, $branchName);  
    
  $fileInfo = $client->api('repo')->contents()->rm($repositoryUserName, $repositoryRepository, $filePath, $commitMessage, $oldFile['sha'], $branchName, $committer);
  echo '<BR><BR>'.$fileInfo;
  exit;
}

/* Function to get ref and download latest version on file from given repository */
if(isset($_REQUEST['downloadFile']) && $_REQUEST['downloadFile']!=''){
  /*print_r($_REQUEST);exit;*/
  $getRefURL = trim($_REQUEST['repoURL']);
  $path = trim($_REQUEST['getRefeURL']);
  
  $getRefURLNew = $respositoryURL."git/refs/heads/".$branchName;
  /* Get ref params from github */
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $getRefURLNew);
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

  $path = str_replace('./fartest/', '', $path);
  $fileContent = $client->api('repo')->contents()->download($repositoryUserName, $repositoryRepository, $path, $reference);
  
  header('Content-Description: File Transfer');
  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename="'.basename($path).'"');
  header('Expires: 0');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  header('Content-Length: ' . filesize($path));
  flush(); // Flush system output buffers  
}

/* COMPAIRE commits of same file */
if(isset($_REQUEST['compaireBrnch']) && $_REQUEST['compaireBrnch']=='Compare'){
  $compareResult = "";
  if(count($_REQUEST['revname']) == 2){
    $commit1 = $_REQUEST['revname'][0];
    $commit2 = $_REQUEST['revname'][1];
    $URL = $respositoryURL."compare/".$commit1."...".$commit2;    

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: Basic bmlsZXNoeWFzaGNvOllhc2hjbzEyMw==';
    $headers[] = 'User-Agent: '.$branchName;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close ($ch);    
    $compareResult = json_decode($result);
  }else{
    $compareResult = "";
    $errorMsg = "Please select only 2 commit's for compaire.";
  }
}

/* EDIT */
if(isset($_REQUEST['ecomment']) && $_REQUEST['nfname']!=""){  
  //print_r($_REQUEST);
  $fileName = trim($_REQUEST['nfname']);
  $comment  = trim($_REQUEST['ecomment']);
  $content  = base64_encode(trim($_REQUEST['editor']));
  
  //if(isset($_REQUEST['fnameSha']) && $_REQUEST['fnameSha']!=''){
    $SHA      = trim($_REQUEST['fnameSha']);
    $arrPost  = array('path' => ''.$fileName,
                    'message' => $comment,
                    'committer' => array('name' => $repositoryUserName, 'email'=> $repositoryUserEmail),
                    'content' => $content,
                    'sha' => $SHA,
                    'branch' => $branchName);
    $nmsg     = "File updated successfully.";
 /* }else{
    $arrPost  = array('path' => $fileName,
                    'message' => $comment,
                    'committer' => array('name' => $repositoryUserName, 'email'=> $repositoryUserEmail),
                    'content' => $content,
                    'branch' => $branchName);
    $nmsg     = "File created successfully.";
  }*/

  $commits = $client->api('repos')->commits()->all('BraventLLC', 'fartest', array('sha' => $branchName, 'path' => $filePath));
  //echo $branchName.'<BR><BR><BR><BR>CURL REQUEST<BR><pre>';print_r($commits);
  echo '<BR><BR>'.$respositoryURL.'contents/'.$fileName.'<BR><BR><BR><BR><pre>';print_r($arrPost);
  //exit;
  
  $postJson = json_encode($arrPost);

  $ch = curl_init();  
  curl_setopt($ch, CURLOPT_URL, $respositoryURL.'contents/'.$fileName.'?ref='.$branchName);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, "".$postJson."");
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
  $headers = array();
  $headers[] = 'application/x-www-form-urlencoded';
  $headers[] = 'cache-control: no-cache';
  $headers[] = 'authorization: Basic QnJhdmVudExMQzphODBhMzQ3NjcxM2VmMjg2NmMyNzVhOGEzMjk1NGVkNzFiZTI3ODg4';
  $headers[] = 'User-Agent: master';//.$branchName;
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  $result = curl_exec($ch);
  echo $branchName.'<BR><BR><BR><BR>CURL REQUEST<BR><pre>';print_r($result);exit;
  curl_close ($ch);
  $respo = json_decode($result);
}else{
  $errorMsg = "<font style='color:red'>Please provide proper comment and name of file.</font>";
}



if(isset($_REQUEST['cbranch']) && $_REQUEST['cbranch']!=''){
  global $base_url;
  $bname = trim($_REQUEST['cbranch']);
  /* FINAL CODE TO CREATE NEW BRANCH  */
  $arrParamas = array('username' => $repositoryUserName,
                  'password' => $repositoryPassword,
                  'ref' => 'refs/heads/'.$bname,
                  'sha' => 'd439b4e7cf531a1f386fc2104f465653a24c2784');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $respositoryURL.'git/refs');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrParamas));
  curl_setopt($ch, CURLOPT_POST, 1);
  $headers = array();
  $headers[] = 'Content-Type: application/json';
  $headers[] = 'Authorization: Basic bmlsZXNoeWFzaGNvOllhc2hjbzEyMw==';
  $headers[] = 'User-Agent: '.$branchName;
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  $result = curl_exec($ch);
  if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
  }
  curl_close ($ch);
  $result = json_decode($result);  
  if(isset($result->message) && $result->message != ''){
    $msg = $result->message;
  }else{
    $msg = '';
  } 
  if(isset($result->ref) && $result->ref != ''){
    $msg = "Branch created successfully";
  }else{
    $msg = '';
  }
}

/* COMMAND TO GET ALL BRANCHES OF PROVIDED REPOSITORY */
$branches = $client->api('repo')->branches($repositoryUserName, $repositoryRepository);
//$tory = shell_exec("curl https://api.github.com/repos/nileshyashco/demo/git/refs/heads");


/* GET ALL COMMITS OF MASTER BRANCH */
//$commits = $client->api('repo')->commits()->all('nileshyashco', 'demo', array('sha' => $branchName));

$imce = & $imce_ref['imce'];//keep this line.
/* CODE TO PULL REPOSITORY FROM GITHUB SERVER */
$pullRepository = shell_exec("git clone ".$repositoryName);
$checkoutRepos  = shell_exec("git checkout ".$branchName);
shell_exec("chmod 0777 -R ".$repositoryRepository);
?>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
  imce.hooks.load.push(imce.initiateShortcuts); //shortcuts for directories and files
  imce.hooks.load.push(imce.initiateSorting); //file sorting
  imce.hooks.load.push(imce.initiateResizeBars); //area resizing
  //inline preview
  imce.hooks.list.push(imce.thumbRow);
  imce.vars.tMaxW = 120; //maximum width of an image to be previewed inline
  imce.vars.tMaxH = 120; //maximum height of an image to be previewed inline
  imce.vars.prvW = 40; //maximum width of the thumbnail used in inline preview.
  imce.vars.prvH = 40; //maximum height of the thumbnail used in inline preview.
  //imce.vars.prvstyle = 'stylename'; //preview larger images inline using an image style(imagecache preset).
  //enable box view for file list. set box dimensions = preview dimensions + 30 or more
  //imce.vars.boxW = 100; //width of a file info box
  //imce.vars.boxH = 100; //height of a file info box
  //imce.vars.previewImages = 0; //disable click previewing of images.
  //imce.vars.cache = 0; //disable directory caching. File lists will always refresh.
  //imce.vars.absurls = 1; //make IMCE return absolute file URLs to external applications.
//--><!]]>
</script>

<div id="imce-content" class="container-fluid row m-0">
<div id="message-box"></div>
<div id="help-box"><!-- Update help content if you disable any of the extra features above. -->
  <div id="help-box-title"><span><?php print t('Help'); ?>!</span></div>
  <div id="help-box-content">
    <h5 class="h5"><?php print t('Tips'); ?>:</h5>
    <ul class="tips">
      <li><?php print t('Select a file by clicking the corresponding row in the file list.'); ?></li>
      <li><?php print t('Ctrl+click to add files to the selection or to remove files from the selection.'); ?></li>
      <li><?php print t('Shift+click to create a range selection. Click to start the range and shift+click to end it.'); ?></li>
      <li><?php print t('In order to send a file to an external application, double click on the file row.'); ?></li>
      <li><?php print t('Sort the files by clicking a column header of the file list.'); ?></li>
      <li><?php print t('Resize the work-spaces by dragging the horizontal or vertical resize-bars.'); ?></li>
      <li><?php print t('Keyboard shortcuts for file list: up, down, left, home, end, ctrl+A.'); ?></li>
      <li><?php print t('Keyboard shortcuts for selected files: enter/insert, delete, R(esize), T(humbnails), U(pload).'); ?></li>
      <li><?php print t('Keyboard shortcuts for directory list: up, down, left, right, home, end.'); ?></li>
    </ul>
    <h5 class="h5"><?php print t('Limitations'); ?>:</h5>
    <ul class="tips">
      <li><?php print t('Maximum file size per upload') .': '. ($imce['filesize'] ? format_size($imce['filesize']) : t('unlimited')); ?></li>
      <li><?php print t('Permitted file extensions') .': '. ($imce['extensions'] != '*' ? $imce['extensions'] : t('all')); ?></li>
      <li><?php print t('Maximum image resolution') .': '. ($imce['dimensions'] ? $imce['dimensions'] : t('unlimited')); ?></li>
      <li><?php print t('Maximum number of files per operation') .': '. ($imce['filenum'] ? $imce['filenum'] : t('unlimited')); ?></li>
    </ul>
  </div>
</div>

<div class="container-fluid row m-0 bg-light p-0" style="max-width:375px" id="left-content-holder">
<div id="ops-wrapper1" class="col-12 p-0">
  <div id="op-items">
    <input type="text" name="getrepo" value="<?php echo $repositoryName;?>" style="width: 240px;" class="form-control">
  </div>
  <div id="op-contents" class="w-100" style="left:none !important;"></div>
  <div class="drop-menu mb-3">
    <label for="validationCustom03">Branches:</label>
    <select class="form-control form-control-lg" name="category" id="validationCustom03" onchange="switch_repository(this.value, '<?php echo $repositoryUserName;?>', '<?php echo $repositoryRepository;?>')" required>
    <?php
      if(isset($branches) && $branches!=''){
        $tot = count($branches);
        for($i=0;$i<$tot;$i++) {
          if($branches[$i]['name']==$branchName){ $sel = 'selected';}else{ $sel = '';};
          echo "<option value='".$branches[$i]['name']."' $sel>".$branches[$i]['name']."</option>";
        }
      }
    ?>
    </select>
    <div class="add_branch" style="width: 130px;text-align: center;line-height: 40px; position: inherit;float: right;font-size: 40px;background-color: dodgerblue;border-radius: 3px;height: 47px; margin-top: -48px;">
      <a href="javascript:void(0);" id="myBtn"><font style="color: #FFF;"> + </font></a>
    </div>
  </div>
<div class="row" id="brnmid" style="background-color: #FFF;padding: 10px;"><?php echo $msg;?></div>
<!-- The Modal -->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Create New Branch</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
       <div class="row">
       <form method="post" action="">
        <input type="text" name="cbranch" style="width: 70%" id="cbranch" class="form-control" placeholder="Please enter valid branchname.">
        <input type="submit"  class="btn btn-primary" name="save" value="Submit">
       </form>
       </div>  
      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
</div>
<div id="ops-wrapper" class="col-12  Left_Tab_Row">
  <div id="op-items"><ul id="ops-list">
    <li> <a> Upload </a> </li>
    <li> <a> Thumbnails </a> </li>    
    <li> <a> Resize </a> </li>
  </ul></div>
  <div id="op-contents" class="w-100" style="left:none !important;"></div>
</div>
<script>
function myFunction() {
  // Declare variables 
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("file-list");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    } 
  }
}
</script>
<div id="browse-wrapper" class="col-12 p-0 row m-0" style="height:80vh !important">
  <div id="navigation-wrapper" style="width: 100%;">
    <div class="w-100 bg-dark text-white" style="height:30px;line-height:30px;font-weight:400;padding-left:6px;font-size:16px;margin-top:0px;"><?php print "Folder ".strtolower(t('Navigation')); ?></div>
      <link href="<?php echo $base_url.'/'.$modPath.'/css/default.css';?>" rel="stylesheet" type="text/css" media="screen" />
      <script src="<?php echo $base_url.'/'.$modPath.'/php_file_tree.js';?>" type="text/javascript"></script>      
      <?php 
        echo php_file_tree("./".$repositoryDirNameFN, "javascript:showContent('[link]', '".$repositoryUserName."', '".$repositoryRepository."');");
        //echo '<BR><BR>branchName = '.$branchName;exit;
        if(isset($branchName) && $branchName!=''){
          ?> 
            <script type="text/javascript"> $(document).ready(function() {
                switch_repository('<?php echo $branchName;?>', '<?php echo $repositoryUserName;?>', '<?php echo $repositoryRepository;?>');
              });
               </script>
          <?php
        }
      ?>
    </div>
  <div id="sub-browse-wrapper" style="width:100%;">

  <div class="w-100 bg-secondary text-white" style="height:30px;line-height:30px;font-weight:400;padding-left:6px;font-size:16px">File <?php print strtolower(t('Navigation')); ?></div>    
    <div id="file-header-wrapper">
      <table id="file-header" class="files">
        <tbody>
          <tr>
            <td class="name"><?php print t('File name'); ?></td>
            <td class="size"><?php print t('Size'); ?></td>
            <td class="date"><?php print t('Date'); ?></td>
          </tr>

        </tbody>
      </table>
    </div>

    <div id="file-list-wrapper">
      <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for files..">
      <!-- <input type="text" id="myInput" placeholder="Search for files.."> -->
      <button type="button" onclick="myFunction()">Search</button>
      <?php //print theme('imce_file_list', array('imce_ref' => $imce_ref)); /* see imce-file-list-tpl.php */?>
      <?php echo php_file_tree_filenav("./".$repositoryDirNameFN, "javascript:showContent('[link]', '".$repositoryUserName."', '".$repositoryRepository."');");?>
    </div>

    <div id="dir-stat"><?php print t('!num files using !dirsize of !quota', array(
        '!num' => '<span id="file-count">'. count($imce['files']) .'</span>',
        '!dirsize' => '<span id="dir-size">'. format_size($imce['dirsize']) .'</span>',
        '!quota' => '<span id="dir-quota">'. ($imce['quota'] ? format_size($imce['quota']) : ($imce['tuquota'] ? format_size($imce['tuquota']) : t('unlimited quota'))) .'</span>'
      )); ?>
    </div>
  </div><!-- sub-browse-wrapper -->
</div><!-- browse-wrapper -->
</div>
<div id="resizable-content" class="col p-0" style="height:100vh">
  <div class="formPanelBody"> 
    <!-- Button panel start here onclick="download_remove(1,'<?php echo $getRefURL;?>');"-->
    <div class="buttonRht"> 
      <a class="btn custom-primary_btn" onclick="show_pages(1);" href="#">Edit</a> 
      <a class="btn custom-primary_btn" onclick="show_pages(2);" id="rivision" href="#" >Revisions</a> 
      
      <form action="" method="POST"> 
        <input type="hidden" name="getRefeURL" id="getRefeURL" >
        <input type="hidden" name="repoURL" id="repoURL" value="<?php echo $getRefURL;?>">
        <input type="submit" class="btn custom-primary_btn" value="Download" name="downloadFile" id="download">
      </form>

      <form action="" method="POST"> 
        <input type="hidden" name="getDelRefeURL" id="getDelRefeURL" >
        <input type="hidden" name="repoURL" id="repoURL" value="<?php echo $getRefURL;?>">
        <input type="submit" class="btn custom-primary_btn" value="Delete" name="deleteFilePath" id="delete">
      </form>

      <!-- <a class="btn custom-primary_btn" onclick="download_remove(0,'<?php echo $getRefURL;?>');" id="delete" href="#">Delete</a> -->
      <a class="btn custom-primary_btn" onclick="show_pages(4);" id="newfile" href="#">Create New File</a> 
    </div>
    <div class="clearfix"></div>
    <div class="formContentWrapper" id="create_file">
      <!-- form goes here -->
      <div class="row" id="filemsg" style="padding: 20px;"><font style="font-size: 12px; color: green;"><?php echo $nmsg;?></font></div>
    
    <form method="post" action="">
      <div>  
        <div class="commentForm"> 
          <!-- Name -->
          <div class="row form-group ">
            <label class="control-label" for="name">Name</label>
            <div class="col-md-9">
              <input id="nfname" name="nfname" type="text" placeholder="File name" class="form-control">
              <input id="fnameSha" name="fnameSha" type="text">            
            </div>
          </div>        
          <!-- enter comments-->
          <div class="row form-group ">
            <label class="control-label" for="email">Enter Comments</label>
            <div class="col-md-9">
              <input id="ecomment" name="ecomment" type="text" placeholder="Your comment" class="form-control">
            </div>
          </div>
        </div>
        
        <!-- EDITOR GOES HERE -->
        <div id="preview-wrapper">          
            <script src="https://cdn.ckeditor.com/4.11.4/standard-all/ckeditor.js"></script>
            <textarea cols="80" id="editor" name="editor" rows="10" data-sample-short>Do a double click on a file to open it...</textarea>
            <script>
              CKEDITOR.replace('editor', {
                uiColor: '#CCEAEE'
                
              });            
            </script>
        </div>

        <div class="col-md-12 BtnSecsubmit">      
          <input type="submit" class="btn btn-primary" name="save" value="Submit">
        </div>
      </div>      
    </form>
  <!-- </div> -->
</div>
 <!--  comment section start --> 
 <div class="mt-5 mb-5">
  <form method="post" action="#">
    <div class="row" id="revision_list" style="display: none;">
        <div class="col-md-12 timeline_commnet">           
            <ul class="timeline">
              <div id="getRevissions"></div>                            
            </ul>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-12 BtnSecRow">
          <input type="submit"  class="btn btn-primary" name="compaireBrnch" value="Compare">
        </div>
    </div>
  </form>
</div>
<!-- end -->

<!-- Panel Compare -->
<div class="col-md-12 Compare_Sec">
  <div class="panel-heading"> <h5> Compare List </h5> </div>
    <div class="list-group"> 
      <span id="nodata">  Please compaire 2 commits to check differences. </span>
        <?php echo '<pre>';print_r($compareResult);?>
        <!-- 
        <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
          <div class="w-100 justify-content-between">
            <label class="CustCheck">
                <input type="checkbox" checked="checked">
                <span class="checkmark"></span>
            </label>
            <img src="<?php echo @$commits[0]['author']['avatar_url'];?>" width="20" height="20">
            <h6 class="mb-1">List group item heading</h5>             
          </div>
          <small> 31 May 2019 </small>
          <p class="mb-1">Donec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius
            blandit.</p>
        </a> -->
    </div>
  </div>
  <!-- Panel Compare End -->
  <!-- resizable-content -->
  <div id="forms-wrapper"><?php print $forms; ?></div>
</div>

<!-- imce-content -->
<!-- <script
  src="https://code.jquery.com/jquery-3.4.1.js"
  integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
  crossorigin="anonymous"></script> -->
<script type="text/javascript">
  if($('#filemsg').val() != ''){
    setTimeout(function() {
       $('#filemsg').fadeOut('fast');
    }, 5000);
  }
  if($('#brnmid').val() != ''){
    setTimeout(function() {
       $('#brnmid').fadeOut('fast');
    }, 5000);
  }

  function showRevissionsNew(action, url, userName, repoName){
    /*alert(action);
    alert(url);
    alert(userName);
    alert(repoName);*/
    $('#edtFileName').val(myFile);
    $('#nfname').val(myFile);
    $('#getRefeURL').val(myFile);
    $('#getDelRefeURL').val(myFile);
    
    var request = $.ajax({
      url: "<?php echo $base_url.'/'.$modPath;?>/get_revissions.php?repoURL="+url+"&type="+action+"&repoName="+repoName+"&userName="+userName,
      type: "POST",
      data: {repoURL : repoURL},
      dataType: "text"
    });      
    request.done(function(msg) { 
      //alert(msg);
      $('#getRevissions').html(msg);
    });
    request.fail(function(jqXHR, textStatus) {
      alert( "Request failed: " + textStatus );
    });
  }



  function download_remove(act, url){      
      //alert(url);return false;
      var request = $.ajax({
        url: "<?php echo $base_url.'/'.$modPath;?>/downloadDelete.php?repoURL="+url+"&type="+act,
        type: "POST",
        data: {url : url},
        dataType: "text"
      });
      
      request.done(function(msg) {      
        //alert(msg);    
        //$('.php-file-tree').html(msg);
      });

      request.fail(function(jqXHR, textStatus) {
        alert( "Request failed: " + textStatus );
      });
  }



  /* CREATE NEW BRANCH CLASS */
  function create_branch() {
    var bname = $('#cbranch').val();    
    if(bname==''){
      alert('Please enter branchname.');
      $('#cbranch').val('');
      $('#cbranch').focus();
      return false;
    }
    var requestB = $.ajax({
      url: "<?php echo $base_url.'/'.$modPath;?>/creare_new_branch.php?bname=tt007",      
      type: "POST",
      data: {bname : bname},
      dataType: "text"
    });
    requestB.done(function(msg) {
      //alert(msg);          
      $("#editor").html( msg );      
    });

    requestB.fail(function(jqXHR, textStatus) {
      alert( "Request failed: " + textStatus + ' ERRO: ' );
      console.log(jqXHR);
    });
  }

  function showRevissions(k){
    $('#idmylink_'+k).click(function () {
      var myFile = $(this).text();      
      $('#edtFileName').val(myFile);
      $('#nfname').val(myFile);
      $('#edtFileName').val(myFile);
      $('#getRefeURL').val(myFile);
      $('#getDelRefeURL').val(myFile);


      var request = $.ajax({
        url: "<?php echo $base_url.'/'.$modPath;?>/get_revissions.php?fpath="+myFile+"&repoName=<?php echo $repositoryRepository;?>",
        type: "POST",
        data: {fpath : myFile},
        dataType: "text"
      });      
      request.done(function(msg) { 
        //alert(msg);
        $('#getRevissions').html(msg);
      });
      request.fail(function(jqXHR, textStatus) {
        alert( "Request failed: " + textStatus );
      });
    });
  }


  function showRevissionsNew(myFile){
    $('#edtFileName').val(myFile);
    $('#nfname').val(myFile);
    var request = $.ajax({
      url: "<?php echo $base_url.'/'.$modPath;?>/get_revissions.php?fpath="+myFile,
      type: "POST",
      data: {fpath : myFile},
      dataType: "text"
    });      
    request.done(function(msg) { 
      //alert(msg);
      $('#getRevissions').html(msg);
    });
    request.fail(function(jqXHR, textStatus) {
      alert( "Request failed: " + textStatus );
    });
  }  

  function showContentNew(k, userName, repoName){

    $('#idmylink_'+k).click(function () {      
      var myFile = $(this).text();      
      $('#edtFileName').val(myFile);
      $('#nfname').val(myFile);
      $('#getRefeURL').val(myFile);
      $('#getDelRefeURL').val(myFile);

      var request = $.ajax({
        url: "<?php echo $base_url.'/'.$modPath;?>/get_file_content.php?fpath="+myFile+"&repoName="+repoName+"&userName="+userName,
        type: "POST",
        data: {fpath : myFile, repoName : repoName, userName : userName},
        dataType: "text"
      });
      
      request.done(function(msg) {
        
        //alert(msg);

        var paraVal = msg.split('@@@@');

        $('#nfname').val(paraVal[0]);
        $('#fnameSha').val(paraVal[1]);
        CKEDITOR.instances['editor'].setData(paraVal[2]);
      });

      request.fail(function(jqXHR, textStatus) {
        alert( "Request failed: " + textStatus );
      });   
    });    
  }

  function showContent(myFile, userName, repoName){    
    //alert(myFile);alert('dddd');
    $('#edtFileName').val(myFile);
    $('#nfname').val(myFile);
    $('#getRefeURL').val(myFile);
    $('#getDelRefeURL').val(myFile);

    showRevissionsNew(myFile, userName, repoName);

    var request = $.ajax({
      url: "<?php echo $base_url.'/'.$modPath;?>/get_file_content.php?fpath="+myFile+"&repoName="+repoName+"&userName="+userName,
      type: "POST",
      data: {fpath : myFile, repoName : repoName, userName : userName},
      dataType: "text"
    });
    request.done(function(msg) {      
      //alert(msg);
      var paraVal = msg.split('@@@@');
      $('#nfname').val(paraVal[0]);
      $('#fnameSha').val(paraVal[1]);
      CKEDITOR.instances['editor'].setData(paraVal[2]);
    });         

    request.fail(function(jqXHR, textStatus) {
      alert( "Request failed: " + textStatus );
    });   
  }

  /* CLEAR TEXT AREA BOX */
  function clearTeaxtarea(){    
    $("#editor").html('');
  }

  /* COMMIT CHANGES ON GIT BRANCH */
  function commitFileChanges(){
    var commitText = $('#pushcomment').val();
    var filePath = $('#edtFileName').val();
    var content = $('#editor').val();

    var request = $.ajax({
      url: "<?php echo $base_url.'/'.$modPath;?>/update_file_content.php",
      type: "POST",
      data: {commit_text : commitText, filePath : filePath, content : content},
      dataType: "html"
    });

    request.done(function(msg) {      
      //alert(msg);      
      alert('Code commited successfully');      
    });

    request.fail(function(jqXHR, textStatus) {
      alert( "Request failed: " + textStatus );
    });
}


function switch_repository(reponame, userName, repoName){    
    var repName = $('#validationCustom03').val();
    //return false;
    var request = $.ajax({
      url: "<?php echo $base_url.'/'.$modPath;?>/set_repository.php?reponames="+repName+"&repoName="+repoName+"&userName="+userName,
      type: "POST",
      data: {reponame : reponame, repoName : repoName, userName : userName},
      dataType: "text"
    });
    
    request.done(function(msg) {      
      //alert(msg);    
      $('.php-file-tree').html(msg);
    });

    request.fail(function(jqXHR, textStatus) {
      alert( "Request failed: " + textStatus );
    });
}

function show_pages(sdiv){
  
  if(sdiv==1){
    $('#preview-wrapper').show();
    $('#revision_list').hide();    
    $('#create_file').show();    
  }else if(sdiv==2){
    $('#create_file').hide();
    $('#revision_list').show();
    $('#preview-wrapper').hide();
  }else if(sdiv==4){
    $('#create_file').show();
    $('#revision_list').hide();
    $('#preview-wrapper').show();
  }
}

// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btn.onclick = function() {
  modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}



</script>

<style type="text/css">
  #preview-wrapper {
      height: 350px !important;
      overflow: auto;
  }
    .timeline_commnet {
        overflow-y: scroll;
    height: 350px;
    }
  .timeline_commnet ul.timeline {
    list-style-type: none;
    position: relative;
    list-style-type: none;
    position: relative;
    padding-right: 20px;
  
}
.timeline_commnet ul.timeline:before {
    content: ' ';
    background: #d4d9df;
    display: inline-block;
    position: absolute;
    left: 29px;
    width: 2px;
    height: 100%;
    z-index: 400;

    left: 25px;
}
.timeline_commnet ul.timeline > li {
    margin: 20px 0;
        margin-left: 46px !important;
    padding-left: 20px;
    border: 1px solid #e1e4e8;
    margin-left: 17px;
    display: flex;
}
.timeline_commnet ul.timeline > li a {color: #444d56; font-size: 14px;}
.timeline_commnet ul.timeline > li:before {
    content: ' ';
    background: white;
    display: inline-block;
    position: absolute;
    border-radius: 50%;
    border: 3px solid #c7c7c7;
    left: 21px;
    width: 10px;
    height: 10px;
    z-index: 400;
}
  select#validationCustom03 {
    width: 200px;
  }
  .formPanelBody {
    /* border: solid 1px #ddd; */
    padding: 6px 15px;
    margin: 0px 6px 6px 0px;
    display: block;
    overflow: auto;
    margin-left: 0;
    background: #f3f3f3;
  }

  .comments-container {
  margin: 60px auto 15px;
  width: 768px;
}

.comments-container h1 {
  font-size: 36px;
  color: #283035;
  font-weight: 400;
}

.comments-container h1 a {
  font-size: 18px;
  font-weight: 700;
}

.comments-list {
  margin-top: 30px;
  position: relative;
}

/**
 * Lineas / Detalles
 -----------------------*/
.comments-list:before {
  content: '';
  width: 2px;
  height: 100%;
  background: #c7cacb;
  position: absolute;
  left: 32px;
  top: 0;
}

.comments-list:after {
  content: '';
  position: absolute;
  background: #c7cacb;
  bottom: 0;
  left: 27px;
  width: 7px;
  height: 7px;
  border: 3px solid #dee1e3;
  -webkit-border-radius: 50%;
  -moz-border-radius: 50%;
  border-radius: 50%;
}

.reply-list:before, .reply-list:after {display: none;}
.reply-list li:before {
  content: '';
  width: 60px;
  height: 2px;
  background: #c7cacb;
  position: absolute;
  top: 25px;
  left: -55px;
}


.comments-list li {
  margin-bottom: 15px;
  display: block;
  position: relative;
}

.comments-list li:after {
  content: '';
  display: block;
  clear: both;
  height: 0;
  width: 0;
}

.reply-list {
  padding-left: 88px;
  clear: both;
  margin-top: 15px;
}
/**
 * Avatar
 ---------------------------*/
.comments-list .comment-avatar {
  width: 65px;
  height: 65px;
  position: relative;
  z-index: 99;
  float: left;
  border: 3px solid #FFF;
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
  -webkit-box-shadow: 0 1px 2px rgba(0,0,0,0.2);
  -moz-box-shadow: 0 1px 2px rgba(0,0,0,0.2);
  box-shadow: 0 1px 2px rgba(0,0,0,0.2);
  overflow: hidden;
}

.comments-list .comment-avatar img {
  width: 100%;
  height: 100%;
}

.reply-list .comment-avatar {
  width: 50px;
  height: 50px;
}

.comment-main-level:after {
  content: '';
  width: 0;
  height: 0;
  display: block;
  clear: both;
}
/**
 * Caja del Comentario
 ---------------------------*/
.comments-list .comment-box {
  width: 680px;
  float: right;
  position: relative;
  -webkit-box-shadow: 0 1px 1px rgba(0,0,0,0.15);
  -moz-box-shadow: 0 1px 1px rgba(0,0,0,0.15);
  box-shadow: 0 1px 1px rgba(0,0,0,0.15);
}

.comments-list .comment-box:before, .comments-list .comment-box:after {
  content: '';
  height: 0;
  width: 0;
  position: absolute;
  display: block;
  border-width: 10px 12px 10px 0;
  border-style: solid;
  border-color: transparent #FCFCFC;
  top: 8px;
  left: -11px;
}

.comments-list .comment-box:before {
  border-width: 11px 13px 11px 0;
  border-color: transparent rgba(0,0,0,0.05);
  left: -12px;
}

.reply-list .comment-box {
  width: 610px;
}
.comment-box .comment-head {
  background: #FCFCFC;
  padding: 10px 12px;
  border-bottom: 1px solid #E5E5E5;
  overflow: hidden;
  -webkit-border-radius: 4px 4px 0 0;
  -moz-border-radius: 4px 4px 0 0;
  border-radius: 4px 4px 0 0;
}

.comment-box .comment-head i {
  float: right;
  margin-left: 14px;
  position: relative;
  top: 2px;
  color: #A6A6A6;
  cursor: pointer;
  -webkit-transition: color 0.3s ease;
  -o-transition: color 0.3s ease;
  transition: color 0.3s ease;
}

.comment-box .comment-head i:hover {
  color: #03658c;
}

.comment-box .comment-name {
  color: #283035;
  font-size: 14px;
  font-weight: 700;
  float: left;
  margin-right: 10px;
}

.comment-box .comment-name a {
  color: #283035;
}

.comment-box .comment-head span {
  float: left;
  color: #999;
  font-size: 13px;
  position: relative;
  top: 1px;
}

.comment-box .comment-content {
  background: #FFF;
  padding: 12px;
  font-size: 15px;
  color: #595959;
  -webkit-border-radius: 0 0 4px 4px;
  -moz-border-radius: 0 0 4px 4px;
  border-radius: 0 0 4px 4px;
}

.comment-box .comment-name.by-author, .comment-box .comment-name.by-author a {color: #03658c;}
.comment-box .comment-name.by-author:after {
  content: 'autor';
  background: #03658c;
  color: #FFF;
  font-size: 12px;
  padding: 3px 5px;
  font-weight: 700;
  margin-left: 10px;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
}
</style>