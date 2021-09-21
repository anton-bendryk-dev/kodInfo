<?php

$config = file_get_contents('./javascript/projects.JSON');
$config = json_decode($config, true);
$project_langs = array();

foreach($config as $arr)
{
	//print_r($arr['language']);
	if($arr['id'] == $_REQUEST['projectId'])
		$project_langs = $arr['language'];
	
	break;
}

$slash = strpos(PHP_OS, 'Linux') !== false ? '//' : '\\';
$dir_to_save = 'texts'.$slash.$_REQUEST['projectName'].$slash.$_REQUEST['projectType'].$slash.'textsData.JSON';

$data = file_get_contents($dir_to_save);
$data = json_decode($data, true);




if(!$_REQUEST['id'])
{
	$max_id = 0;
	
	if(count($data) > 0)
	{
		foreach($data as $arr)
		{
			echo $arr['id'];
			$id = $arr['id']*1;
			if($id > $max_id)
				$max_id = $id;
		}
	}
			
	$final_arr = array('id'=>$max_id+1);
}
else
	$final_arr = array('id'=>$_REQUEST['id']);

// echo '<pre>';
// print_r($data);
// echo '</pre>';	
// exit;

// $final_arr['projectId'] = $_REQUEST['projectId'];
$final_arr['projectName'] = $_REQUEST['projectName'];
$final_arr['projectType'] = $_REQUEST['projectType'];
$final_arr['date'] = $_REQUEST['date'];
$final_arr['projectSegment'] = $_REQUEST['projectSegment'];
$final_arr['textLanguage'] = $project_langs;

foreach($project_langs as $lang)
{
	$temp_arr = array();
	
	foreach($_REQUEST as $key=>$param)
	{
		if(strpos($key, '_'.$lang) !== false)
		{
			$t_key = str_replace('_'.$lang, '', $key);
			$temp_arr[] = array($t_key, $param);
		}
	}
	
	$final_arr[$lang] = $temp_arr;
}

// echo '<pre>';
// print_r($final_arr);
// echo '</pre>';
// exit;


if(!$_REQUEST['id'])
{
	// foreach($_REQUEST as $key=>$val)
		// $final_arr[$key] = $val;

	$data[] = $final_arr;
}
else
{
	if(count($data) > 0)
	{
		foreach($data as $key=>$arr)
		{
			if($arr['id'] == $_REQUEST['id'])
			{
				$data[$key] = $final_arr;
				break;
			}
		}
	}
	else
		$data = $final_arr;
}

$json = json_encode($data);

if(file_put_contents($dir_to_save, $json))
	echo 'Текст сегмента '.$_REQUEST['projectSegment'].' сохранился';
else
	echo 'Ошибка при сохранении текста для сегмента '.$_REQUEST['projectSegment'];
?>