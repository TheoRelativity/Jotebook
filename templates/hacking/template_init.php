<?php
# ~ Define the default title page
define("TPL_TITLE","Hacking Notebook");
# ~ Define the default title page's suffix
define("TPL_TITLE_SUFFIX",":: Hacking Notebook");

# ~ Elaborates the index of the Paper
function objToHTML(&$array,&$html='',&$depth=1,$obj_name="")
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
}



/*
 * Print element in json array
 * @param string $data 
 *   example: To read the [title] data $data = 'title'
 *   example: To read a nested value $data = 'tables/table_name/html'
 * @param boolean $HTML
 * if the content contains HTML code use true
 */
function t($data,$HTML=false)
{
	global $OHNP;
	return $OHNP->t(explode("/",$data),$HTML);
}

function html($data)
{
	global $OHNP;
	return $OHNP->md(explode("/",$data));
}


/*
 * Set the configuration variable for the 
 * upper page of the template
 * @param array $data
 * example: $data = ['title'=>'This is a title']
*/
function page($data)
{
	
	foreach($data as $var => $val)
	{
		$template[$var] = $val;
	}
    
	return $template;
}