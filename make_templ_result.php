<?php 
	require_once('functions.php');
	require_once('const.php');
	require_once('config.php');
	
	if(isset($_REQUEST['set_emoji']))
		$emojies = get_emojies();
		
	$for_test_temp = array();
	foreach($_REQUEST as $key=>$t)
	{
		if(strpos($key, 'templ_id') !== false)
			$for_test_temp[$t] = '';
	}
	
	$content = file_get_contents('texts/'.$_REQUEST['project_id'].'.txt');
	// $content = explode('{^^^^^^^^^^^^^^^^^^^^delimiter^^^^^^^^^^^^^^^^^^^^}', $content);
	
	// $text = substr($content[0], (strpos($content[0], '{text}')+strlen('{text}')), (strpos($content[0], '{/text}') - (strpos($content[0], '{text}')+strlen('{text}'))));
	// $style = $projects[$_REQUEST['project_id']][3];
	
	// $formatted_text = format_text($text, $style, 0);
	
	// echo $formatted_text;
	// exit();
	
	// $is_delimiter = $projects[$_REQUEST['project_id']][5];
	
	
	//определяем id шаблонов и папки для них
	$existed_t = scandir('generated');
	
	foreach($_REQUEST as $key=>$val)
	{
		if(strpos($key, 'templ_id') !== false)
		{
			if(!in_array($val, $existed_t))
			{
				echo"Папки $val нет";
				
				mkdir('generated/'.$val);
				
				foreach(explode("|", $projects[$_REQUEST['project_id']][2]) as $lang)
				{
					fopen('generated/'.$val.'/'.$lang.'_params.txt', 'w');
					fopen('generated/'.$val.'/'.$lang.'_html.txt', 'w');
				}
			}
		}
	}
	
	
	$content = file_get_contents('texts/'.$_REQUEST['project_id'].'.txt');
	
	if(!$content)
	{
		echo 'текст не взят';
		exit();
	}
	else
		$texts = explode('=======', $content);
?>
<!DOCTYPE html PUBLIC"-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="ru" xml:lang="ru" xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1 user-scalable=no" />
		<title>Шаг 3 - создать шаблоны</title>
		<link rel="stylesheet" href="css/reset.css" type="text/css">
		<link rel="stylesheet" href="css/style.css" type="text/css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="js/zclip/jqueryzclip.js"></script>
	</head>
	<body>
		<div class="teamplates-container">
		<?php
			$c = 1;
			
			$for_save = array();
			foreach($texts as $text)
			{
				if($c == count($texts))
					break;
				
				$lang = trim(substr($text, (strpos($text, 'Language:')+strlen('Language:')), (strpos($text, 'Segment:') - (strpos($text, 'Language:')+strlen('Language:')))));
				$segment = trim(substr($text, (strpos($text, 'Segment:')+strlen('Segment:')), (strpos($text, 'Subject:') - (strpos($text, 'Segment:')+strlen('Segment:')))));
				$subject = trim(substr($text, (strpos($text, 'Subject:')+strlen('Subject:')), (strpos($text, 'Text:') - (strpos($text, 'Subject:')+strlen('Subject:')))));
				
				$banner = $_REQUEST['banner_id'.$c];
				$but_text = $_REQUEST['but_text'.$c];
				
				$clean_text = substr($text, (strpos($text, 'Text:')+strlen('Text:')));
				$style = $projects[$_REQUEST['project_id']][3];
				$is_del = $projects[$_REQUEST['project_id']][5];
				$formatted_text = format_text($clean_text, $style, $is_del, 0, $currency_aliases);
				
				// echo $formatted_text;
				// exit();
				
				$templ = file_get_contents('templates/'.$_REQUEST['project_id'].'/'.$lang.'.txt');
				
				if(!$templ)
				{
					echo '<br />Язык - '.$lang.'<br />Проект - '.$projects[$_REQUEST['project_id']][0].'<br />Файл с макетом шаблона не найден!';
					continue;
				}
				
				
				//--------------------------------МАНИПУЛЯЦИИ С ТЕМОЙ-----------------------------------
				
				$subject = str_replace('’', '', $subject);
				$subject = str_replace('\'', '', $subject);
				$subject = str_replace('“', '', $subject);
				$subject = str_replace('"', '', $subject);
				$subject = str_replace('!', '.', $subject);
				$subject = str_replace(PHP_EOL, '', $subject);
				$subject = str_replace('\r\n', '', $subject);
				
				$subject_last_symbol = substr($subject, (strlen($subject)-1), 1);
				
				if($subject_last_symbol == '.')
					$subject = substr($subject, 0, (strlen($subject)-1));
				
				$subject = make_curr_var($subject, $currency_aliases);
				$subject = str_replace(array('<strong>', '</strong>'), '', $subject);
				
				$intext_subject = $subject;
				$subject = add_name_var($subject);
				
				if($emojies)
				{
					$rand_emoji = $emojies[rand(0, count($emojies)-1)];
					$subject = $rand_emoji.$subject;
				}
										
								
				$template = str_replace('{banner}', $banner, $templ);
				
				if(strpos($template, '{subject}'))
					$template = str_replace('{subject}', $intext_subject, $template);
				
				$template = str_replace('{text}', $formatted_text, $template);
				$template = str_replace('{button_name}', $but_text, $template);
				$template = str_replace(PHP_EOL, '', $template);
				$template = str_replace('\'', '&apos;', $template);
				$template = trim($template);
				
				
				// echo $_REQUEST['templ_id'.$c].'<br /><br />';
				// echo $lang.'<br /><br />';
				// echo $segment.'<br /><br />';
				// echo $subject.'<br /><br />';
				// echo $banner.'<br /><br />';
				// echo $template.'<br /><br />';
				// echo $but_text.'<br /><br />';
				// echo '<br /><br />';
				
				$params = $_REQUEST['templ_id'.$c]."<>";
				$params .= $lang."<>";
				
				$params .= $subject."<>";
                $params .= $banner."<>";
                //$params .= parse_url($banner, PHP_URL_PATH)."<>";
                                
                $news_text = format_text($clean_text, '', 0, 0, $currency_aliases);
                $news_text = str_replace('’', '', $news_text);
				$news_text = str_replace('\'', '', $news_text);
                $params .= $news_text;
				
				echo '<div class="teamplates-settings" style="position: relative; margin-top: 10px;">';
				echo '<b style="font-size: 30px;color: green;">'.$_REQUEST['templ_id'.$c].'['.$lang.'], '.$segment.'</b><br />';
					
				if(file_put_contents('generated/'.$_REQUEST['templ_id'.$c].'/'.$lang.'_params.txt', $params))
					echo '<p class="parameters">Параметры для шаблона успешно сохранёны!</p>';
				
				
				if(@file_put_contents('generated/'.$_REQUEST['templ_id'.$c].'/'.$lang.'_html.txt', $template))
				{
					echo '<p class="parameters">HTML шаблона успешно сохранён!</p>';
					
					echo '<div>
							<div id="iframe'.$_REQUEST['templ_id'.$c].$lang.'" class="template-example">
								<a href="javascript:void(0);" class="closeiframe">Закрыть</a>
								<p style="margin:20px 60px;font-size: 1.1em;">'.$subject.'</p>
								<iframe src="templ_preview.php?tid='.$_REQUEST['templ_id'.$c].'&lang='.$lang.'" width="700" height="600" align="left" style="padding: 50px; border: none; min-width: 800px;">
									Тут показывается шаблон шириной 600px+
								</iframe>
							</div>
							<div class="parameters"><b style="color: #34b1da">Тема</b><span><p class="parameters">'.$subject.'</p></span></div>
							<div class="form-container-btns">
								<a class="main-link" onclick="viewHTML('.$_REQUEST['templ_id'.$c].', \''.$lang.'\')">Просмотреть HTML</a>
								<a href ="javascript:void(0);" class ="htmlpreview gen-templates" id="'.$_REQUEST['templ_id'.$c].$lang.'">Превью шаблона</a>
							</div>
						</div>';
					
					// echo '<div style="margin: 10px 0;">
					// <p><b>Тема</b> - <span>'.add_name_var($subject).'</span></p>
					// <p><button onclick="copyTempl(\''.$_REQUEST['templ_id'.$c].'\', \''.$lang.'\');">Загрузить HTML</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					// <button class ="copy_but" id="'.$_REQUEST['templ_id'.$c].$lang.'">Скопировать</button>
					// </p><span style="display:none;" id="copytext'.$_REQUEST['templ_id'.$c].$lang.'"></span></div>';
					
					// echo '<div style="width: 700px; height: 500px; overflow: scroll;">';;
					// echo $template;
					// echo '</div>';
				}
				
				//$for_save[$_REQUEST['templ_id'.$c]] = 
				
				echo '</div>';
				
				$temp = $for_test_temp[$_REQUEST['templ_id'.$c]];
				$temp .= $lang.'|';
				$for_test_temp[$_REQUEST['templ_id'.$c]] = $temp;
				
				$c++;
			}
			
			$for_test = array();
			foreach($for_test_temp as $key=>$t)
				$for_test[$key] = substr($t, 0, strlen($t)-1);
			
			$save_str = '';
			$save_c = 1;
			foreach($for_test as $key=>$ft)
			{
				if($save_c != count($for_test))
					$save_str .= $key.','.$ft.'{br}';
				else
					$save_str .= $key.','.$ft;
				
				$save_c++;
			}
			
			$templates_str = '';
			foreach($for_test as $key=>$ft)
				$templates_str .= $key.',';
			
			$templates_str = substr($templates_str, 0, strlen($templates_str)-1);
			
			//echo $save_str;
		?>
		</div>
		<p id='for_save_p' style="display: none;"><?php echo $save_str; ?></p>
		<p id='for_test_p' style="display: none;"><?php echo $_REQUEST['project_id'].'|'.$templates_str; ?></p>
		<div class="save-test-container">
        <div class="creator-container">
            <p class="creator-name">Вадим</p>
            <div class="form-container-btns">
                <a href="javascript:void(0);" id="s1" class='forsave' data-is-saving-now=''>Сохранить</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="javascript:void(0);" id="t1" class='fortest' data-is-testing-now=''>Тестировать</a>
            </div>
        </div>
        <div class="pull-container">
            <ul style="list-style: none;">Пул для тестов
                <li class="pull-box" style="padding-top: 10px;">
                    <input type='radio' name='pool' value='1' id='pool1' />
                    <span></span>
                    <label for="pool1">Pool 1</label>
                </li>
                <li class="pull-box" style="padding-top: 10px;">
                    <input type='radio' name='pool' value='2' id='pool2' />
                    <span></span>
                    <label for="pool2">Pool 2</label>
                </li>
                <li class="pull-box" style="padding-top: 10px;">
                    <input type='radio' name='pool' value='3' id='pool3' />
                    <span></span>
                    <label for="pool3">Pool 3</label>
                </li>
                <li class="pull-box" style="padding-top: 10px;">
                    <input type='radio' name='pool' value='4' id='pool4' />
                    <span></span>
                    <label for="pool4">Pool 4</label>
                </li>
                <li class="pull-box" style="padding-top: 10px;">
                    <input type='radio' name='pool' value='5' id='pool5' />
                    <span></span>
                    <label for="pool5">Pool 5</label>
                </li>
            </ul>
        </div>
        <div class="creator-container">
            <p class="creator-name">Георгий</p>
            <div class="form-container-btns">
                <a href="javascript:void(0);" id="s2" class='forsave' data-is-saving-now=''>Сохранить</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="javascript:void(0);" id="t2" class='fortest' data-is-testing-now=''>Тестировать</a>
            </div>
        </div>
        <div class="creator-container">
            <p class="creator-name">Женя</p>
            <div class="form-container-btns">
                <a href="javascript:void(0);" id="s3" class='forsave' data-is-saving-now=''>Сохранить</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="javascript:void(0);" id="t3" class='fortest' data-is-testing-now=''>Тестировать</a>
            </div>
        </div>
        <div></div>
        <div class="creator-container">
            <p class="creator-name">Антон</p>
            <div class="form-container-btns">
                <a href="javascript:void(0);" id="s4" class='forsave' data-is-saving-now=''>Сохранить</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="javascript:void(0);" id="t4" class='fortest' data-is-testing-now=''>Тестировать</a>
            </div>
        </div>
    </div>
    <div class="bottom-btns-container">
        <div class="form-container-btns">
            <a href="index.php" style="text-decoration: none; font-size: 1.3em; color: #fff; padding: 10px; background-color:#cc0000; border: 1px solid #ff80aa;border-radius: 5px;">
            Сделать новый шаблон</a>
            <a class="gen-templates" href="javascript:void(0);" class="melissa" id="2246">Открыть в Мелиссе</a>
        </div>
    </div>
	</body>
	<script text="text/javascript">
		$('.htmlpreview').click(function(){
			var iframe = '#iframe'+$(this).attr('id');
			$(iframe).toggle();
		});
		
		$('.closeiframe').click(function(){
			$(this).parent().toggle();
		});
		
		function viewHTML(tId, lang){
			window.open('generated/'+tId+'/'+lang+'_html.txt', '_blank',"width=400,height=400");
		}
		
		
		function get_save_status(fromWho){
			//alert("Проверка");
			
			$.ajax({
				type:"POST",
				url:"save_status.php",
				dataType:"json",
				data:"action=check&fromwho="+fromWho,
				success: function(data){
					if(data.message == 'success'){
						if(data.status == 1)
						{
							//alert('Ещё сохраняется ...');
							setTimeout(get_save_status, 5000, fromWho);
						}
						else if(data.status == 2)
						{
							alert('Сохранилось!');
							$('#'+fromWho).css('background-color', '#00cc66').css('border', '2px solid #00cc66').css('color', '#fff').attr('data-is-saving-now', '');
						}
						else
						{
							alert('Неизвестный статус - '+data.status);
							return false;
						}
					}
					else if(data.message == 'error')
						alert("Ошибка про получении статуса\n\n"+data.error_str);
				}
			});
			
		}
		
		function get_test_status(fromWho){
			//alert("Проверка");
			
			$.ajax({
				type:"POST",
				url:"test_status.php",
				dataType:"json",
				data:"action=check&fromwho="+fromWho,
				success: function(data){
					if(data.message == 'success'){
						if(data.status == 1)
						{
							//alert('Ещё сохраняется ...');
							setTimeout(get_test_status, 5000, fromWho);
						}
						else if(data.status == 2)
						{
							alert('Тесты отправлены, проверяй!!');
							$('#'+fromWho).css('background-color', '#00cc66').css('border', '2px solid #00cc66').css('color', '#fff').attr('data-is-testing-now', '');
						}
						else
						{
							alert("Тестирование\n\nНеизвестный статус -"+data.status);
							return false;
						}
					}
					else if(data.message == 'error')
						alert("Тестирование\n\nОшибка про получении статуса\n\n"+data.error_str);
				}
			});
		}
		
		
		$('.forsave').click(function(){
			var id = $(this).attr('id');
			var savestr = $('#for_save_p').text();
			// alert(savestr);  
			// alert(id);  
			
			if($('#'+id).attr('data-is-saving-now') == '1')
			{
				alert("Идёт сохранение шаблонов...\n\nПосмотри пока в окошко или чай завари :)");
				return false;
			}
			
			$.get("forsave.php?fromwho="+id+"&savestr="+savestr, function( data ) {
				if(data == (id+'-1'))
				{
					$.ajax({
						type:"POST",
						url:"save_status.php",
						dataType:"json",
						data:"action=put&fromwho="+id+"&status=1",
						success: function(status_data){
							if(status_data.status == 'ok')
							{
								// alert(status_data);
								$('#'+id).css('background-color', '#ffccb3').css('border', '2px solid #ffaa80').css('color', '#fff');
								$('#'+id).attr('data-is-saving-now', '1');
								
								setTimeout(get_save_status, 5000, id);
							}
							else
							{
								alert("Ошибка!\r\n"+status_data);
								return false;
							}
						}
					});
				}
				else if(data == id+'-0')
				{
					alert(data);
					$('#'+id).css('background-color', 'red').css('border', '2px solid red').css('color', '#fff');
				}
				else
				{
					alert(data);
					$('#'+id).css('background-color', 'red').css('border', '2px solid red').css('color', '#fff');
				}
			});
		});
		
		$('.fortest').click(function(){
			var id = $(this).attr('id');
			var teststr = $('#for_test_p').text();
			var pool = $('input[name=pool]:checked').val();
			
			if(pool === undefined)
			{
				alert('Выберите пул для тестирования');
				return false;
			}
			else
				teststr += '|'+pool;
				
			
			$.get("fortest.php?fromwho="+id+"&teststr="+teststr, function( data ) {
				if(data == (id+'-1'))
				{
					$.ajax({
						type:"POST",
						url:"test_status.php",
						dataType:"json",
						data:"action=put&fromwho="+id+"&status=1",
						success: function(status_data){
							if(status_data.status == 'ok')
							{
								$('#'+id).css('background-color', '#ffccb3').css('border', '2px solid #ffaa80').css('color', '#fff');
								$('#'+id).attr('data-is-testing-now', '1');
								
								setTimeout(get_test_status, 5000, id);
							}
							else
							{
								alert("Ошибка!\r\n"+status_data);
								return false;
							}
						}
					});
					//$('#'+id).css('background-color', '#00cc66').css('border', '2px solid #00cc66').css('color', '#fff');
				}
				else if(data == id+'-0')
				{
					alert(data);
					$('#'+id).css('background-color', 'red').css('border', '2px solid red').css('color', '#fff');
				}
				else
				{
					alert(data);
					$('#'+id).css('background-color', 'red').css('border', '2px solid red').css('color', '#fff');
				}
			});
		});
		
		$('.melissa').click(function() {
			var ids = $(this).attr('id');
			ids_arr = ids.split(',');
			
			for(var i = 0; i < ids_arr.length; i++)
				window.open('https://thiscrm.co/admin/crm/email/edit/'+ids_arr[i], '_blank');
		});
	</script>
</html>