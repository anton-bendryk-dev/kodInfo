<?php 

	require_once('config.php');
	require_once('const.php');
	require_once('functions.php');
	
	
	$focuses_xml = file_get_contents("texts/letter_focus.xml");
	$f = new SimpleXMLElement($focuses_xml);
	$focuses = $f->xpath("//focus");
	
	if($_FILES['texts']['error'] == '0')
	{
		
		if(file_exists('texts/'.$_REQUEST['project'].'.txt'))
			unlink('texts/'.$_REQUEST['project'].'.txt');
		
		if(!move_uploaded_file($_FILES['texts']['tmp_name'], 'texts/'.$_REQUEST['project'].'.txt'))
		{
			echo '<br />Выбранный файл с текстом не удалось на сервер';
			echo '<a href="index.php">Назад</a>';
			exit();
		}
	}
	else
	{
		echo 'Код ошибки - '.$_FILES['texts']['error'];
		echo '<br />Ошибка при загрузке файла с текстом!';
		echo '<br /><br /><br /><a href="index.php">Назад</a>';
		exit();
	}
	
	if(file_exists('texts/'.$_REQUEST['project'].'.txt'))
	{
		$content = file_get_contents('texts/'.$_REQUEST['project'].'.txt');
		
		// echo $content;
		// exit();
		
		$texts = explode('=======', $content);
	    // $texts = explode('{^^^^^^^^^^^^^^^^^^^^delimiter^^^^^^^^^^^^^^^^^^^^}', $content);
	}
	else
	{
		echo 'Файл с текстом не найден!';
		exit();
	}
	
	$errors = array();
	$caution = array();
	
	$c = 1;
	$temples_per_lang = 0;
	$first_lang = '';
	$langspool = '';
	$uniq_langspool = array();
	$prevlang = '';
	$segments = array();
	$complete_texts = array();
	
	
	foreach($texts as $text)
	{
		if($c == count($texts))
			break;
		
		$temp_arr = array();
		$text_with_p = array();
		
		//$text = str_replace(PHP_EOL, '', $text);
		//echo $text.'<br /><br />';
		
		$l = trim(substr($text, (strpos($text, 'Language:')+strlen('Language:')), (strpos($text, 'Segment:') - (strpos($text, 'Language:')+strlen('Language:')))));
		$temp_arr[] = $l;
		
		if($c == 1)
			$first_lang = $l;
		
		$seg = trim(substr($text, (strpos($text, 'Segment:')+strlen('Segment:')), (strpos($text, 'Subject:') - (strpos($text, 'Segment:')+strlen('Segment:')))));
		$temp_arr[] = $seg;
		
		$subj = trim(substr($text, (strpos($text, 'Subject:')+strlen('Subject:')), (strpos($text, 'Text:') - (strpos($text, 'Subject:')+strlen('Subject:')))));
		$temp_arr[] = $subj;
		
		if($l == $first_lang)
		{
			$segments[$temples_per_lang+1] = $seg;
			$temples_per_lang++;
		}
		
		if(!in_array($l, $uniq_langspool))
			$uniq_langspool[] = $l;
		
		if($prevlang != $l)
		{
			$langspool .= $l.',';
			$prevlang = $l;
		}
				
		$temptext = substr($text, (strpos($text, 'Text:')+strlen('Text:')));
		$format_text = format_text($temptext, $projects[$_REQUEST['project']][3], $projects[$_REQUEST['project']][5], 0, $currency_aliases);
		
		$temp_arr[] = $format_text;
		$complete_texts[] = $temp_arr;
		
		$c++;
				
		// echo $l.'<br />';
		// echo $seg.'<br />';
		// echo $subj.'<br />';
		// echo $temptext.'<br />';
		
		// echo '<br /><br /><br />';
	}
	
	$langspool = substr($langspool, 0, (strlen($langspool) - 1));
	
	// foreach($complete_texts as $ct)
	// {
		 // echo '<b>Язык</b> - '.$ct[0].'<br /><br />';
		 // echo '<b>Сегмент</b> - '.$ct[1].'<br /><br />';
		 // echo '<b>Тема</b> - '.$ct[2].'<br /><br />';
		 // echo '<b>текст</b>'.$ct[3].'<br /><br />';
	// }
	

	// $template = file_get_contents('templates/neon.txt');
	// $str_text = implode($text_with_p);
	
	// $template = str_replace('{text}', $str_text, $template);
	// echo $template;
?>
<!DOCTYPE html PUBLIC"-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ru" xml:lang="ru" xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1 user-scalable=no" />
		<title>Шаг 2 - создать шаблоны</title>
		<link rel="stylesheet" href="css/reset.css" type="text/css">
		<link rel="stylesheet" href="css/style.css" type="text/css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		
	</head>
	<body>
		<div class="header">
	<?php
		$is_langs_valid = 1;
		foreach($complete_texts as $ct)
		{
			//Проверка 1
			//print_r($ct); 
			if(!empty(validation_tags($ct[0])))
				$errors[] = validation_tags($ct[0]);
			
			if(!empty(validation_tags($ct[1])))
				$errors[] = validation_tags($ct[1]);
			
			if(!empty(validation_tags($ct[2])))
				$errors[] = validation_tags($ct[2]);
			
			if(!empty(validation_tags($ct[3])))
				$errors[] = validation_tags($ct[3]);
			
			
			//Проверка 8
			
			if(is_lang_valid($ct[0], $_REQUEST['project']) !== false)
			{
				$errors[] = is_lang_valid($ct[0], $_REQUEST['project']);
				$is_langs_valid = 0;
			}
			
			
			//Проверка 4
			
			if(subject_length($ct[2]) !== false)
			{
				$subj_message = subject_length($ct[2]);
				
				if($subj_message[0] == 1)
					$caution[] = $subj_message[1];
				elseif($subj_message[0] == 2)
					$errors[] = $subj_message[1];
			}
			
			if(segment_length($ct[1]) !== false)
			{
				$seg_message = segment_length($ct[1]);
				
				if($seg_message[0] == 1)
					$caution[] = $seg_message[1];
				elseif($seg_message[0] == 2)
					$errors[] = $seg_message[1];
			}
		}
		
		//Проверка 3
		if($is_langs_valid == 1)
		{
			$langs_count = array();
			
			foreach(explode(',', $langspool) as $langg)
				$langs_count[$langg] = 0;
			
			foreach($complete_texts as $ct)
			{
				if(isset($langs_count[$ct[0]]))
					$langs_count[$ct[0]]++;
			}
			
			// print_r(array_count_values($langs_count));
			foreach($langs_count as $key=>$l)
				echo '<p>'. $key.' - '.$l.'</p>';
			
			//exit();
		}
			
			
		if(!empty($errors))
		{
			foreach($errors as $error_in_str)
			{
				echo '<div style="margin-bottom:15px; padding:10px;width: 600px; background-color: #ffe6e6; border-radius: 5px; border: 1px dashed #b30000;">';
				echo $error_in_str;
				echo '</div>';
			}
				
			echo '<button onclick="javascript:location.reload();">Ошибки исправлены</button>';
			
			exit();
		}
		else
			echo '<p>Ошибок нет</p>';
		
		
		if(!empty($caution))
		{
			foreach($caution as $caution_str)
			{
				echo '<div style="margin-bottom:15px; padding:10px;width: 600px; background-color: #e6ecff; border-radius: 5px; border: 1px dashed #001a66;">';
				echo $caution_str;
				echo '</div>';
			}
		}
		else
			echo '<p>Подзрительных значений нет</p>';
		?>
		</div>
		<div class="main">
			<div class="teamplates-settings-container">
				<!--<div>
					<a href ="index.php">На главную</a>
				</div>
				<div>
					<h1><?php echo"Проект -".$projects[$_REQUEST['project']][0]; ?></h1>
				</div>-->
				<div class="teamplates-settings">
					<!--<div>
						<h2>Фильтр</h2>
						<?php 
							$langs = explode(',', $langspool);
						
							for($ll = 1; $ll < count($langs); $ll++) 
							{
						?>
							<p><input type="checkbox" class = 'fl' id="fl<?php echo $ll; ?>" />&nbsp;&nbsp;<label style="text-decoration: underline;" for="fl<?php echo $ll; ?>"><?php echo $langs[$ll]; ?></label></p>
						<?php } ?>
					</div>-->
					<!--<input type="checkbox" id="templ_for_all">&nbsp;&nbsp;&nbsp;<span>Шаблон</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="templ_for_all_txt" style="padding: 5px; width: 100px;" />-->
					<div class = 'segments'>
					<?php for($i = 1; $i <= $temples_per_lang; $i++) { ?>
							<p><input type="text" class="tl<?php echo $i; ?>" style="padding: 5px; width: 70px;" />Шаблон для <span data-used-filter = '0' data-seg-number = '<?php echo ($i-1); ?>' style="text-decoration: underline; cursor: pointer;" ><?php echo $segments[$i]; ?></span></p>
						<?php } ?>
						<p><button id="set_t_ids" class="set_t_ids">Проставить ID шаблонов</button></p>
					</div>
					<div class="add-emoji-container">
						<input type="checkbox" id="set_emoji" class="checkbox">
						<span></span>
						<label for='set_emoji'>Добавить эмоджи втему письма</label>
					</div>
					<div class="add-banner-container">
						<input type="checkbox" id="banner_for_all" class="checkbox">
						<span></span>
						<label for='banner_for_all'>Баннер</label>
						<input type="text" id="banner_for_all_txt" class="banner_for_all_txt" />
						<input type="button" name='paste_banner' value="" class='paste checkbox' onclick="pasteBanners();" />
					</div>
					<div class="button-language">
						<select style="padding: 5px; border-radius:10px;" id='but_name_lang'>
							<option id='0'>all</option>
							<?php foreach($uniq_langspool as $lang) { ?>
								<option id='lang_<?php echo $lang; ?>'><?php echo $lang; ?></option>
							<?php } ?>
						</select>
						<span>Язык</span>
					</div>
					<div class="buttext_for_all-container">
						<input type="checkbox" id="buttext_for_all" class="checkbox">
						<span>Текст на кнопке</span>
						<input type="text" id="buttext_for_all_txt" class="banner_for_all_txt" />
                	</div>
                	<div class="but_name_select">
						<span>Посыл писем</span>
						<select style="padding: 5px; border-radius:10px;" id='but_name_select'>
							<option id='0'> --- </option>
							<?php foreach($focuses as $focus) { ?>
							<option id='<?php echo $focus['id']; ?>'><?php echo $focus['name']; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-container-btns">
						<a class="main-link" href ="index.php">На главную</a>
						<input class="gen-templates" type ="submit" value="Сгенерировать шаблоны" id = 'gen_templates' />
					</div>
				</div>
			</div>
			<div class="form-container">
				<form action="make_templ_result.php" name ="templ_params" method="post">
					<input type="hidden" name="project_id" value="<?php echo $_REQUEST['project']; ?>"/>
					<input type="hidden" name="langspool" value="<?php echo $langspool; ?>"/>
					<input type="hidden" name="set_emoji" value="0"/>
					<?php
						$c = 1;
					
						foreach($complete_texts as $ct)
						{
							// echo '<b>Язык</b> - '.$ct[0].'<br /><br />';
							// echo '<b>Сегмент</b> - '.$ct[1].'<br /><br />';
							// echo '<b>Тема</b> - '.$ct[2].'<br /><br />';
							// echo '<b>текст</b>'.$ct[3].'<br /><br />';
					?>
					<div class="div<?php echo $ct[0]; ?> teamplate-section" data-filtered = ''>
						<div class="teamplate-head">
								<label>ID ШАБЛОНА - </label>
								<input class="templ" type="text" name="templ_id<?php echo $c; ?>" style="padding: 5px; width: 100px;" />
						</div>
						<div class="teamplate-parameters">
							<?php echo '<b>Язык</b> - '.$ct[0]; ?>
						</div>
						<div class="teamplate-parameters">
							<?php echo '<b>Сегмент из текста</b> - '.$ct[1]; ?>
						</div>
						<div class="teamplate-parameters">
							<?php echo '<b>Тема</b> - '.$ct[2] ?>
						</div>
						<div class="teamplate-parameters">
							<?php echo '<b class="teamplate-parameters">Текст</b>'.'<div class="teamplate-text">'.$ct[3].'</div>'; ?>
						</div>
						<div style="margin: 60px 0 0 0;">
							<p class="teamplate-parameters">
								<label>Баннер - </label>
								<input type="text" class="banner" name="banner_id<?php echo $c; ?>" style="padding: 5px; width: 250px;" />
							</p>
							<p class="teamplate-parameters">
								<label>Текст на кнопке - </label>
								<input type="text" class="but_name_<?php echo trim($ct[0]); ?>" name="but_text<?php echo $c; ?>" style="padding: 5px; width: 250px;" />
							</p>
						</div>
					</div>
					<?php 
						$c++;
						}
					?>
				</form>
			</div>
		</div>
	</body>
	<!-- <script text="text/javascript" src="js/scripts.js"></script> -->
		
	<script text="text/javascript">
		$('#banner_for_all').change(function() {
			if($(this).prop('checked') == true)
			{
				var str = $('input[name="langspool"]').val();
				var langs = str.split(',');
				
				for(var i = 0; i < langs.length; i++)
					$('.div'+langs[i]+':visible .banner').val($('#banner_for_all_txt').val());
				
				//$('.banner').val($('#banner_for_all_txt').val());
				//alert($('#banner_for_all_txt').val());
			}
			else
				$('.banner').text('');
		});
		
		$('#buttext_for_all').change(function() {
			if($(this).prop('checked') == true)
			{
				var str = $('input[name="langspool"]').val();
				var langs = str.split(',');
				var lang_filter = $('#but_name_lang option:selected').val();
				
				if(lang_filter == 'all')
					{	
						for(var i = 0; i < langs.length; i++)
							$('.div'+langs[i]+':visible input[class^="but_name"]').val($('#buttext_for_all_txt').val());
					}
					else
					{
						for(var i = 0; i < langs.length; i++)
						{
							if(langs[i] == lang_filter)
								$('.div'+langs[i]+':visible input[class^="but_name"]').val($('#buttext_for_all_txt').val());
						}
							
						
						for(var i = 0;i<names.length;i++)
						{
							var bname = names[i].split(',');
							
							if(bname[0] == lang_filter)
							{
								if($('.but_name_'+bname[0]).length > 0)
									$('.but_name_'+bname[0]).val(bname[1]);
							}
						}
					}
				
				// alert($('#buttext_for_all_txt').val());
			}
			else
				$("input[class^= 'but_name']").text('');
		});
		
		$('#set_emoji').change(function() {
			if($(this).prop('checked') == true)
				$('input[name="set_emoji"]').val('1');
			else
				$('input[name="set_emoji"]').val('0');
		});
		
		$('#but_name_select').change(function(){
			if($('#but_name_select option:selected').attr('id') != '0')
			{
				var but_names_id = $('#but_name_select option:selected').attr('id');
				var lang_filter = $('#but_name_lang option:selected').val();
				
				$.get("get_but_names.php?bn_id="+but_names_id, function( data ) {
					var names = data.split('|');
					
					if(lang_filter == 'all')
					{	
						for(var i = 0;i<names.length;i++)
						{
							var bname = names[i].split(',');
							if($('.but_name_'+bname[0]).length > 0)
								$('.but_name_'+bname[0]).val(bname[1]);
						}
					}
					else
					{
						for(var i = 0;i<names.length;i++)
						{
							var bname = names[i].split(',');
							
							if(bname[0] == lang_filter)
							{
								if($('.but_name_'+bname[0]).length > 0)
									$('.but_name_'+bname[0]).val(bname[1]);
							}
						}
					}
				});
			}
			else
				alert(0);
		});
		
		$('#templ_for_all').change(function() {
			if($(this).prop('checked') == true)
			{
				$('.templ').val($('#templ_for_all_txt').val());
				//alert($('#buttext_for_all_txt').val());
			}
			else
				$('.templ').text('');
		});
		
		$('#set_t_ids').click(function() {
			var str = $('input[name="langspool"]').val();
			var langs = str.split(',');
			
			for(var i = 0; i < langs.length; i++)
			{
				for(var u = 0; u < $('.div'+langs[i]+' .templ').length; u++)
					$('.div'+langs[i]+' .templ:eq('+u+')').val($('input.tl'+(u+1)+'').val());
			}
			
			//alert($('.divru .templ').length);
		});
		
		$('.segments p span').click(function() {
			var is_used_filter = $(this).attr('data-used-filter');
			var str = $('input[name="langspool"]').val();
			var langs = str.split(',');
				
			// alert(is_used_filter);
			if(is_used_filter == 0)
			{
				$('div[class^="div"]').fadeOut(100);
				var seg_number = $(this).attr('data-seg-number');
				
				for(var i = 0; i < langs.length; i++)
				{
					// alert($('.div'+langs[i]).eq(seg_number).length);
					$('.div'+langs[i]).eq(seg_number).fadeIn(100);
					$('.div'+langs[i]).eq(seg_number).attr('data-filtered', 1);
				}
				
				$(this).attr('data-used-filter', 1);
				$(this).css("color","blue").css("font-weight","bold");
				//alert($(this).attr('data-seg-number'));
			}
			else
			{
				$('div[class^="div"]').fadeIn(100);
				
				$('div[class^="div"]').attr('data-filtered', 0);
				$(this).attr('data-used-filter', 0);
				
				$(this).css("color","#222").css("font-weight","normal");
			}
		});
		
		
	</script>
</html>