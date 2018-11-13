<h1><?= $this->PAPER['info']['title']?> </h1>
<?php foreach($this->PAPER['index'] as $obj => $info)
{

	$html = ""; 
	$depth=1;
	echo objToHTML($info,$html,$depth,$obj); 
}