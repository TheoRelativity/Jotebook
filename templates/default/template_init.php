<?php
# ~ Define the default title page
define("TPL_TITLE","Default Jotebook Template");
# ~ Define the default title page's suffix
define("TPL_TITLE_SUFFIX","- Jotebook");

# ~ Elaborates the index of the Paper
function objToHTML(&$array,&$html='',&$depth=1,$obj_name="")
{

	if($array['type']=="section")
	{
	$html .= "<h{$depth}>".str_repeat("",$depth)."{$array['name']}</h{$depth}>";
		$depth++;
		foreach($array["paragraphs"] as $obj => $info)
			$html = objToHTML($info,$html,$depth,$obj_name);
		$depth = 1;
	}
	else if ($array['type']=="ref")
	{
		$html .=  "<p>".str_repeat("&nbsp;",$depth)." <a href=\"?p={$array['href']}\">{$array['name']}</a></p>";
	}
	else if ($array['type']=="ext-ref")
	{
		$html .=  "<p>".str_repeat("&nbsp;",$depth)." <a href=\"{$array['href']}\">{$array['name']}</a></p>";
	}
	
	return $html;
}

# Helper functions for the template

# ~ Return paper
function paper($data)
{
	global $JB;
	return $JB->paper(explode("/",$data));
}

/* # ~ Return paragraph
function p($data)
{
	global $JB;
	return $JB->p(explode("/",$data));
} */

function md($data)
{
	global $JB;
	return $JB->md($data);
}


/*
 * Set the configuration variable for the 
 * upper page of the template
 * @param array $data
 * example: $data = ['title'=>'This is a title']
*/
/* function page($data)
{
	
	foreach($data as $var => $val)
	{
		$template[$var] = $val;
	}
    
	return $template;
} */

class Template extends Jotebook 
{
	
	function __construct()
	{
		$this->selectTheme("default");
	}
	
	function this_folder($file)
	{
		/* 
			Realpath code here
			to protect the code 
		*/
		return TEMPLATES_DIR.$this->getTheme()."/$file";
	}
	
	function index()
	{
		return $this->makeIndex();
	}
	
}

$template = new Template();
