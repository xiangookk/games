
;(function($){
    $.fn.pingPong=function(options){
        var opts=$.extend({},defaults,options);
        sysSetting.boxSide=opts.boxSide||sysSetting.boxSide;
        sysSetting.bgRow=opts.bgRow||sysSetting.bgRow;
        sysSetting.bgCol=opts.bgCol||sysSetting.bgCol;
        sysSetting.musicSwitch=opts.musicSwitch||sysSetting.musicSwitch;
        sysSetting.level=opts.level||sysSetting.level;
        return this.each(function(){
            var $this=$(this);
            $this.html('<div id="frame">\
                        <div id="frametitle">乒乓球</div>\
                        <div id="framebody">\
                            <div id="bodyleft">\
                                <div id="main">\
                                </div>\
                            </div>\
                            <div id="bodyright">\
                                <div id="score">得分：<br /><span>0</span></div>\
                                <div id="level">级别：<br /><span>1</span></div>\
                                <div id="menu">\
                                    <br />\
                                    <input type="button" id="btnSetting" value="设置" />\
                                    <br /><br />\
                                    <input type="button" id="btnControl" value="开始" />\
                                </div>\
                            </div>\
                        </div>\
                        <div id="divSetting">\
                            <ul style="list-style:none;padding:5px;margin:0px;text-align:left">\
                                <li id="musicSet" >\
                                        声音开关：<input type="radio" name="type" id="mOpen"  value="1" checked="checked"/><label for="musicOpen">开</label>\
                                        <input type="radio" name="type" id="mClose" value="0"/><label for="musicClose">关</label>\
                                </li>\
                                <li title="游戏开始前才能设置">\
                                    设置级别：<input type="range" id="setLevel" min="1" max="10" style="width:130px;margin-bottom:-4px"/>\
                                    <input type="text" value="" style="font-size:14px;width:20px;height:14px" readonly/>\
                                </li>\
                                <li style="text-align:center">\
                                    <br>\
                                    <input type="button" id="btnSave" value="保 存"/>\
                                    <input type="button" id="btnClose" value="关 闭"/>\
                                </li>\
                            </ul>\
                        </div>\
                        <div id="targetCount"></div>\
                        <div id="msg"></div>\
                        </div>');

        
            Init($this);
            //开始按钮
            $("#btnControl").click(function () {            
                if ($(this).val() == "开始") {
                    runing = true;
                    ballTimerId = setInterval(ballTimerEvent, ball.speed);
                    $(this).val("暂停");
                    $("#setLevel").attr("disabled","disabled").parent().css("color","gray");
                }
                else {
                    runing = false;
                    clearInterval(ballTimerId);
                    $(this).val("开始");
                }
            });
            //设置
            $("#btnSetting").click(function(){
                $("#"+(sysSetting.musicSwitch?"mOpen":"mClose")).attr("checked",true);                
                $("#setLevel").val(sysSetting.level).next().val(sysSetting.level);
                
                var left=($("#frame").width()-$("#divSetting").width())/2;
                var top=($("#frame").height()-$("#divSetting").height())/2;
                $("#divSetting").css({"left":left+"px","top":top+"px","display":"block"})
            });
            $("input[type='range']").change(function(){
                $(this).next().val(this.value);
            });
            //保存
            $("#btnSave").click(function(){
                sysSetting.musicSwitch=$("#musicSet input:checked").val()=="1";
                if(!$("#setLevel").attr("disabled")){
                    sysSetting.level=parseInt($("#setLevel").val());
                    Init();
                }
                $("#divSetting").css("display","none");
            });
            //关闭
            $("#btnClose").click(function(){
                $("#divSetting").css("display","none");
            });
        });        
    }
    var sysSetting={
       boxSide:25,
       bgRow:18,
       bgCol:16,
       bgImgSize:"",
       musicSwitch:false,
       audioEat:"../audio/Move.wav",
       audioCrash:"pingpong/audio/Crash.wav",
       playAudio:function (url) {
            var au = document.createElement("audio");
            if (this.musicSwitch) {
                au.src = url;
                au.play();
            }
       },
       level:1,
       score:0
    }
    //球
    var Ball=function(){
        this.bgImgPosition={X:0,Y:0};
        this.speed=320;//走一格间隔毫秒数
        this.direction={X:1,Y:1}; //方向
        this.position={X:0,Y:0}; //位置
        this.boxType="ball";
    }
    //球拍
    var Racket=function(){
        this.bgImgPosition={X:0,Y:0};
        this.speed=50;//多少毫秒走一格
        this.position={X:0,Y:0}; //左起第一个方格位置
        this.len=3;//长度
        this.boxType="racket";
    }
    //球靶
    var Target=function(){
        this.bgImgPosition={X:0,Y:0};
        this.position={X:0,Y:0}; //位置
        this.boxType="target";
    }
    var Init=function(tag){
        var boxSide=sysSetting.boxSide;
        var bgRow=sysSetting.bgRow;
        var bgCol=sysSetting.bgCol;
        //背景图像调整后的大小
        sysSetting.bgImgSize = boxSide * 7 + "px " + boxSide * 5 + "px";
        //背景图像一组X,Y坐标值
        var bgImgPositionXs = [0, -boxSide, -boxSide * 2, -boxSide * 3, -boxSide * 4, -boxSide * 5, -boxSide * 6];
        var bgImgPositionYs = [0, -boxSide, -boxSide * 2, -boxSide * 3, -boxSide * 4];
        
        ball=new Ball();
        //球背景图像XY坐标值
        ball.bgImgPosition.X=bgImgPositionXs[2];
        ball.bgImgPosition.Y=bgImgPositionYs[3];                  
        //初始化球位置
        ball.position.X = Math.floor(bgCol / 2) - 1;
        ball.position.Y=bgRow - 2;
        //初始化球方向
        ball.direction.X=-1;
        ball.direction.Y=-1;
        ball.speed=350-30*sysSetting.level;
       // tag.data(ball);

        racket=new Racket();
        //球拍背景图像XY坐标值
        racket.bgImgPosition.X=bgImgPositionXs[0];
        racket.bgImgPosition.Y=bgImgPositionYs[3];                  
        //初始化球拍位置
        racket.position.X = Math.floor((bgCol - racket.len)/2);
        racket.position.Y=bgRow - 1;
        //tag.data(racket);
       
        //初始化球靶
        targets=[];
        for(var i=0;i<sysSetting.bgCol*(sysSetting.level+1);i++){
            if(Math.random()>0.5){
                var target=new Target(); 
                target.bgImgPosition.X=bgImgPositionXs[4];
                target.bgImgPosition.Y=bgImgPositionYs[3];
                target.position.X=i%sysSetting.bgCol;
                target.position.Y=Math.floor(i/sysSetting.bgCol);              
                targets.push(target);
            }
        }

        //清空box元素
        $("#main").empty();
        //加载背景网格
        for (var i = 0; i < bgCol * bgRow; i++) {
            var box = $("<div class='box'></div>");
            $("#main").append(box);
        }
        $(".box").css({"float":"left", "width": boxSide - 1 + "px", "height": boxSide - 1 + "px", "borderRight": "dashed 1px #1B272C", "borderBottom": "dashed 1px #1B272C", "borderLeft": "0px", "borderTop": "0px" });
        $("#bodyleft").css("width", bgCol * boxSide + "px");
        $("#framebody").css("height", bgRow * boxSide + "px");
        $("#frame").css({ "width": bgCol * boxSide + 82 + "px", "height": bgRow * boxSide + 22 + "px" });
        
        //绘制球靶对象
        for(var i=0;i<targets.length;i++){
            DrawBox(targets[i].position.X,targets[i].position.Y,targets[i].bgImgPosition,targets[i].boxType);
        }
        DrawBox(ball.position.X, ball.position.Y, ball.bgImgPosition,ball.boxType);
        DrawRacket();
        RefreshBand();      
    }
    //计时器事件
    function ballTimerEvent(){
        if(!runing)return;
        //ballMove
        var x=ball.position.X+ball.direction.X; //next x
        var y=ball.position.Y+ball.direction.Y;  //next y
        if(x<0||x>sysSetting.bgCol-1) ball.direction.X=-ball.direction.X;
        x=ball.position.X+ball.direction.X;
        if(y<0||GetType(x,y)==racket.boxType) ball.direction.Y=-ball.direction.Y;
        y=ball.position.Y+ball.direction.Y;
        if(GetType(x,y)==new Target().boxType)
        {
            sysSetting.score+=100;
            targets.splice(0,1);
            if(targets.length==0){
                sysSetting.level++;
                Init();
            }
            RefreshBand();
        }
        if(y==sysSetting.bgRow){
            //你输了
            runing = false;
            clearInterval(ballTimerId);
            sysSetting.playAudio(sysSetting.audioCrash);
            ShowMsg("你输了，哈哈~");
            return;
        }
        ClearActBox(ball.position.X,ball.position.Y);
        ball.position.X+=ball.direction.X;
        ball.position.Y+=ball.direction.Y;
        DrawBox(ball.position.X,ball.position.Y,ball.bgImgPosition,ball.boxType);
        
    }
    //绘制一个单格
    var DrawBox = function (x, y, bgPos,type) {
        if (x < 0 || y < 0) return;
        var i = y * sysSetting.bgCol + x;
        $(".box:eq(" + i + ")").addClass("box-bg").css({ "width": sysSetting.boxSide + "px", "height": sysSetting.boxSide + "px", "background-size": sysSetting.bgImgSize, "backgroundPosition": bgPos.X + "px " + bgPos.Y + "px", "border": "0px" });
        $(".box:eq(" + i + ")").attr("type", type);
    }
    //清除一个单格
    var ClearActBox = function (x, y) {
        if (x < 0 || y < 0) return;
        var i = y * sysSetting.bgCol + x;
        $(".box:eq(" + i + ")").removeClass("box-bg").css({ "width": sysSetting.boxSide - 1 + "px", "height": sysSetting.boxSide - 1 + "px",  "borderRight": "dashed 1px #1B272C", "borderBottom": "dashed 1px #1B272C", "borderLeft": "0px", "borderTop": "0px" });
        $(".box:eq(" + i + ")").removeAttr("type");
    }
    //绘制球拍
    var DrawRacket=function(){
        for(var i=0; i<racket.len;i++){
            DrawBox(racket.position.X+i,racket.position.Y,racket.bgImgPosition,racket.boxType);
        }
    }
    //清除球拍
    var ClearRacket=function(){
        for(var i=0; i<racket.len;i++){
            ClearActBox(racket.position.X+i,racket.position.Y);
        }
    }
    //获取单格type
    var GetType=function(x,y){
        var i = y * sysSetting.bgCol + x;
        return $(".box:eq(" + i + ")").attr("type");
    }
    //移动球拍
    var RacketMove=function(key){
        if(key!=37&&key!=39) return;
        var x=racket.position.X;
        var y=sysSetting.bgRow-1;
        if(key==37&&(x-1<0||GetType(x-1,y)==ball.boxType)) return;
        if(key==39&&(x+1>(sysSetting.bgCol-racket.len)||GetType(x+racket.len,y)==ball.boxType)) return;
        ClearRacket();
        racket.position.X += key==37?-1:1;
        DrawRacket();   
        if(ball.position.X>racket.position.X+(key==37?-1:0)
         &&ball.position.X<racket.position.X+racket.len+(key==37?0:1)
         &&ball.position.Y==sysSetting.bgRow-2)
         {
             ClearActBox(ball.position.X, ball.position.Y);
             ball.position.X+=key==37?-1:1;
             ball.direction.X=key==37?-1:1;  
             ball.direction.Y=-ball.direction.Y;
             DrawBox(ball.position.X, ball.position.Y, ball.bgImgPosition, ball.boxType);
         }
    }
    //控制方向
    $(document).keydown(function (e) {
        if (runing) {
            if (isKeyDown) return;
            isKeyDown = true;
            var key = e.which;
            RacketMove(key);
            
			setTimeout(function(){isKeyDown=false;},racket.speed);
        }
    });
    //显示消息
    var ShowMsg = function (m) {
        var left=($("#frame").width()-400)/2;
        var top=($("#frame").height()-90)/2;
        $("#msg").html(m).css({"left":left+"px","top":top+"px","display":"block"}).animate({ "width": "400px", "height": "90px" }, 400);
    }
    //刷新得分板
    var RefreshBand=function(){
        $("#score span").html(sysSetting.score);
        $("#level span").html(sysSetting.level);
        $("#targetCount").html(targets.length);
    }
    var defaults={
        level:1,
        musicSwitch:false
    }
    var ball, racket,ballTimerId,runing=false,isKeyDown = false,targets=[];
})(jQuery)