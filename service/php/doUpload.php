<?php

if(isset($_FILES['userfile'])){
	$uploadPath='../files/upload/';
	if(!file_exists($uploadPath)){
		mkdir ($uploadPath,0777,true);
	}
	$uploadfile=$uploadPath.$_FILES['userfile']['name'];
	if(move_uploaded_file($_FILES['userfile']['tmp_name'],iconv("utf-8","gb2312",$uploadfile))){
		echo '上传成功';
	}else{
		echo '上传失败';
	}
}
if(isset($_GET["type"])&&$_GET["type"]=="getData"){
	$resposeStr='[';
	$dir='../files/upload/';
	$dh=opendir($dir);
	$dirOut='files/upload/';
	while($filename=readdir($dh))
	{
		if($filename!="."&&$filename!=".."){
			if(is_dir($dir.$filename))continue;
			$filesize=round(filesize($dir.$filename)/1024/1024,2);
			$filetime=date('Y-m-d H:i:s',filemtime($dir.$filename));

			$filename=iconv("gb2312","utf-8",$filename);
			$resposeStr=$resposeStr.'{ "fileName": "'.$filename.'", "fileUrl": "'.$dirOut.$filename.'", "fileSize": "'.$filesize.'MB", "fileTime": "'.$filetime.'"},';
		}
	}
	$resposeStr=substr($resposeStr,0,strlen($resposeStr)-1).']';
	echo $resposeStr;
}
?>