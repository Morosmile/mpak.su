<? die; # Заголовка блока
################################# php код #################################

//if(array_key_exists('blocks', $_GET['m']) && array_key_exists('null', $_GET) && ($_GET['id'] == $arg['blocknum']) && $_POST){};

$tpl['index'] = qn("SELECT * FROM {$conf['db']['prefix']}{$arg['modpath']}_{$arg['fn']}");

################################# верстка ################################# ?>
<? foreach($tpl['index'] as $index): ?>
	<div><a href="/<?=$arg['modname']?>/<?=$index['id']?>"><?=$index['name']?></a></div>
<? endforeach; ?>