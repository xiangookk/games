<?php

$do_type=$_POST["do_type"];
$cname=$_POST["cname"];
$type=$_POST["type"];
$score=$_POST["score"];

$number=$_POST["number"];
$pass=$_POST["pass"];

//$con = mysql_connect("127.0.0.1","zjwdb_6130248","Sxx123456");
$con = mysql_connect("127.0.0.1","root","123");
if (!$con)
{	
	die('Could not connect: ' . mysql_error());
}

mysql_select_db("zjwdb_6130248", $con);
mysql_query("SET NAMES 'utf8'",$con);

if ($do_type==1)//新增或更新
{
	$sql="select * from clientuser where GameType='".$type."' and CName='".$cname."'";
	$result = mysql_query($sql);
	$count=mysql_num_rows($result);

	if($count>0)
	{
		if($score > mysql_result($result,0,'HighScore')){
			$sql="update clientuser set HighScore=".$score.",LastUpdateTime=now() where GameType='".$type."' and CName='".$cname."'";
		}else{
			return;
		}		
	}
	else
	{
		$sql="INSERT INTO clientuser (CName,GameType, HighScore, CreateTime,LastUpdateTime) 
			  VALUES ('".$cname."', '".$type."',".$score.", now(),now())";
	}
		
	//print_r($sql);
    mysql_query($sql);
}
elseif ($do_type==2)//获取排行数据
{
	
	$result = mysql_query("SELECT * FROM clientuser where GameType='".$type."' order by HighScore desc limit 0, 10");

	echo "<table border='0'>
	<tr>
	<th style='width:50px'>名次</th>
	<th>用户名</th>
	<th style='text-align:right'>分数</th>
	</tr>";

	while($row = mysql_fetch_array($result))
	  {
		  static $num=1;
		  echo "<tr>";
		  echo "<td>".$num."</td>";
		  echo "<td style='width:135px'>".$row['CName']."</td>";
		  echo "<td style='width:80px;text-align:right'>".$row['HighScore']."</td>";
		  echo "</tr>";
		  $num++;
	  }
	echo "</table>";
}
elseif($do_type==8)//查询ofo数据
{
	$sql="select password from ofodata where Number='".$number."' ";
	$result = mysql_query($sql);
	$pass = mysql_fetch_row($result);

	echo $pass[0];
}
elseif($do_type==9)//保存ofo数据
{
	$sql="select * from ofodata where Number='".$number."' ";
	$result = mysql_query($sql);
	$count=mysql_num_rows($result);

	if($count>0)
	{		
		$sql="update ofodata set Password=".$pass." where Number='".$number."'";
	}
	else
	{
		$sql="INSERT INTO ofodata (Number,Password, type) 
			  VALUES ('".$number."', '".$pass."','".$type."')";
	}
		
	//print_r($sql);
	mysql_query($sql);
    $res = mysql_affected_rows();
	echo $res;
}

mysql_close($con);
?>