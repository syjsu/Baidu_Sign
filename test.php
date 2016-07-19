<?php

	$data;

 	$kv = new SaeKV();
   
        // 循环获取所有key-values       
        $ret = $kv->pkrget('', 100);     
        while (true) {                    
        	var_dump($ret);                       
        	end($ret);                                
        	$start_key = key($ret);
        	$i = count($ret);
        	if ($i < 100) break;
        	$ret = $kv->pkrget('', 100, $start_key);
        }
		echo "</br>===========================================================</br>";
		foreach ($ret as $sub1) {
            foreach ($sub1 as $sub2) {
				echo "++".$sub2."++";
			}
            // 初始化KVClient对象
            // $ret = $kv->init();
		    //var_dump($ret);
			// 更新key-value
            //$ret = $kv->set($sub1["tiebaName"],$sub1["tiebaId"]);

        }
		
		echo "</br>===========================================================</br>";
		echo json_encode($ret,JSON_UNESCAPED_UNICODE);
        echo "</br>===========================================================</br>";
		echo "</br>".var_dump($ret);
		

?>