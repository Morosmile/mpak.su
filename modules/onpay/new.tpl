<? if($conf['tpl']['operation']): ?>
	<style>
		#operation { border-color:#eee; background-color:#eee; opacity:0.7; }
		#operation tr td {padding:5px;}
		.ar { text-align:right; }
		.al { text-align:left;}
	</style>
	<div style="text-align:center; margin:50px;">
		<table id="operation" style="width:100%;" cellspacing="0" cellpadding="3" border="1">
			<tr>
				<td class="ar" style="width:50%;">Счет</td>
				<td class="al">#<?=$conf['tpl']['operation']['id']?></td>
			</tr>
			<tr>
				<td class="ar">Сумма:</td>
				<td class="al"><?=$conf['tpl']['operation']['sum']?> <!-- [settings:<?=$arg['modpath']?>_currency] --></td>
			</tr>
			<tr>
				<td class="ar">Тип:</td>
				<td class="al"><?=$conf['tpl']['operation']['type']?></td>
			</tr>
			<tr>
				<td class="ar">Комментарий:</td>
				<td class="al">&nbsp;<?=$conf['tpl']['operation']['description']?></td>
			</tr>
			<? if($conf['tpl']['operation']['status']): ?>
			<tr>
				<td class="ar">Статус:</td>
				<td class="al"><span style=color:green;>Оплачен</span></td>
			</tr>
			<? else: ?>
			<tr>
				<td class="ar">Ссылка для оплаты:</td>
				<td class="al">
					<a href="/<?=$arg['modname']?>/<?=$conf['tpl']['operation']['id']?>">
						Оплатить
					</a>
				</td>
			</tr>
			<? endif; ?>
		</table>
	</div>
<? else: ?>
	<style>
		.typeButton {float:right !important;}
	</style>
	<form method="post" style="width:400px; text-align:center; margin:0 auto;">
		<div style="margin: 100px 10px; text-align: center;">
			счет на сумму <input id="sum" type="text" name="sum" value="<?=($_GET['id'] ?: 99)?>" style="width:50px;"> <!-- [settings:<?=$arg['modpath']?>_currency] -->
				<input type="submit" value="Добавить">
			<div>
				<textarea name="description" title="Ваш комментарий" style="width:100%; margin-top:5px;"><?=$_GET['description']?></textarea>
			</div>
		</div>
	</form>
<? endif; ?>