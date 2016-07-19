<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>贴吧签到器</title>
        <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <style type="text/css">
            body,
            button,
            input,
            select,
            textarea,
            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                font-family: Microsoft YaHei, '宋体', Tahoma, Helvetica, Arial, "\5b8b\4f53", sans-serif;
            }
            </style>
    </head>

    <body>
		<br>
        <br>
        <br>
		<p> <font size="24"  color="#f00">&nbsp;&nbsp;&nbsp;&nbsp; My 贴吧签到器</font></p>
		
		<br>
        <br>
        <br>
        <br>

	<!-- 占据设备100%的宽度 -->
	

	<iframe src="http://www.thinkpage.cn/weather/weather.aspx?uid=U0B5DC8845&cid=CHGD160000&l=zh-CHS&p=SMART&a=0&u=C&s=1&m=2&x=1&d=3&fc=&bgc=&bc=&ti=0&in=0&li=&ct=iframe" frameborder="0" scrolling="no" width="200" height="230" allowTransparency="true"></iframe>
		
        <div class="container-fluid">
            <p tyle="padding-left:30px;"> 	添加需要签到的贴吧信息
            <form  method="POST" onSubmit="return submitOnce(this)">
              <div class="row-fluid">
				<div class="span4">
					<label for="tiebaName" style="padding-left:30px;">贴吧名字</label>
					<input type="text"  name="tiebaName" placeholder="例:java" style="width:200px">
					<input type="hidden" name="originator" value = "$code">
				</div>
                			
              </div>
			  <br>
			  <br>
              <button type="submit" class="btn btn-default">确认提交</button>	  
            </form>        
        <br>
        <br>
        <br>
        </div>
    </body>
</html>



<?php

	include('library/Requests.php');

	Requests::register_autoloader();
	
	$kv = new SaeKV();
	
	if ( isset($_REQUEST["tiebaName"])) {

		$tiebaName = strip_tags( $_REQUEST['tiebaName'] );
		

		if(strlen($tiebaName)<1){
			echo "您没有输入tiebaName";
			break;
		}
        
        $url = 'http://tieba.baidu.com/f/commit/share/fnameShareApi?ie=utf-8&fname='.$tiebaName; 
		
		$ch = curl_init();  
    	curl_setopt($ch, CURLOPT_URL, $url);  
     	curl_setopt($ch, CURLOPT_HEADER, false);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //如果把这行注释掉的话，就会直接输出  
    	$result=curl_exec($ch);  
        
        //var_dump(data2);
        $result = json_decode($result);
        
        $fid = strip_tags(intval($result->data->fid));
        
        echo "* fid  = ".$fid;
        
        curl_close($ch); 
        
        // 出入数据前，先获取当前存储贴吧的总数量
        $tiebaMaxNum = $kv->get("ALLMAX");
        // 现在要存储的贴吧id
        $tieCurrenId = (int)$tiebaMaxNum + 1;
        // 重新保存最新的总数量
        $kv->set("ALLMAX",$tieCurrenId);
        // key-value  id ：name 存储
        $kv->set($tieCurrenId,$tiebaName);
        // key-value  name : fid
        $kv->set($tiebaName,$fid);
        
       
    }
		
	// 这里是显示所有需要签到的贴吧
      
	echo "<br>";
	echo "需要签到的贴吧有：";
	echo "<br>";
	echo '<table class="table table-striped">';
	echo "<tr><td>id</td><td>tiebaID</td><td>tiebaName</td></tr>";
        
    // 获得key-value
    $MaxCount = $kv->get('ALLMAX');

	//var_dump($MaxCount);
	echo "</br>".$MaxCount;

	$maxnum = (int)$MaxCount;

	for($i = 0;$i<= $maxnum;$i++){
        
        echo "<tr>";
               
        $tieName = $kv->get($i);
        
        $tieFid = $kv->get($tieName);
        
        //var_dump($tieName);
        
        //var_dump($tieFid);
        
        echo "<td>".$i."<td>".$tieName."<td>".$tieFid."<td>";
        
        echo "</tr>";
    
    }

	echo "</table>";
	
	die();
?>