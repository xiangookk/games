<?php

if(isset($_FILES['userfile'])){
	$uploadfile='../files/'.basename($_FILES['userfile']['name']);
	if(move_uploaded_file($_FILES['userfile']['tmp_name'],$uploadfile)){
		echo '上传成功';
	}else{
		echo '上传失败';
	}
}
if($_GET["type"]=="getData"){
	$resposeStr='[';
	$dir='../files/';
	$dh=opendir($dir);
	$dirOut='files/';
	while($filename=readdir($dh))
	{
		if($filename!="."&&$filename!=".."){
			$filesize=round(filesize($dir.$filename)/1024/1024,2);
			$filetime=date('Y-m-d H:i:s',filemtime($dir.$filename));
			$resposeStr=$resposeStr.'{ "fileName": "'.$filename.'", "fileUrl": "'.$dirOut.$filename.'", "fileSize": "'.$filesize.'MB", "fileTime": "'.$filetime.'"},';
		}
	}
	$resposeStr=substr($resposeStr,0,strlen($resposeStr)-1).']';
	echo $resposeStr;
}
?>