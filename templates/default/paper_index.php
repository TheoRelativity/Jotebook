<h1><?= $this->PAPER['info']['title']?> </h1>
<?php
if(count($this->PAPER['index'])>0)
{
	foreach($this->PAPER['index'] as $obj => $info)
	{
		$html = ""; 
		$depth=1;
		echo objToHTML($info,$html,$depth,$obj); 
	}
}
else 
{
	$paragraphs = '';
	foreach($this->PAPER['paragraphs'] as $obj => $info)
	{
		$paragraphs .= "<a href=\"/?p={$info['canonicals'][0]}\">{$info['title']}</a><br/>";
	}
	echo $paragraphs;
}