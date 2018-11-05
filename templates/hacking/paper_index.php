<?php
/* function objToHTML(&$array,&$html='',&$depth=1,$obj_name="")
{

	if($array['type']=="section")
	{
	$html .= "<h{$depth}>".str_repeat("-",$depth)."{$array['name']}</h{$depth}>";
		$depth++;
		foreach($array["paragraphs"] as $obj => $info)
			$html = objToHTML($info,$html,$depth,$obj_name);
		$depth = 1;
	}
	else if ($array['type']=="ref")
	{
		$html .=  "<p>".str_repeat("&nbsp;-",$depth)."> <a href=\"?p={$array['href']}\">{$array['name']}</a></p>";
	}
	
	return $html;
} */
?>


<h1><?= $this->PAPER['info']['title']?> </h1>
<?php foreach($this->PAPER['index'] as $obj => $info)
{

	$html = ""; 
	$depth=1;
	echo objToHTML($info,$html,$depth,$obj); 
}