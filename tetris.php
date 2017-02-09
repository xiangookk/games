<?php 
$userName="游客";
if (isset($_COOKIE["user"]))
{
	$userName=$_COOKIE["user"];
    echo "欢迎你， " . $userName. "!<br />";
}
else
 {
      $userName="游客".rand(100000,999999);
      setcookie("user", $userName, time()+3600*24*7);
      echo "欢迎你， （新人）".$userName."!<br />";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>俄罗斯方块</title>
<script src="jquery-1.9.1.min.js"></script>
<style>
    body{
	    text-align:center;
	    background-color:#1B272C;
        font-family:LiSu,KaiTi;
        font-size:18px;
        padding-top:40px;
        color:#fff;
	    }
    #frame{
        box-shadow:0px 0px 15px rgba(222, 226, 237, 0.84);
        border-radius:6px;
        -o-box-shadow:0px 0px 15px rgba(222, 226, 237, 0.84);
        -o-border-radius:6px;
        -moz-transition:-moz-transform 2s; 
        -webkit-transition:-webkit-transform 2s;
        -o-transition:-o-transform 2s;

        padding:2px;
        background-color:#ccc;
	    margin-left:auto;
	    margin-right:auto;
	    width:339px;
	    height:403px;
	    border:solid 2px #CCCCCC;
	    }
    #frametitle{
        font-weight:600;
	    width:100%;
	    height:20px;
	    background-color:#CCCCCC;
        color:black;
	    }
    #framebody {
        height:383px;
        background-color:#1B272C;
    }
    #bodyleft {
        width:65%;
        height:99.5%;
        float:left;
        border-right:solid 1px #CCCCCC;
        margin-right:-1px;
        position:relative;
    }
    #bodyright {
        width:35%;
        height:100%;
        float:right;
        border:solid 0px green;
    }
    #menu {
        width:100%;
        height:8%;
        border:solid 0px #CCCCCC;
    }
    #menu input {
        height:23px;
        font-size:15px;
        margin-top:4px;
        background-color:#1B272C;
        border:solid 1px #CCCCCC;
        color:#fff;
    }
    #menu input:hover {
        background-color:rgba(204, 204, 204, 0.34);
        cursor:pointer;
    }
    #main {
        border-top:solid 1px #CCCCCC ;
        width:100%;
        height:92%;
        background-color:#000;
    }
    #next {
        margin-left:auto;margin-right:auto;
        border:solid 1px #CCCCCC;
        width:88px;
        height:88px;
        margin-top:30px;
        background-color:#000;
    }
    .box {
        border-right: dashed 1px #1B272C;
        border-bottom: dashed 1px #1B272C;
        width: 21px;
        height: 21px;
        float: left;
    }
    #score,#leve {
        font-size:13px;
    }
    #score span,#leve span {
        font-weight:600;
        font-size:16px;
        padding-top:8px;
    }
    #gametype {
        font-size:13px;
    }
    .showMsg {
         position:absolute;
         width:40px;
         height:7px;
         border-radius:15px;
         border:solid 2px #ccc;
         background-color:#010424;
         padding-top:20px;
         top:200px;
         left:500px;
         font-size:35px;
         font-family:KaiTi,LiSu; 
         font-weight:bold;
         color:red;
         display:none;
    }
    .showScore {
         position:absolute;
         width:100px;
         height:30px;
         top:156px;
         left:60px;
         font-size:35px;
         font-family:KaiTi; 
         font-weight:bold;
         border:solid 0px #fff;
         opacity:0.0
    }
	#sort{
		 position:absolute;
         width:240px;
         height:400px;
         border-radius:5px;
         border:solid 2px #ccc;
         background-color:#010424;
         padding:20px;
         top:100px;
         left:50px;
         font-size:15px;
         font-family:KaiTi,LiSu; 
         font-weight:bold;
	}
	td{
		padding:6px;
	}
</style>
<!--<script src="http://pv.sohu.com/cityjson?ie=utf-8"></script>-->
<script type="text/javascript"> 
//document.write(returnCitySN["cip"]+' '+returnCitySN["cname"]);
        //使用HTML5的Canvas绘制喇叭
        var cxt;
        var musicStatus;
        var musicOpen = function () {
            cxt.beginPath();
            cxt.clearRect(0, 0, 50, 50);
            cxt.moveTo(15, 15);
            cxt.lineTo(6, 15);
            cxt.lineTo(6, 25);
            cxt.lineTo(15, 25);
            cxt.lineTo(15, 15);
            cxt.lineTo(25, 8);
            cxt.lineTo(25, 32);
            cxt.lineTo(15, 25);
            cxt.stroke();
            cxt.moveTo(30, 15);
            cxt.arc(16, 20, 15, -0.2, 0.2, false);
            cxt.stroke();
            cxt.moveTo(35, 12);
            cxt.arc(22, 20, 15, -0.5, 0.5, false);
            cxt.stroke();
            cxt.moveTo(39, 10);
            cxt.arc(28, 20, 15, -0.8, 0.8, false);
            cxt.stroke();
            musicStatus = true;
        }
        var musicClose = function () {
            cxt.beginPath();
            cxt.clearRect(0,0,50,50);
            cxt.moveTo(15, 15);
            cxt.lineTo(6, 15);
            cxt.lineTo(6, 25);
            cxt.lineTo(15, 25);
            cxt.lineTo(15, 15);
            cxt.lineTo(25, 8);
            cxt.lineTo(25, 32);
            cxt.lineTo(15, 25);
            cxt.stroke();
            cxt.moveTo(6, 6);
            cxt.lineTo(35,35);
            cxt.stroke();
            musicStatus = false;
        }
        
        //声音文件

        //锁定
        var locked = false;
        var audioMove = "Move.wav";
        var audioCrash = "Crash.wav";
        var audioBgmic = "Bgmic.wav";
        //播放音效
        var au = document.createElement("audio") ;
        var PlayAudio = function (url) {
            if (au.ended)
            { locked = false; }
            if ((!locked || url == audioCrash) && musicStatus) {
                locked = true;
                au.src = url;
                au.play();
            }
        }
        //播放背景音
        var bgau = document.createElement("audio");
        
        var BgAudio = function (crl) {
            if (crl == "play")
                bgau.play();
            else
                bgau.pause();
        };
       
       
        
        //基点
        var baseDot = [3, 0];
           //“L”形方块四种形态 
           var L = [
                   [[0, 0], [0, 1], [1, 1], [2, 1]],
                   [[1, 0], [1, 1], [0, 2], [1, 2]],
                   [[0, 0], [1, 0], [2, 0], [2, 1]],
                   [[0, 0], [1, 0], [0, 1], [0, 2]]
           ];
           //“┻”形方块 
           var K = [
                   [[1, 0], [0, 1], [1, 1], [2, 1]],
                   [[1, 0], [0, 1], [1, 1], [1, 2]],
                   [[0, 0], [1, 0], [2, 0], [1, 1]],
                   [[0, 0], [0, 1], [1, 1], [0, 2]]
           ];
        　　//“N”形方块
           var N = [
                   [[0, 0], [1, 0], [1, 1], [2, 1]],
                   [[1, 0], [0, 1], [1, 1], [0, 2]]
           ];
        　 //“田”字形方块
           var O = [
                   [[0, 0], [1, 0], [0, 1], [1, 1]]
           ];
           //“｜”条形方块
           var X = [
                   [[0, 0], [1, 0], [2, 0], [3, 0]],
                   [[1, 0], [1, 1], [1, 2], [1, 3]]
           ];
          //===========以下高级模式=============
           var Q = [
                   [[1, 0]]
           ];
           var B = [
                   [[0, 0],[1, 0]],
                   [[0, 0],[0, 1]]
           ];
           //“┗”形方块
           var V = [
                   [[0, 0], [0, 1], [1, 1]],
                   [[1, 0], [0, 1], [1, 1]],
                   [[0, 0], [1, 0], [1, 1]],
                   [[0, 0], [1, 0], [0, 1]]
           ];
           //“凹”字形方块
           var U = [
                   [[0, 0], [0, 1], [1, 1], [2, 1], [2, 0]],
                   [[0, 0], [1, 0], [1, 1], [1, 2], [0, 2]],
                   [[0, 0], [1, 0], [2, 0], [0, 1], [2, 1]],
                   [[0, 0], [1, 0], [0, 1], [0, 2], [1, 2]]
           ];
        var Block1 = [L, K, N, O, X];
        var Block2 = [L, K, N, O, X, Q, B, V, U];
        var Blocks;
        //方块单格背景图片位置
        var bgPositionXs = [0, -23, -46, -69, -91, -114, -137], bgPositionY = -66, _bgPositionX, bgPositionX;
        var score = 0;//得分
        var currentBlock = new Array();//当前方块
        var nextBlock = new Array();//next
        var tempBlock;
        var isEnd = true;//当前方块是否已到底或碰上其它方块。
        var timeoutId;
        var isrun = false;//开始、暂停
        var downOver = true;//下降后的事情是否处理完（消满格层比较花时间）
        var g = 0, h = 0;//当前方块哪个哪种形态
        var _g = 0; _h = 0;//next
        var speed = 1000; //初始频率1000毫秒下降一次，最低100毫秒
        var leveScore = 1000;//每级分数
        //预加载下一个方块
        //createNextBlock();
        
        var run = function () {          
            if (isEnd&&isrun) {             
                createNewBlock();
            }
            BlockDown();
        }
        //创建一个新方块
        var createNewBlock = function () {
            currentBlock = nextBlock;
            g = _g; h = _h;
            bgPositionX = _bgPositionX;
            isEnd = false;

            downOver = true;
            baseDot = [3, 0];
            for (var i = 0; i < currentBlock.length; i++)
            {
                currentBlock[i][0]=currentBlock[i][0] - _x + baseDot[0];
                currentBlock[i][1]=currentBlock[i][1] - _y + baseDot[1];
            }
            //绘制当前
            createActBlock();
            //创建下一个方块
            createNextBlock();
        }
        //创建下一个新方块
        var _x=0, _y=0;
        var createNextBlock = function () {
            //清除Next整个框
            for (var i = 0; i < 4; i++) {
                for (var j = 0; j < 4;j++)
                    clearNextBox(i,j)
            }
            nextBlock = new Array();
            //随机背景色
            _bgPositionX = bgPositionXs[Math.floor(Math.random() * 7)];
            //随机一种方块
            _g = Math.floor(Math.random() * Blocks.length);
            //随机一种形态（一般情况是上下左右四种形态，也有特殊情况如 “田”字形只有一种）
            _h = Math.floor(Math.random() * Blocks[_g].length);
            var newBlock = Blocks[_g][_h];
            //偏移显示（为了居中好看）            
            _x = 1; _y = 1;
            //“｜”条形方块特殊处理
            if (_g == 4)
            {
                if (_h == 0)
                    _x = 0;
                if (_h == 1)
                    _y = 0;
            }
            //偏移后的方块
            for (var i = 0; i < newBlock.length; i++) {
                var cur = new Array();
                cur.push(newBlock[i][0] + _x);
                cur.push(newBlock[i][1] + _y);
                nextBlock.push(cur);
            }
            //绘制
            for (var i = 0; i < nextBlock.length; i++) {
                createNextBox(nextBlock[i][0], nextBlock[i][1])
            }
        }
        //绘制一个激活单格
        var createActBox = function (x, y) {
            if (x < 0 || y < 0) return;
            var i = y * 10 + x;
            $(".box:eq(" + i + ")").css({ "width": "22px", "height": "22px", "background": "url(BlockBg.gif)", "background-size": "160px 110px", "backgroundPosition": bgPositionX + "px " + bgPositionY + "px", "border": "solid 0px #CCCCCC" });
            $(".box:eq(" + i + ")").attr("status", "active");
            //$(".box:eq(" + i + ")").html("<span style='color:yellow'>N</span>");
        }
        //绘制下一个单格
        var createNextBox = function (x, y) {
            var i = y * 4 + x;
            $("#next>.box:eq(" + i + ")").css({ "width": "22px", "height": "22px", "background": "url(BlockBg.gif)", "background-size": "160px 110px", "backgroundPosition": _bgPositionX+"px "+bgPositionY+"px", "border": "solid 0px #CCCCCC" });
            $("#next>.box:eq(" + i + ")").attr("status", "active");
            // $(".box:eq(" + i + ")").html("<span style='color:yellow'>act</span>"); 
        }
        //绘制一个固定单格
        var createFixBox = function (x, y,bgPosition) {
            var i = y * 10 + x;
            $(".box:eq(" + i + ")").css({ "width": "22px", "height": "22px", "background": "url(BlockBg.gif)", "background-size": "160px 110px", "backgroundPosition": bgPosition, "border": "solid 0px #CCCCCC" });
            $(".box:eq(" + i + ")").attr({ "status": "fixed", "recover": "true" });
           // $(".box:eq(" + i + ")").html("<span style='color:red'>F</span>");
        }
        //绘制一个方块
        var createActBlock = function () {
            for (var i = 0; i < currentBlock.length; i++)
            {           
                createActBox(currentBlock[i][0], currentBlock[i][1])
            }
            if(musicStatus)
            PlayAudio(audioMove);
        }
        //改变一个单格状态为固定
        var changeBoxStatus = function (x, y) {
            var i = y * 10 + x;
            $(".box:eq(" + i + ")").attr("status", "fixed");
            //$(".box:eq(" + i + ")").html("<span style='color:blue'>F</span>");
        }
        //改变一个方块状态为固定
        var changeBlockStatus = function () {
            if (!downOver) return;
            downOver = false;
            for (var i = 0; i < currentBlock.length; i++) {
                changeBoxStatus(currentBlock[i][0], currentBlock[i][1])
            }
            if (musicStatus)
            PlayAudio(audioCrash);
            //清除满格的行
            var rows = new Array();
            for (var i = baseDot[1]; i < baseDot[1]+4; i++) {
                var isfull = true;
                for (var j = 0; j < 10; j++) {
                    var n = i * 10 + j;
                    if ($(".box:eq(" + n + ")").attr("status") != "fixed") {
                        isfull = false;
                        break;
                    }       
                }
                if (isfull)
                {
                    for (var k = 0; k < 10; k++)
                    {
                        clearActBox(k,i);
                    }
                    rows.push(i);
                }
            }
            if (rows.length > 0) {
                var preBlocks = $(".box:lt(" + (rows[(rows.length - 1)] * 10) + ")[status='fixed']");
                //消除满格上的方块          
                preBlocks.each(function () {
                    var index = $(this).index();
                    var y1 = Math.floor(index / 10);
                    var x1 = index % 10;
                    var downRows=0; //此单格需要下降行数
                    for (var r = rows.length-1; r >=0; r--)
                    {
                        if (index < rows[r] * 10+10)
						{downRows++;}
                        else
						{break;}
                    }
                    createFixBox(x1, y1 + downRows, $(this).css("backgroundPosition"));
                    if($(this).attr("recover")!="true")
                    clearActBox(x1, y1);
                });
                $(".box:lt(" + (rows[(rows.length - 1)] * 10 + 10) + ")[status='fixed']").removeAttr("recover");
                              
                //计算分数
                var addsco;
                switch (rows.length)
                {
                    case 1: addsco = 100;  break;
                    case 2: addsco = 300; break;
                    case 3: addsco = 600; break;
                    case 4: addsco = 1000; break;
                }
                score += addsco;

                speed = 1000 - Math.floor(score / leveScore) * 100;
                if (speed < 100) speed = 100;
                clearInterval(timeoutId);
                timeoutId = setInterval(run, speed);
                $("#addscore").css({ "top": "156px", "opacity": "1.0", "color": "green" }).html("+" + addsco).animate({ "top": "130px", "opacity": "0.0" }, 1500);
                $("#score>span").text(score);
                $("#leve>span").text((1000-speed)/100+1);
            }
            for (var i = 0; i < currentBlock.length; i++) {
                if (currentBlock[i][1] <= 0)
                {
                    clearInterval(timeoutId);
                    isrun = false; 
                    $("#gametype input").removeAttr("disabled");
					//得分存入数据库
				    $.post("doPost.php",{cname:getCookie('user'),type:'tetris',score:score,do_type:1});
					
                    var lev = score / leveScore;
                    var msg = "";
                    if (lev <= 3) msg = "菜鸟，加油哦！";
                    if (lev > 3 && lev <= 6) msg = "资质一般，再接再厉！";
                    if (lev > 6 && lev < 9) msg = "高手，我看好你！";
                    if (lev >=9) msg = "大神，膜拜！";
                    showMsg(msg);
                    break;
                }
            }
            isEnd = true;
        }
        //清除一个单格
        var clearActBox = function (x, y) {
            if (x < 0 || y < 0) return;
            var i = y * 10 + x;
            $(".box:eq(" + i + ")").css({ "width": "21px", "height": "21px", "background": "", "borderRight": "dashed 1px #1B272C", "borderBottom": "dashed 1px #1B272C", "borderLeft": "0px", "borderTop": "0px" });
            $(".box:eq(" + i + ")").attr("status", "null");
            $(".box:eq(" + i + ")").removeAttr("recover");
            //$(".box:eq(" + i + ")").html("<span style='color:gray'></span>");
        }
        //清除一个单格(Next)
        var clearNextBox = function (x, y) {
            var i = y * 4 + x;
            $("#next>.box:eq(" + i + ")").css({ "width": "21px", "height": "21px", "background": "", "backgroundColor": "#000", "borderRight": "dashed 1px #1B272C", "borderBottom": "dashed 1px #1B272C", "borderLeft": "0px", "borderTop": "0px" });
            $("#next>.box:eq(" + i + ")").attr("status", "null");
            // $(".box:eq(" + i + ")").html("<span style='color:gray'>nul</span>");
        }   
        //清除一个方块
        var clearBlock = function (arry) {
            for (var i = 0; i < arry.length; i++) {
                    clearActBox(arry[i][0], arry[i][1]);
            }           
        }
        //清除所有方格
        var clearAllBlock = function () {
            $(".box").css({ "width": "21px", "height": "21px", "background": "", "backgroundColor": "#000", "borderRight": "dashed 1px #1B272C", "borderBottom": "dashed 1px #1B272C", "borderLeft": "0px", "borderTop": "0px" });
            $(".box").attr("status", "null");
        }
        //方块下降一格
        var BlockDown = function () {
            //下降前检查是否到底或碰上其它方块
            for (var i = 0; i < currentBlock.length; i++) {
                var n = (currentBlock[i][1] + 1) * 10 + currentBlock[i][0];
                if (currentBlock[i][1] == 15||$(".box:eq(" + n + ")").attr("status") == "fixed")
                {
                    changeBlockStatus();
                    return;
                }
            }

            baseDot[1] += 1;
            clearBlock(currentBlock);
            for (var i = 0; i < currentBlock.length; i++)
            {
                currentBlock[i][1] += 1;
            }
            createActBlock(currentBlock);
        }
        //方块下降到底
        var BlockDownToBottom = function () {
            clearBlock(currentBlock);
            while (true)
            {
                //下降前检查是否到底或碰上其它方块
                for (var i = 0; i < currentBlock.length; i++) {
                    var n = (currentBlock[i][1] + 1) * 10 + currentBlock[i][0];
                    if (currentBlock[i][1] == 15 || $(".box:eq(" + n + ")").attr("status") == "fixed") {               
                        createActBlock(currentBlock);
                        changeBlockStatus();
                        return;
                    }
                }
                baseDot[1] += 1;
                for (var i = 0; i < currentBlock.length; i++) {
                    currentBlock[i][1] += 1;
                }
            }
        }
        //方块左移一格
        var BlockLeft = function () {
            //左移前检查是否已固定
            for (var i = 0; i < currentBlock.length; i++) {
                var n = currentBlock[i][1] * 10 + currentBlock[i][0];
                if ($(".box:eq(" + n + ")").attr("status") == "fixed") {
                    return;
                }
            }
            //左移前检查是否已靠左边框
            for (var i = 0; i < currentBlock.length; i++) {
                var x = currentBlock[i][0];
                if (x == 0)
                    return;
            }
            //左移前检查是否已靠左边方块
            for (var i = 0; i < currentBlock.length; i++) {
                var n = currentBlock[i][1] * 10 + currentBlock[i][0]-1;
                if ($(".box:eq(" + n + ")").attr("status") == "fixed") {
                    return;
                }
            }
            baseDot[0] -= 1;
            clearBlock(currentBlock);
            for (var i = 0; i < currentBlock.length; i++) {
                currentBlock[i][0] -= 1;
            }
            createActBlock();
        }
        //方块右移一格
        var BlockRight = function () {
            //右移前检查是否已固定
            for (var i = 0; i < currentBlock.length; i++) {
                var n = currentBlock[i][1] * 10 + currentBlock[i][0];
                if ($(".box:eq(" + n + ")").attr("status") == "fixed") {
                    return;
                }
            }
            //右移前检查是否已靠右边框
            for (var i = 0; i < currentBlock.length; i++) {
                var x = currentBlock[i][0];
                if (x == 9)
                    return;
            }
            //右移前检查是否已靠右边方块
            for (var i = 0; i < currentBlock.length; i++) {
                var n = currentBlock[i][1] * 10 + currentBlock[i][0] + 1;
                if ($(".box:eq(" + n + ")").attr("status") == "fixed") {
                    return;
                }
            }
            baseDot[0] += 1;
            clearBlock(currentBlock);
            for (var i = 0; i < currentBlock.length; i++) {
                currentBlock[i][0] += 1;
            }
            createActBlock();
        }
        //变形
        var BlockUp = function () {    
            tempBlock = new Array();
            if (baseDot[0] < 0) return;
            h = (h < Blocks[g].length - 1) ? h+1 : 0;
            var newBlock = Blocks[g][h];
            for (var i = 0; i < newBlock.length; i++) {
                if ((newBlock[i][0] + baseDot[0]) > 9 || (newBlock[i][1] + baseDot[1]) > 15)
                    return;
                var cur = new Array();
                cur.push(newBlock[i][0] + baseDot[0]);
                cur.push(newBlock[i][1] + baseDot[1]);
                tempBlock.push(cur);
            }
            //检查是否有已固定
            for (var i = 0; i < tempBlock.length; i++) {
                var n = tempBlock[i][1] * 10 + tempBlock[i][0];
                if ($(".box:eq(" + n + ")").attr("status") == "fixed") {
                    return;
                }
            }
            clearBlock(currentBlock);
            currentBlock = tempBlock;
            //绘制
            createActBlock();
        }
        //控制
        $(document).keydown(function (e) {
            if (isrun && downOver) {
                var key = e.which;
                switch (key) {
                    case 37: BlockLeft(); break;
                    case 38: BlockUp(); break;
                    case 39: BlockRight(); break;
                    case 40: BlockDown(); break;
                    case 32: BlockDownToBottom(); break;
                }
            }
        });
        //显示消息
        var showMsg = function (m) {
            $("#msg").html(m).css("display", "block").animate({ "width": "400px", "height": "70px" }, 400);
        }
		//获取排名
		var getSort=function(){
			$.post("doPost.php",{do_type:2,type:'tetris'},function(d){$("#sort").html(d);});
		}
		//初始化数据
		var init=function(){
			$("#gametype input").attr("disabled", "disabled");
			$("#msg").css("display", "none");
			if ($("#gametype input:checked").val() == "普通")
				Blocks = Block1;
			else
				Blocks = Block2;
			score = 0;
			$("#score>span").text(score);
			speed = 1000;
			$("#leve>span").text(1);
			clearAllBlock();
			createNextBlock();
			createNewBlock();
			getSort();
		}
        $(function () {
            //加载背景网格
            for (var i = 0; i < 160; i++) {
                var box = $("<div class='box' status='null'></div>");
                $("#main").append(box);
            }
            //加载next网格
            for (var i = 0; i < 16; i++) {
                var box = $("<div class='box' status='null'></div>");
                $("#next").append(box);
            }

            //使用HTML5的Canvas绘制喇叭
            var c = document.getElementById("myCanvas");
            cxt = c.getContext("2d");
            cxt.strokeStyle = "#fff";
            //使游戏难度选择可用
            $("#gametype input").removeAttr("disabled");
            musicOpen();
            //if (confirm("是否加载背景音乐？（约1M）")) {
				bgau.loop = "loop";
				bgau.autoplay = "autoplay";
				bgau.src = audioBgmic;
            //}
            //声音控制
            $("#myCanvas").click(function () { if (musicStatus) { musicClose(); BgAudio("pause"); } else { musicOpen(); BgAudio("play"); } });
            //单选按钮3D旋转
            var deg = 360;
            $(":radio").change(function () { $("#frame").css({ "-moz-transform": "rotateY(" + deg + "deg)", "-webkit-transform": "rotateY(" + deg + "deg)", "-o-transform": "rotateY(" + deg + "deg)" }); deg = deg == 360 ? 0 : 360 });
            $("input[value*='开始']").click(function () {
                if (!isrun) {
                    if (isEnd) {
                        init();
                    }
                    isrun = true;
                    timeoutId = setInterval(run, speed);
                } else {
                    isrun = false;
                    clearInterval(timeoutId);
                }
                $(this).blur();
            });          
			getSort();
        });
function getCookie(name)
{
	var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
	if(arr=document.cookie.match(reg))
	{return decodeURIComponent(arr[2]);}
	else
	{return null;}
}
</script>
</head>
<body>
    <bgsound id="snd" loop="1"/>
	<div style="height:42px;width:160px; margin-left:auto;margin-right:auto; ">
	   <div style="height:30px;width:100px;padding-top:10px;float:left; ">声音控制：</div>
	   <canvas id="myCanvas" width="50" height="40" style="cursor:pointer; float:left; "  title="点击切换">您的浏览器不支持HTML5新特性</canvas>
	</div>
	<div id="frame">
	  <div id="frametitle">俄罗斯方块</div>
	  <div id="framebody">
		  <div id="bodyleft">
			<div id="menu">
				<input type="button" value="开始/暂停" />
			</div>
			<div id="main">
			</div>
			  <div id="addscore" class="showScore"></div>
		  </div>
		  <div id="bodyright">
			  <div id="next"></div>
			  <div id="score"><br />得分：<br /><span>0</span></div>
			  <div id="leve"><br />关卡：<br /><span>1</span></div><br />
			  <div id="gametype">
				  <input type="radio" name="type" id="pt"  value="普通" checked="checked"/><label for="pt">普通</label> 
				  <input type="radio" name="type" id="gj"  value="高级"/><label for="gj">高级</label>
			  </div>
		  </div>    
	  </div>
	</div>
	<div id="msg" class="showMsg"></div>
	<div id="sort"></div>
	<div><br /><a href="index.html">返回主页</a></div>
</body>
</html>