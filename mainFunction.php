<?php

/**
 * 显示某个贴吧的外链
 */
echo '<td class="wrap"><a title="'.$x['tieba'].'" href="http://tieba.baidu.com/f?ie=utf-8&kw='.$x['tieba'].'" target="_blank">'. mb_substr($x['tieba'] , 0 , 30 , 'UTF-8') .'</a>';

/**
 * 得到贴吧 FID
 * @param string $kw 贴吧名
 * @return string FID
 */

public static function getFid($kw) {
	global $m;
	/*
	$f  = misc::findFid($kw);
	if ($f) {
		return $f;
	} else {
	*/
		$ch = new wcurl('http://tieba.baidu.com/mo/m?kw='.urlencode($kw), array('User-Agent: fuck phone','Referer: http://wapp.baidu.com/','Content-Type: application/x-www-form-urlencoded'));
		$s  = $ch->exec();
		//self::mSetFid($kw,$fid[1]);
		$x  = easy_match('<input type="hidden" name="fid" value="*"/>',$s);
		if (isset($x[1])) {
			return $x[1];
		} else {
			return false;
		}
	//}
}

/**
 * 得到TBS
 */
public static function getTbs($uid,$bduss){
	$ch = new wcurl('http://tieba.baidu.com/dc/common/tbs', array('User-Agent: fuck phone','Referer: http://tieba.baidu.com/','X-Forwarded-For: 115.28.1.'.mt_rand(1,255)));
	$ch->addcookie("BDUSS=". $bduss);
	$x = json_decode($ch->exec(),true);
	return $x['tbs'];
}


/**
 * 对输入的数组添加客户端验证代码（tiebaclient!!!）
 * @param array $data 数组
 */
public static function addTiebaSign(&$data) {
    $data = array(
        '_client_id' => '03-00-DA-59-05-00-72-96-06-00-01-00-04-00-4C-43-01-00-34-F4-02-00-BC-25-09-00-4E-36',
        '_client_type' => '4',
        '_client_version' => '6.0.1',
        '_phone_imei' => '540b43b59d21b7a4824e1fd31b08e9a6',
    ) + $data;
    $x = '';
    foreach($data as $k=>$v) {
        $x .= $k.'='.$v;
    }
    $data['sign'] = strtoupper(md5($x.'tiebaclient!!!'));
}


/**
 * 50个贴吧客户端一键签到
 */
public static function DoSign_Onekey($uid,$kw,$id,$pid,$fid,$ck) {
	$ch = new wcurl('http://c.tieba.baidu.com/c/c/forum/msign', array(
		'User-Agent: bdtb for Android 6.5.8'
	));
	$ch->addcookie(array('BDUSS' => $ck));
	$temp = array(
		'BDUSS' => misc::getCookie($pid),
		'_client_id' => '03-00-DA-59-05-00-72-96-06-00-01-00-04-00-4C-43-01-00-34-F4-02-00-BC-25-09-00-4E-36',
		'_client_type' => '4',
		'_client_version' => '1.2.1.17',
		'_phone_imei' => '540b43b59d21b7a4824e1fd31b08e9a6',
		'fid' => $fid,
		'kw' => $kw,
		'net_type' => '3',
		'tbs' => misc::getTbs($uid,$ck)
	);
    self::addTiebaSign($temp);
	return $ch->post($temp);
}

/**
 * 手机网页签到
 */
public static function DoSign_Mobile($uid,$kw,$id,$pid,$fid,$ck) {
	//没问题了
	$ch = new wcurl('http://tieba.baidu.com/mo/q/sign?tbs='.misc::getTbs($uid,$ck).'&kw='.urlencode($kw).'&is_like=1&fid='.$fid ,array('User-Agent: fuck phone','Referer: http://tieba.baidu.com/f?kw='.$kw , 'Host: tieba.baidu.com','X-Forwarded-For: 115.28.1.'.mt_rand(1,255), 'Origin: http://tieba.baidu.com', 'Connection: Keep-Alive'));
	$ch->addcookie("BDUSS=".$ck);
	return $ch->exec();
}

/**
 * 网页签到
 */
public static function DoSign_Default($uid,$kw,$id,$pid,$fid,$ck) {
	global $m,$today;
	$ch = new wcurl('http://tieba.baidu.com/mo/m?kw='.urlencode($kw).'&fid='.$fid, array('User-Agent: fuck phone','Referer: http://wapp.baidu.com/','Content-Type: application/x-www-form-urlencoded'));
	$ch->addcookie("BDUSS=".$ck);
	$s  = $ch->exec();
	$ch->close();
	preg_match('/\<td style=\"text-align:right;\"\>\<a href=\"(.*)\"\>签到\<\/a\>\<\/td\>\<\/tr\>/', $s, $s);
	if (isset($s[1])) {
		$ch = new wcurl('http://tieba.baidu.com'.$s[1],
			array(
				'Accept: text/html, application/xhtml+xml, */*',
				'Accept-Language: zh-Hans-CN,zh-Hans;q=0.8,en-US;q=0.5,en;q=0.3',
				'User-Agent: Fucking Phone'
			));
		$ch->addcookie("BDUSS=".$ck);
		$ch->exec();
		$ch->close();
		//临时判断解决方案
		$ch = new wcurl('http://tieba.baidu.com/mo/m?kw='.urlencode($kw).'&fid='.$fid, array('User-Agent: fuck phone','Referer: http://wapp.baidu.com/','Content-Type: application/x-www-form-urlencoded'));
		$ch->addcookie("BDUSS=".$ck);
		$s = $ch->exec();
		$ch->close();
		//如果找不到这段html则表示没有签到则stripos()返回false，同时is_bool()返回true，最终返回false
		return !is_bool(stripos($s,'<td style="text-align:right;"><span >已签到</span></td>'));
	} else {
		return true;
	}
}

/**
 * 客户端签到
 */
public static function DoSign_Client($uid,$kw,$id,$pid,$fid,$ck){
	$ch = new wcurl('http://c.tieba.baidu.com/c/c/forum/sign', array('Content-Type: application/x-www-form-urlencoded','User-Agent: Fucking iPhone/1.0 BadApple/99.1'));
	$ch->addcookie("BDUSS=".$ck);
	$temp = array(
		'BDUSS' => misc::getCookie($pid),
		'_client_id' => '03-00-DA-59-05-00-72-96-06-00-01-00-04-00-4C-43-01-00-34-F4-02-00-BC-25-09-00-4E-36',
		'_client_type' => '4',
		'_client_version' => '1.2.1.17',
		'_phone_imei' => '540b43b59d21b7a4824e1fd31b08e9a6',
		'fid' => $fid,
		'kw' => $kw,
		'net_type' => '3',
		'tbs' => misc::getTbs($uid,$ck)
	);
	$x = '';
	foreach($temp as $k=>$v) {
		$x .= $k.'='.$v;
	}
	$temp['sign'] = strtoupper(md5($x.'tiebaclient!!!'));
	return $ch->post($temp);
}

/**
 * 对一个贴吧执行完整的签到任务
 *
 * 使用方法 do.php
 *
foreach ($q as $x) {
	self::DoSign_All($x['uid'] , $x['tieba'] , $x['id'] , $table , $sign_mode , $x['pid'] , $x['fid']);
}
 */

public static function DoSign_All($uid,$kw,$id,$table,$sign_mode,$pid,$fid) {
	global $m;
	$again_error_id     = 160002; //重复签到错误代码
	$again_error_id_2   = 1101; //特殊的重复签到错误代码！！！签到过快=已签到
	$again_error_id_3   = 1102; //特殊的重复签到错误代码！！！签到过快=已签到
	$status_succ    = false;

	$ck = misc::getCookie($pid);
	$kw = addslashes($kw);
	$today = date('d');

	if (empty($fid)) {
		$fid = misc::getFid($kw);
		$m->query("UPDATE  `".DB_PREFIX.$table."` SET  `fid` =  '{$fid}' WHERE  `".DB_PREFIX.$table."`.`id` = '{$id}';",true);
	}

	//dump(json_decode(self::DoSign_Client($uid,$kw,$id,$pid,$fid,$ck),true),true);die;

	if(!empty($sign_mode) && in_array('1',$sign_mode) && $status_succ === false) {
		$r = self::DoSign_Client($uid,$kw,$id,$pid,$fid,$ck);
		$v = json_decode($r,true);
		if($v != $r && $v != NULL){//decode失败时会直接返回原文或NULL
			if (empty($v['error_code']) || $v['error_code'] == $again_error_id) {
				$status_succ = true;
			} else {
				$error_code = $v['error_code'];
				$error_msg  = $v['error_msg'];
			}
		}
	}

	if(!empty($sign_mode) && in_array('3',$sign_mode) && $status_succ === false) {
		$r = self::DoSign_Mobile($uid,$kw,$id,$pid,$fid,$ck);
		$v = json_decode($r,true);
		if($v != $r && $v != NULL){//decode失败时会直接返回原文或NULL
			if (empty($v['no']) || $v['no'] == $again_error_id_2 || $v['no'] == $again_error_id_3) {
				$status_succ = true;
			} else {
				$error_code  = $v['no'];
				$error_msg   = $v['error'];
			}
		}
	}

	if(!empty($sign_mode) && in_array('2',$sign_mode) && $status_succ === false) {
		if(self::DoSign_Default($uid,$kw,$id,$pid,$fid,$ck) === true) {
			$status_succ = true;
		}
	}

	if ($status_succ === true) {
		$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX.$table."` SET  `latest` =  '".$today."',`status` =  '0',`last_error` = NULL WHERE `".DB_PREFIX.$table."`.`id` = '{$id}'",true);
	} else {
		$m->query("UPDATE `".DB_NAME."`.`".DB_PREFIX.$table."` SET  `latest` =  '".$today."',`status` =  '".$error_code."',`last_error` = '".$error_msg."' WHERE `".DB_PREFIX.$table."`.`id` = '{$id}'",true);
	}

	usleep(option::get('sign_sleep') * 1000);
}





/**
 * 登录百度
 * @param string $bd_name 百度用户名
 * @param string $bd_pw百度密码
 * @param string $verifycode 验证码
 * @param string $vcodestr 验证字符
 * @return array [0成功|-1网络请求失败|-2json解析失败|-3表示需要验证码或验证码错误|2表示登陆失败|其他为百度提供的错误代码, 成功为BDUSS|需要验证码则返回vcodestr|其他错误返回百度提供的错误信息, 如果登陆成功，返回百度用户名|如果需要验证码，则此处返回验证图片地址 ]
 */
public static function loginBaidu( $bd_name , $bd_pw , $verifycode = '', $vcodestr = '') {
	$x = new wcurl('http://c.tieba.baidu.com/c/s/login');
	$p = array(
            'passwd'      => base64_encode($bd_pw),
            'timestamp'   => time() . '156',
            'un'          => $bd_name,
	);
	if(!empty($verifycode) && !empty($vcodestr)) {
		$p['vcode'] = $verifycode;
		$p['vcode_md5'] = $vcodestr;
	}
    self::addTiebaSign($p);
	//print_r($p);
	if(!$data = $x->post($p)) return array(-1, '网络请求失败');
	if(!$v = json_decode($data, true)) return array(-2, 'json解析失败');
	$md5pos = strpos($v['user']['BDUSS'], '|');
	if(!empty($md5pos)) {
		$bduss = substr($v['user']['BDUSS'], 0 , $md5pos);
	} else {
		$bduss = $v['user']['BDUSS'];
	}
    if($v['error_code'] == '0') {
        return array(0, $bduss, $v['user']['name']);
    } else {
        switch($v['error_code']) {
            case '5': //需要验证码或验证码输入错误
            case '6':
                return array(-3, $v['anti']['vcode_md5'], $v['anti']['vcode_pic_url']);
                break;

            default: //其他错误
                return array((int)$v['error_code'], $v['error_msg']);
                break;
        }
    }
}

/*
 * 获取指定pid用户userid
 */
public static function getUserid($pid){
	global $m;
	$ub  = $m->once_fetch_array("SELECT * FROM `".DB_PREFIX."baiduid` WHERE `id` = '{$pid}';");
	$user = new wcurl('http://tieba.baidu.com/i/sys/user_json');
	$user->addCookie(array('BDUSS' => $ub['bduss']));
	$re = iconv("GB2312","UTF-8//IGNORE",$user->get());
	$ur = json_decode($re,true);
	$userid = $ur['creator']['id'];
	return $userid;
}

/*
 * 获取指定pid
 */
public static function getTieba($userid,$bduss,$pn){
	$head = array();
	$head[] = 'Content-Type: application/x-www-form-urlencoded';
	$head[] = 'User-Agent: Mozilla/5.0 (SymbianOS/9.3; Series60/3.2 NokiaE72-1/021.021; Profile/MIDP-2.1 Configuration/CLDC-1.1 ) AppleWebKit/525 (KHTML, like Gecko) Version/3.0 BrowserNG/7.1.16352';
	$tl = new wcurl('http://c.tieba.baidu.com/c/f/forum/like',$head);
	$data = array(
		'_client_id' => 'wappc_' . time() . '_' . '258',
		'_client_type' => 2,
		'_client_version' => '6.5.8',
		'_phone_imei' => '357143042411618',
		'from' => 'baidu_appstore',
		'is_guest' => 1,
		'model' => 'H60-L01',
		'page_no' => $pn,
		'page_size' => 200,
		'timestamp' => time(). '903',
		'uid' => $userid,
	);
	$sign_str = '';
	foreach($data as $k=>$v) $sign_str .= $k.'='.$v;
	$sign = strtoupper(md5($sign_str.'tiebaclient!!!'));
	$data['sign'] = $sign;
	$tl->addCookie(array('BDUSS' => $bduss));
	$tl->set(CURLOPT_RETURNTRANSFER,true);
	$rt = $tl->post($data);
	return $rt;
}

/**
 * 扫描指定PID的所有贴吧
 * @param string $pid PID
 */
public static function scanTiebaByPid($pid) {
	global $i;
	global $m;
	$cma    = $m->once_fetch_array("SELECT * FROM `".DB_PREFIX."baiduid` WHERE `id` = '{$pid}';");
	$uid    = $cma['uid'];
	$ubduss = $cma['bduss'];
	$isvip  = self::isvip($uid);
	$pid    = $cma['id'];
	$userid = self::getUserid($pid);
	$table  = self::getTable($uid);
	$o      = option::get('tb_max');
	$pn     = 1;
	while (true){
		if (empty($userid)) break;
		$rc = self::getTieba($userid,$ubduss,$pn);
		$rc = json_decode($rc,true);
		if (count($rc['forum_list']['non-gconforum']) < 1) break;
		foreach ($rc['forum_list']['non-gconforum'] as $v){
			$tb = $m->fetch_array($m->query("SELECT count(id) AS `c` FROM `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `uid` = {$uid}"));
			if ($tb['c'] >= $o && !$isvip) break;
			$v = addslashes(htmlspecialchars($v['name']));
			$ist = $m->once_fetch_array("SELECT COUNT(id) AS `c` FROM `".DB_NAME."`.`".DB_PREFIX.$table."` WHERE `uid` = ".$uid." AND `pid` = '{$pid}' AND `tieba` = '{$v}';");
			if ($ist['c'] == 0){
				$m->query("INSERT INTO `".DB_NAME."`.`".DB_PREFIX.$table."` (`id`, `pid`, `uid`, `tieba`, `no`, `latest`) VALUES (NULL, {$pid}, {$uid}, '{$v}', 0, 0);");
			}
		}
		$pn ++;
	}
}

/**
 * 扫描指定用户的所有贴吧并储存
 * @param UID，如果留空，表示当前用户的UID
 */
public static function scanTiebaByUser($uid = '') {
	global $i;
	global $m;
	set_time_limit(0);
	if (empty($uid)) {
		$bduss = $i['user']['bduss'];
	} else {
		$bx = $m->query("SELECT * FROM `".DB_PREFIX."baiduid` WHERE `uid` = '{$uid}';");
		while ($by = $m->fetch_array($bx)) {
			$upid         = $by['id'];
			$bduss[$upid] = $by['bduss'];
		}
	}
	$n      = 0;
	foreach ($bduss as $pid => $ubduss) {
		$t = self::scanTiebaByPid($pid);
	}
}
