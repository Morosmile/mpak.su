<?

if($_GET['anket_id']){
	$tpl['anket'] = mpql(mpqw("SELECT * FROM {$conf['db']['prefix']}{$arg['modpath']}_anket WHERE id=". (int)$_GET['anket_id']), 0);
	$conf['index'] = mpql(mpqw("SELECT * FROM {$conf['db']['prefix']}{$arg['modpath']}_index WHERE id=". (int)$tpl['anket']['index_id']), 0);
	if($conf['index']['uid'] == $conf['user']['uid']){
		$tpl['result'] = mpqn(mpqw("SELECT * FROM {$conf['db']['prefix']}{$arg['modpath']}_result WHERE anket_id=". (int)$tpl['anket']['id']), 'vopros_id', 'variant_id');//	mpre($tpl['result']);
	}else{
		header("Location: /{$arg['modpath']}/{$conf['index']['id']}");
	} $_GET['id'] = $tpl['anket']['index_id'];
}

$tpl['index'] = qn("SELECT id.*, u.name AS uname FROM {$conf['db']['prefix']}{$arg['modpath']}_index AS id LEFT JOIN {$conf['db']['prefix']}users AS u ON id.uid=u.id WHERE id.id=". (int)$_GET['id']);

if($index = $tpl['index'][ $_GET['id'] ]){
	$tpl['type'] = qn("SELECT * FROM {$conf['db']['prefix']}{$arg['modpath']}_type");

	$tpl['vopros'] = mpqn(mpqw("SELECT v.id AS vid, v.* FROM {$conf['db']['prefix']}{$arg['modpath']}_index AS id LEFT JOIN {$conf['db']['prefix']}{$arg['modpath']}_vopros AS v ON id.id=v.index_id WHERE id.id=". (int)$_GET['id']. " ORDER BY v.type_id, v.sort, v.id"), 'type_id', 'vid');
	$tpl['variant'] = mpqn(mpqw("SELECT vt.* FROM {$conf['db']['prefix']}{$arg['modpath']}_index AS id LEFT JOIN {$conf['db']['prefix']}{$arg['modpath']}_vopros AS v ON id.id=v.index_id LEFT JOIN {$conf['db']['prefix']}{$arg['modpath']}_variant AS vt ON v.id=vt.vopros_id WHERE id.id=". (int)$_GET['id']), 'vopros_id', 'id');

	if($anket = $tpl['index'][ $_GET['id'] ]){
		foreach($tpl['vopros'] as $t){
			foreach($t as $v){
				if(!empty($v['tn'])){
					$fields = mpqn(mpqw("SHOW COLUMNS FROM `". mpquot($v['tn']). "`"), 'Field');
					if(array_search($v['type'], array("radio", "select")) !== false){
						$tpl['variant'][ $v['id'] ] = mpqn(mpqw("SELECT * FROM ". mpquot($v['tn']). " WHERE 1 ". ($fields['uid'] ? " AND uid=". (int)$conf['user']['uid'] : ""). " ORDER BY name"));
						if($v['type'] != "radio") $tpl['variant'][ $v['id'] ] += array("0"=>array("name"=>""));
					}else if($v['type'] == "file"){
						$key = implode("_", array_slice(explode("_", $anket['tn']), 2, 999)). "_id";
						$tpl['variant'][ $v['id'] ] = mpqn(mpqw("SELECT * FROM ". mpquot($v['tn']). " WHERE 1 ". ($fields['uid'] ? " AND uid=". (int)$conf['user']['uid'] : ""). " AND ". mpquot($key). "=0 ORDER BY id"));
					}else if($v['type'] == "check"){
						$tpl['variant'][ $v['id'] ] = mpqn(mpqw("SELECT * FROM ". mpquot($v['tn']). " WHERE 1 ". ($fields['uid'] ? " AND uid=". (int)$conf['user']['uid'] : ""). " ORDER BY name"));
					}
				}
			}
		}// mpre($tpl['variant']);

		if($_POST){
			if(array_key_exists("null", $_GET) && ($vopros = $tpl['vopros'][0][ $_POST['vopros_id'] ])){
				if($img_id = mpfid($vopros['tn'], $vopros['alias'], null, $_POST['vopros_id'], array('*'=>'*'))){
	//				mpqw("UPDATE ". mpquot($vopros['tn']). " SET img=\"". mpquot($fn). "\" WHERE id=". (int)$insert_id);
				} exit(json_encode(array("id"=>$img_id, "tn"=>explode("_", $vopros['tn'], 3))));

			}else if($anket['captcha'] && ($_COOKIE['captcha_keystring'] != md5($_POST['captcha']))){
				$tpl['captcha'] = "false";
			}else{
				mpqw("INSERT INTO {$conf['db']['prefix']}{$arg['modpath']}_anket SET time=". time(). ", index_id=". (int)$_GET['id']. ", uid=". (int)$conf['user']['uid']);
				if($tpl['anket_id'] = mysql_insert_id()){
					foreach($tpl['vopros'] as $type_id=>$vopros){
						foreach($vopros as $vopros_id=>$v){
							if(($v['type'] == 'check') && !empty($_POST[ $vopros_id ])){
								foreach($_POST[ $vopros_id ] as $variant_id=>$val){
									mpqw($sql = "INSERT INTO {$conf['db']['prefix']}{$arg['modpath']}_result SET anket_id=". (int)$tpl['anket_id']. ", vopros_id=". (int)$vopros_id. ", variant_id=". (int)$variant_id);
									$cds[$vopros_id][] = $variant_id;
								}
							}elseif($_POST[$vopros_id] && (($v['type'] == 'text') || ($v['type'] == 'textarea') || ($v['type'] == 'map'))){
								mpqw($sql = "INSERT INTO {$conf['db']['prefix']}{$arg['modpath']}_result SET anket_id=". (int)$tpl['anket_id']. ", vopros_id=". (int)$vopros_id. ", val=\"". mpquot($_POST[$vopros_id]). "\"");
								$fds[ $v['alias'] ] = $_POST[$vopros_id];
							}elseif(($v['type'] == 'file')){
								if($v['tn']){ # Отдельная таблица для изображений
									$fds_img[] = $vopros_id;
								}else{// mpre($fds_img); exit; # Файл в связанной таблице
									if(($fid = mpfid("{$conf['db']['prefix']}{$arg['modpath']}_result", 'file', null, $vopros_id, array("*"=>"*")))){
										$file = mpql(mpqw($sql = "SELECT * FROM {$conf['db']['prefix']}{$arg['modpath']}_result WHERE id=". (int)$fid), 0);// mpre($sql); exit;
										$fds[ $v['alias'] ] = $file['file'];// mpre($fds); exit;
									}else{ /*mpre($_FILES);*/ }// mpre($fds);
								}
							}elseif((int)$_POST[ $vopros_id ]){
								$sql = "INSERT INTO {$conf['db']['prefix']}{$arg['modpath']}_result SET anket_id=". (int)$tpl['anket_id']. ", vopros_id=". (int)$vopros_id. ", variant_id=". (int)$_POST[$vopros_id];
								$fds[ $v['alias'] ] = (int)$_POST[$vopros_id];
								mpqw($sql);
							} $res[] = "\n#{$v['id']} {$v['name']} : ". $_POST[$vopros_id];
						}
					} mpevent("Заполнение формы {$index['name']}", $_SERVER['REQUERT_URI'], $anket['uid'], "/{$arg['modpath']}/anket_id:{$tpl['anket_id']}", implode("<br />", $res), $_POST, $conf['user'], $_SERVER);
					if($fds){ # Свойства результирующей таблицы
						$tpl['mysql_inset_id'] = mpfdk($anket['tn'], null, array("time"=>time(), "uid"=>$conf['user']['uid'])+(array)$fds);
						if($fds_img){
							foreach($fds_img as $v){
								$vp = $tpl['vopros'][0][ $v ];
								$key = implode("_", array_slice(explode("_", $anket['tn']), 2, 999)). "_id";
								mpqw($sql = "UPDATE ". mpquot($vp['tn']). " SET ". mpquot($key). "=". (int)$tpl['mysql_inset_id']. " WHERE uid=". (int)$conf['user']['uid']. " AND ". mpquot($key). "=0");// mpre($sql);
							}
						}
					} if($cds){ # Связанные списки выбора
						foreach($cds as $vid=>$ds){
							$tn = $anket['tn']. "_". ($fk = array_shift(array_slice(explode("_", $vopros[$vid]['tn'], 3), 2, 1)));
							if(implode("_", array_slice(explode("_", $anket['tn']), 0, 2)) != implode("_", array_slice(explode("_", $vopros[$vid]['tn']), 0, 2))){
								$fk = implode("_", array_slice(explode("_", $vopros[$vid]['tn']), 1, 999));
							}else{
								$fk .= "_id";
							}
							if(mpql(mpqw("SHOW TABLES WHERE Tables_in_{$conf['db']['name']}='". mpquot($tn). "'"), 0)){ # Есть ли таблица связанных элементов в базе данных
								$pk = array_shift(array_slice(explode("_", $tpl['index'][ $_GET['id'] ]['tn'], 3), 2, 1));
								foreach($ds as $v){
									mpqw("INSERT INTO {$tn} SET {$fk}=". (int)$v. ", {$pk}_id=". (int)$tpl['mysql_inset_id']);
								}
							}else{
								mpqw("Таблица {$tn} для связанных элементов не найдена");
							}
						}
					}
				}
			}
		}else{
			mpevent("Просмотр формы", $_SERVER['REQUERT_URI'], $anket['uid']);
		}
	}
}else{
	$tpl['index'] = mpql(mpqw("SELECT o.*, u.name AS uname FROM {$conf['db']['prefix']}{$arg['modpath']}_index AS o LEFT JOIN {$conf['db']['prefix']}users AS u ON o.uid=u.id ORDER BY o.id DESC"));
}

?>
