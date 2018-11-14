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

function md($data)
{
	global $JB;
	return $JB->md($data);
}


class Template extends Jotebook
{
	function __construct($jotebook,$canonical)
	{
		parent::__construct($jotebook,$canonical);
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
	
	
	function paperIndex()
	{
		if (!isset($this->PAPER['paragraphs']) || !is_array($this->PAPER['paragraphs']) )
		{
			return "";
		}
		
		$paragraphs = "<li class=\"dropdown\">".
					  "<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">{$this->PAPER['info']['title']}<span class=\"caret\"></span></a>".
					  "<ul class=\"dropdown-menu\">".
					  "<li><a href=\"?p={$this->PAPER['canonicals'][0]}\">Index</a></li>";
			  
		foreach($this->PAPER['paragraphs'] as $obj => $info)
		{
			$paragraphs .= "<li><a href=\"?p={$info['canonicals'][0]}\">{$info['title']}</a></li>";
		}
		echo $paragraphs. "</ul></li>";
	}
	
}

$template = new Template(SELECTED_JOTEBOOK,CURRENT_CANONICAL);
