<?php	

global $base_url;
echo $bname = trim($_REQUEST['bname']);

die('1');
/* FINAL CODE TO CREATE NEW BRANCH  */
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/repos/nileshyashco/demo/git/refs');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"username\":\"nileshyashco\",\"password\":\"Yashco123\",\"ref\":\"refs/heads/".$bname."\",\"sha\":\"d439b4e7cf531a1f386fc2104f465653a24c2784\"}");
curl_setopt($ch, CURLOPT_POST, 1);
$headers = array();
$headers[] = 'Content-Type: application/json';
$headers[] = 'Authorization: Basic bmlsZXNoeWFzaGNvOllhc2hjbzEyMw==';
$headers[] = 'User-Agent: master';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close ($ch);
echo json_decode($result);
/*  BRANCH CREATION CODE IS END HERE */
?>