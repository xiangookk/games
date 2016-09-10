<?php
/*echo '$_POST接收:<br/>'; 
print_r($_POST); 
  
echo '$GLOBALS[\'HTTP_RAW_POST_DATA\']接收:<br/>'; 
print_r($GLOBALS['HTTP_RAW_POST_DATA']); 

echo 'php://input接收:<br/>'; 
$data = file_get_contents('php://input'); 
print_r(urldecode($data));
*/
$do_type=$_POST["do_type"];
$cname=$_POST["cname"];
$type=$_POST["type"];
$score=$_POST["score"];

$con = mysql_connect("mysql.2freehosting.com","u109760551_sxx","86641813");
if (!$con)
  die('Could not connect: ' . mysql_error());

mysql_select_db("u109760551_game", $con);
mysql_query("SET NAMES 'utf8'",$con);

if ($do_type==1)
{
	$sql="select * from clientuser where GameType='".$type."' and CName='".$cname."'";
	$result = mysql_query($sql);
	$count=mysql_num_rows($result);

	if($count>0)
	{
		if($score > mysql_result($result,0,'HighScore')){
			$sql="update clientuser set HighScore=".$score.",LastLoginTime=now() where GameType='".$type."' and CName='".$cname."'";
		}else{
			return;
		}		
	}
	else
	{
		$sql="INSERT INTO clientuser (CName,GameType, HighScore, CreateTime,LastLoginTime) 
			  VALUES ('".$cname."', '".$type."',".$score.", now(),now())";
	}
		
	//print_r($sql);
    mysql_query($sql);
}
elseif ($do_type==2)
{
	
	$result = mysql_query("SELECT * FROM clientuser where GameType='".$type."' order by HighScore desc");

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
}else
{}

mysql_close($con);
?>