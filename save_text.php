<?php
// print_r($_POST);
$config = file_get_contents('javascript/projects.json');
$config = json_decode($config, true);
$project_langs = array();

foreach($config as $arr)
{
	//print_r($arr['language']);
	if($arr['id'] == $_POST['projectId'])
	{
		$project_langs = $arr['language'];
		break;
	}
	
}

$slash = strpos(PHP_OS, 'Linux') !== false ? '//' : '\\';
$dir_to_save = 'texts'.$slash.$_POST['projectName'].$slash.$_POST['projectType'].$slash.'textsData.json';

$data = file_get_contents($dir_to_save);
$data = json_decode($data, true);




if(!$_POST['id'])
{
	$max_id = 0;
	
	if(count($data) > 0)
	{
		foreach($data as $arr)
		{
			$id = $arr['id']*1;
			if($id > $max_id)
				$max_id = $id;
		}
	}
			
	$final_arr = array('id'=>$max_id+1);
}
else
	$final_arr = array('id'=>$_POST['id']);

// echo '<pre>';
// print_r($data);
// echo '</pre>';	
// exit;

// $final_arr['projectId'] = $_POST['projectId'];
$final_arr['projectName'] = $_POST['projectName'];
$final_arr['projectType'] = $_POST['projectType'];
$final_arr['date'] = $_POST['date'];
$final_arr['projectSegment'] = $_POST['projectSegment'];
$final_arr['textLanguage'] = $project_langs;

foreach($project_langs as $lang)
{
	$temp_arr = array();
	
	foreach($_POST as $key=>$param)
	{
		if(strpos($key, '_'.$lang) !== false)
		{
			$t_key = str_replace('_'.$lang, '', $key);
			$temp_arr[] = array($t_key, $param);
		}
	}
	
	$final_arr[$lang] = $temp_arr;
}

 //echo '<pre>';
 //print_r($final_arr);
 //echo '</pre>';
 //exit;


if(!$_POST['id'])
{
	// foreach($_POST as $key=>$val)
		// $final_arr[$key] = $val;

	$data[] = $final_arr;
}
else
{
	if(count($data) > 0)
	{
		foreach($data as $key=>$arr)
		{
			if($arr['id'] == $_POST['id'])
			{
				$data[$key] = $final_arr;
				break;
			}
		}
	}
	else
		$data = $final_arr;
}

//print_r($data);
$json = json_encode($data);

if(file_put_contents($dir_to_save, $json))
	echo 'Текст сегмента '.$_POST['projectSegment'].' сохранился';
else
	echo 'Ошибка при сохранении текста для сегмента '.$_POST['projectSegment'];
?>