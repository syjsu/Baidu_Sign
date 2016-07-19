<?php

	include('library/Requests.php');

	Requests::register_autoloader();

	$kv = new SaeKV();

	$url ="http://tieba.baidu.com/mo/q-0--D4DAB0D65A107C5477B697D5704E4B4D%3AFG%3D1-sz%40480_800%2C-1-3-0--2--wapp_1450066145678_442/sign?tbs=072edb1b0fc223841450066401&fid=420659&kw=%E5%B9%BF%E4%B8%9C%E8%8D%AF%E5%AD%A6%E9%99%A2";

	// 获得key-value
    $MaxCount = $kv->get('ALLMAX');

	//var_dump($MaxCount);
	echo "</br>".$MaxCount;

	$maxnum = (int)$MaxCount;

	for($i = 0;$i<= $maxnum;$i++){
                   
        $tieName = $kv->get($i);
        
        $tieFid = $kv->get($tieName);
       
        SignIn($tieFid,$tieName);
    
    }
	
	function SignIn($id,$name){
		
        echo "<br/>正在自动签到贴吧:".$name."<br/>";
        
		// 下面输入你的百度 BDUSS码 在 '' 中输入
        $BDUSS = '';

        $_headers = array(
            'Cookie' => 'BDUSS='.$BDUSS,
            'Host'  => 'tieba.baidu.com',
            'Referer' => 'http://tieba.baidu.com/',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.111 Safari/537.36',
        );
		
		$TBS_URL = "http://tieba.baidu.com/dc/common/tbs";
		
		$response = Requests::get($TBS_URL, $_headers);
        
        $response = json_decode($response->body);
        echo $response->tbs."<br/>";
        echo $response->is_login;
		
		$myurl = 'http://tieba.baidu.com/mo/q-0--D4DAB0D65A107C5477B697D5704E4B4D%3AFG%3D1-sz%40480_800%2C-1-3-0--2--wapp_1450066145678_442/sign?';
		
		$myUrl2 =  $myurl."tbs=".$response->tbs."&fid=".$id."&kw=".urlencode($name);
		
		echo "<br/>".$myUrl2;
        
        $_headers2 = array(
            'Cookie' => 'BDUSS='.$BDUSS,
            'Host'  => 'tieba.baidu.com',
            'Referer' => 'http://tieba.baidu.com/',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.111 Safari/537.36',
        );
        
        $response2 = Requests::get($myUrl2, $_headers2);
		
	}

?>