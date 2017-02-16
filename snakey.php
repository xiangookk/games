
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>贪吃蛇</title>
<script src="jquery-1.9.1.min.js"></script>
<style>
    body{
	    text-align:center;
	    background-color:#1B272C;
        font-family:LiSu,KaiTi;
        font-size:18px;
        padding-top:20px;
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
	    height:374px;
	    border:outset 2px #CCCCCC;
        position:relative;    
	    }
    #frametitle{
        font-weight:600;
	    width:100%;
	    height:20px;
	    background-color:#CCCCCC;
        color:black;
	    }
    #framebody {
        height:352px;
        background-color:#1B272C;
    }
    #bodyleft {
        height:100%;
        float:left;
        border-right:solid 1px #CCCCCC;
        margin-right:-1px;
        position:relative;
    }
    #bodyright {
        width:80px;
        height:100%;
        float:right;
        border:solid 0px green;
    }
    #main {
        border-top:solid 1px #CCCCCC ;
        width:100%;
        height:100%;
        background-color:#000;
    }
   
    .box {
        border-right: dashed 1px #1B272C;
        border-bottom: dashed 1px #1B272C;
        float: left;
    }
    #btnControl {
        width:60px;
        height:25px;
        font-size:16px;
        font-weight:bold;
        background-color:#1B272C;
        border:solid 1px #CCCCCC;
        color:#fff;
    }
    #btnControl:hover {
        background-color:rgba(204, 204, 204, 0.34);
        cursor:pointer;
    }
    #msg {
         position:absolute;
         width:40px;
         height:7px;
         border-radius:15px;
         border:solid 2px #ccc;
         background-color:#010424;
         padding-top:20px;
         top:140px;
         left:60px;
         font-size:35px;
         font-family:KaiTi,LiSu; 
         font-weight:bold;
         color:red;
         display:none;
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
<script type="text/javascript">    
    $(function () {  
        //使用HTML5的Canvas绘制喇叭
        var c = document.getElementById("myCanvas");
        var cxt = c.getContext("2d");
        cxt.strokeStyle = "#fff";
        //声音开关 true/false
        var musicStatus;
        //锁定
        var locked=false;
        var audioMove = "Move.wav";
        var audioCrash = "Crash.wav";
        //绘制喇叭打开状
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
        //绘制喇叭关闭状
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
        //创建audio
        var au = document.createElement("audio");
        //播放音效
        var playAudio = function (url) {
            if (au.ended)
            { locked = false; }
            if ((!locked||url==audioCrash) && musicStatus) {
                locked = true;
                au.src = url;
                au.play();    
            }       
        }    
        //声音控制
        $("#myCanvas").click(function () { musicStatus?musicClose():musicOpen();});


        //单格边长（包含边框）
        var boxSide = 22;
        //背景格行数
        var bgRow = 16;
        //背景格列数
        var bgCol = 18;
        //方格背景图像
        var boxBgImg = "BlockBg.gif"
        //背景图像调整后的大小
        var bgImgSize = boxSide * 7 + "px " + boxSide * 5 + "px";
        //背景图像一组X坐标值
        var bgImgPositionXs = [0, -boxSide, -boxSide * 2, -boxSide * 3, -boxSide * 4, -boxSide * 5, -boxSide * 6];
        //背景图像一组Y坐标值
        var bgImgPositionYs = [0, -boxSide, -boxSide * 2, -boxSide * 3, -boxSide * 4];
        //背景图像Y坐标值
        var bgPositionY = bgImgPositionYs[3];
        //背景图像X坐标值
        var bgPositionX = bgImgPositionXs[5];
        //食物Y坐标值
        var foodPositionY = bgImgPositionYs[1];
        //食物X坐标值
        var foodPositionX = bgImgPositionXs[0];
        //蛇身
        var snakeBody;
        //食物
        //var food = [];
        //前行方向
        var direction = "right";
        //计时器Id
        var timerId;
        //得分
        var score = 0;
        //等级
        var level = 1;
        //速度
        var speed = 500;
        //每一等级分数
        var levelScore = 3000;


        //计时器事件
        var timerEvent = function () {
            snakeMove();      
        }
        //绘制一个激活单格
        var createActBox = function (x, y) {
            if (x < 0 || y < 0) return;
            var i = y * bgCol + x;
            $(".box:eq(" + i + ")").css({ "width": boxSide + "px", "height": boxSide + "px", "background": "url(" + boxBgImg + ")", "background-size": bgImgSize, "backgroundPosition": bgPositionX + "px " + bgPositionY + "px", "border": "0px", });
            $(".box:eq(" + i + ")").attr("status", "active"); 
        }
        //绘制一个食物单格
        var createFoodBox = function (x, y) {
            if (x < 0 || y < 0) return;
            var i = y * bgCol + x;
            $(".box:eq(" + i + ")").css({  "background": "url(" + boxBgImg + ")", "background-size": bgImgSize, "backgroundPosition": foodPositionX + "px " + foodPositionY + "px"});
            $(".box:eq(" + i + ")").attr("status", "food");
        }
        //绘制蛇身
        var createSnakeBody = function () {
            for (var i = 0; i < snakeBody.length; i++)
            {
                createActBox(snakeBody[i][0], snakeBody[i][1]);
            }
        }
        //绘制一个随机食物
        var createRandFood = function () {
            var x = Math.floor(Math.random() * bgCol);
            var y = Math.floor(Math.random() * bgRow);
            var n=y*bgCol+x;
            if ($(".box:eq(" + n + ")").attr("status") == "active") {
                createRandFood();
                return;
            }
            foodPositionX = bgImgPositionXs[Math.floor(Math.random() * bgImgPositionXs.length)];
            createFoodBox(x,y);
        }
        //清除一个单格
        var clearActBox = function (x, y) {
            if (x < 0 || y < 0) return;
            var i = y * bgCol + x;
            $(".box:eq(" + i + ")").css({ "width": boxSide - 1 + "px", "height": boxSide - 1 + "px", "background": "", "borderRight": "dashed 1px #1B272C", "borderBottom": "dashed 1px #1B272C" });
            $(".box:eq(" + i + ")").attr("status", "null");
        }
        //清除所有单格
        var clearAllBox = function () {
            $(".box").css({ "width": boxSide - 1 + "px", "height": boxSide - 1 + "px", "background": "", "borderRight": "dashed 1px #1B272C", "borderBottom": "dashed 1px #1B272C", "borderLeft": "0px", "borderTop": "0px" });
            $(".box").attr("status", "null");
        }              
        //蛇身前行一格
        var snakeMove = function () {
            //移动前清除最后一个单格
            clearActBox(snakeBody[snakeBody.length - 1][0], snakeBody[snakeBody.length - 1][1]);
            //除蛇头方格外，每个方格往前移动一格,从最后开始才能保证正确
            for (var i = snakeBody.length - 1; i > 0; i--) {
                snakeBody[i][0] = snakeBody[i - 1][0];
                snakeBody[i][1] = snakeBody[i - 1][1];
            }
            //定义新蛇头
            switch (direction) {
                case "up":
                    if (snakeBody[0][1] - 1 < 0)
                        snakeBody[0][1] = bgRow - 1;
                    else
                        snakeBody[0][1] -= 1;
                    doCollide(snakeBody[0][0], snakeBody[0][1]);
                    break;
                case "down":
                    if (snakeBody[0][1] + 1 > bgRow - 1)
                        snakeBody[0][1] = 0;
                    else
                        snakeBody[0][1] += 1;
                    doCollide(snakeBody[0][0], snakeBody[0][1]);
                    break;
                case "left":
                    if (snakeBody[0][0] - 1 < 0)
                        snakeBody[0][0] = bgCol - 1;
                    else
                        snakeBody[0][0] -= 1;
                    doCollide(snakeBody[0][0], snakeBody[0][1]);
                    break;
                case "right":
                    if (snakeBody[0][0] + 1 > bgCol - 1)
                        snakeBody[0][0] = 0;
                    else
                        snakeBody[0][0] += 1;
                    doCollide(snakeBody[0][0], snakeBody[0][1]);
                    break;
            }
            //绘制新蛇头
            createActBox(snakeBody[0][0], snakeBody[0][1]);
            isMove = true;
            playAudio(audioMove);
        }
        //处理是否相撞
        var doCollide = function (x, y) {
            var n = y * bgCol + x;
            if ($(".box:eq(" + n + ")").attr("status") == "active") {
                clearInterval(timerId);
                playAudio(audioCrash);    
				//得分存入数据库
				$.post("doPost.php",{cname:getCookie('user'),type:'snakey',score:score,do_type:1});				
                showMsg("笨蛋，撞到屁股啦！");      
            }
            else if ($(".box:eq(" + n + ")").attr("status") == "food") {
                snakeBody.push([-1, -1]);
                createRandFood();
                //计算得分
                score += 100;
                if (score % levelScore == 0) {
                    level = score / levelScore + 1;
                    if (level > 10) {
                        clearInterval(timerId);
                        showMsg("恭喜！通关啦！");
                        return;
                    }
                    speed = 500 - (level - 1) * 50;
                    clearInterval(timerId);
                    timerId = setInterval(timerEvent, speed);
                    initLevel();
                }
                $("#score span").html(score);
                $("#level span").html(level);
               
            }
        }
        //显示消息
        var showMsg = function (m) {
            $("#msg").html(m).css("display", "block").animate({ "width": "400px", "height": "70px" }, 400);
        }
        //初始化级别
        var initLevel = function () {
            snakeBody = [[2, 7], [1, 7], [0, 7]];
            direction = "right";
            clearAllBox();
            createSnakeBody();
            createRandFood();	
        }
		//获取排名
		var getSort=function(){
			$.post("doPost.php",{do_type:2,type:'snakey'},function(d){$("#sort").html(d);});
		}
        //初始化
        var init = function () {
			getSort();
            //加载背景网格
            for (var i = 0; i < bgRow * bgCol; i++) {
                var box = $("<div class='box' status='null'></div>");
                $("#main").append(box);
            }
            $(".box").css({ "width": boxSide - 1 + "px", "height": boxSide - 1 + "px" });
            $("#bodyleft").css("width", bgCol * boxSide + "px");
            $("#framebody").css("height", bgRow * boxSide + "px");
            $("#frame").css({ "width": bgCol * boxSide + 83 + "px", "height": bgRow * boxSide + 22 + "px" });
            initLevel();
            musicOpen();			
        }
		//输入一个名称
		var inputName=function(){
		    if(getCookie("user")==null)
			{
				var name=prompt("给自己起一个响亮的名字：","");
				document.cookie="user="+name;
			}
			$("#user").text("欢迎你："+getCookie("user"));
		}
        var isMove = false;
        //控制方向
        $(document).keydown(function (e) {
            if (!isMove) return;
               isMove = false;
                var key = e.which;
                switch (key) {
                    case 37: if (direction != "right") direction = "left"; break;
                    case 38: if (direction != "down") direction = "up"; break;
                    case 39: if (direction != "left") direction = "right"; break;
                    case 40: if (direction != "up") direction = "down"; break;
                }
        });
        //开始按钮
        $("#btnControl").click(function () {
            if ($(this).val() == "开始") {
                timerId = setInterval(timerEvent, speed);
                $(this).val("暂停");
            }
            else {
                clearInterval(timerId);
                $(this).val("开始");
            }
        });
        init();
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
<div style="height:62px;width:160px; margin-left:auto;margin-right:auto; ">
   <div id="user"></div>
   <div style="height:30px;width:100px;padding-top:10px;float:left; ">声音控制：</div>
   <canvas id="myCanvas" width="50" height="40" style="cursor:pointer; float:left; "  title="点击切换">您的浏览器不支持HTML5新特性</canvas>
</div>
<div id="frame">
  <div id="frametitle">贪吃蛇</div>
  <div id="framebody">
      <div id="bodyleft">
        
        <div id="main">
        </div>
      </div>
      <div id="bodyright">    
          <div id="score"><br />得分：<br /><span>0</span></div>
          <div id="level"><br />级别：<br /><span>1</span></div>    
          <div id="menu">
            <br />
            <input type="button" id="btnControl" value="开始" />
          </div>   
      </div>    
  </div>
  <div id="msg">Msg</div>
</div>
<div id="sort"></div>
<div><br /><a href="index.html">返回主页</a></div>
</body>
</html>