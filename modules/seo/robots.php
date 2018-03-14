<?

$_GET['null'] = header("Content-Type: text/plain; charset=utf-8");

if(!is_array($SEO_ROBOTS = call_user_func(function() use($conf, $arg){
	if(get($conf, 'settings', 'seo_robots')){
		return rb('robots');
	}else{ return []; }}))){ mpre("Список сайтов для установки правил не найден");
}elseif(!is_array($SEO_ROBOTS_AGENT = call_user_func(function() use($conf, $arg){
	if(get($conf, 'settings', 'seo_robots_agent')){
		return rb('robots_agent');
	}else{ return []; }}))){ mpre("Список агентов не задан в админке");
}elseif(!is_array($SEO_ROBOTS_DISALLOW = call_user_func(function() use($conf, $arg){
	if(get($conf, 'settings', 'seo_robots_disallow')){
		return rb('robots_disallow');
	}else{ return []; }}))){ mpre("Список запрещенных адресов не задан");
}elseif(!is_array($SEO_ROBOTS_ALLOW = call_user_func(function() use($conf, $arg){
	if(get($conf, 'settings', 'seo_robots_allow')){
		return rb('robots_allow');
	}else{ return []; }}))){ mpre("Список запрещенных адресов не задан");
}elseif(get($conf, 'settings', 'themes_index') && !is_array($themes_index = get($conf, 'themes', 'index'))){ mpre("Определение хоста");
}elseif(!empty($themes_index) && get($themes_index, 'hide') && (print("User-agent: *\n\nDisallow: /"))){ mpre("Сайт скрыт, так как является <a href=''>зеркалом</a>");
}else{ //mpre("Количество элементов ". count($_SEO_ROBOTS_DISALLOW));
	foreach(rb($SEO_ROBOTS_AGENT, "name") + ['*'=>['name'=>'*'], 'Yandex'=>['name'=>'Yandex']] as $seo_robots_agent){// mpre($seo_robots_agent);
		if(!print("User-agent: {$seo_robots_agent['name']}\n")){
		}elseif(get($conf, 'themes', 'index', 'hide')){ echo "\nDisallow: /";
		}elseif(!is_array($seo_robots = rb($SEO_ROBOTS, 'themes_index', (empty($themes_index) ? false : get($themes_index, 'id'))))){ mpre("Ошибка определения робота", $SEO_ROBOTS, $seo_robots, get($conf, 'themes', 'index', 'id'));
		}elseif(!$robots_id = "[0,NULL,". (int)get($seo_robots, 'id'). "]"){ mpre("Фильтр робота");
		}elseif(!$robots_agent_id = "[0,NULL,". get($seo_robots_agent, 'id'). "]"){ mpre("Фильтр агента");
		}elseif(!is_array($_SEO_ROBOTS_DISALLOW = rb($SEO_ROBOTS_DISALLOW, "robots_agent_id", "robots_id", "id", $robots_agent_id, $robots_id))){ mpre("Список исключений агента");
		}elseif(!is_array($_SEO_ROBOTS_ALLOW = rb($SEO_ROBOTS_ALLOW, "robots_agent_id", "robots_id", "id", $robots_agent_id, $robots_id))){ mpre("Список разрешений агента");
	//	}elseif(!$SEO_ROBOTS_ALL = array_merge($_SEO_ROBOTS_DISALLOW, $_SEO_ROBOTS_ALLOW)){ mpre("ошибка соединения массивов");
		}else{// mpre($SEO_ROBOTS, $seo_robots_agent, $robots_id, $robots_agent_id);
			foreach($_SEO_ROBOTS_DISALLOW as $seo_robots_disallow){
				echo "\nDisallow: {$seo_robots_disallow['name']}";
			}
			foreach($_SEO_ROBOTS_ALLOW as $seo_robots_allow){
				echo "\nAllow: {$seo_robots_allow['name']}";
			}
			if($seo_robots_agent['name'] == "Yandex"){
				if(!empty($themes_index) && get($themes_index, 'index_id') && ($index = rb("themes-index", "id", get($themes_index, 'index_id')))){
					echo "\nHost: {$index['name']}";
				}else{
					echo "\n\nHost: {$_SERVER['HTTP_HOST']}";
				}
			} echo "\n\nSitemap: http://{$_SERVER['HTTP_HOST']}/sitemap.xml\n\n";
		}
	}
}


