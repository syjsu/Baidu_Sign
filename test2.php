<?php
	$data;

 	$kv = new SaeKV();

	$count = 0;

	// 初始化KVClient对象
    $ret = $kv->init();
    //var_dump($ret);
        
    // 更新key-value
    $ret = $kv->set('ALLMAX', '30');
    var_dump($ret);

	// 获得key-value
    $MaxCount = $kv->get('ALLMAX');
	//var_dump($MaxCount);
	echo "</br>".$MaxCount;

	$maxnum = (int)$MaxCount;

	for($i = 0;$i<= $maxnum;$i++){
        
    	echo "</br>===========================================================</br>"; 
        
        $tieName = $kv->get($i);
        
        $tieFid = $kv->get($tieName);
        
        //var_dump($tieName);
        
        //var_dump($tieFid);
        
        echo " ".$i."         ".$tieName."-".$tieFid;
    
    }

?>