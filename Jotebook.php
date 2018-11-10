<?php
class Jotebook 
{
	protected
	# ~ Selected Jotebook
	$CURRENT_JOTEBOOK,
	
	# ~ It Contains the decoded  json array of the requested page 
	$PAPER,
		
	# ~ It Contains the requested object 
	$PAPER_OBJ = '',
		
	# ~ Requested for Index?
	$IS_PAPER_INDEX = false,
		
	# ~ Requested for Home?
	$IS_HOME = false,
	
	# ~ Reserved folder names for the notebook folder
	$RESERVED_FOLDER_NAMES =
		[
			"pages" # Reserved folder for wiki-pages
		],
	
	# ~ Contains the index of the Jotebook's papers
	$JOTEBOOK_INDEX,
		
	# ~ Load CSS dinamically from the Template files
	# for future usage
	$TEMPLATE_STYLE = '';
	
	public $PARSEDOWN;
	
	# ~ Jotebook version
	protected $VERSION = "0.0.3-dev";
	
	function __construct($canonical)
	{
		
		# ~ Jotebook Initial Check
		
		if
		  ( 
			!defined("DATA_DIRECTORY") || 
			!file_exists(__DIR__.DIRECTORY_SEPARATOR.'config')  ||
			!file_exists(__DIR__.DIRECTORY_SEPARATOR.'config/papers.json')  ||
			json_decode(file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'config/papers.json'),true) === NULL
		  )
		{
			$this->makeContent(__DIR__.DIRECTORY_SEPARATOR.DATA_DIRECTORY);
			exit("Refresh the page. If you already reloaded it check your Jotebook installation.");
		}

		
		# ~ Load The Papers' Configuration file
		
		$papers_configuration_file = json_decode(file_get_contents(CONFIG_DIRECTORY.'/papers.json'),true);
		
		# ~ Load whitelist canonical in PAPERS_CANONICAL
		
		$this->PAPERS_CANONICAL = $papers_configuration_file['translate'];
		$this->PAPERS_INDEX_CAN = $papers_configuration_file['index'] ?? [];
		$this->JOTEBOOK_CAT_INDEX   = $papers_configuration_file['jotebook_cat_index'] ?? []; 
		
		
		# ~ Operations on the canonical string
		
		$canonical = preg_replace('/(^\/|\/$)/','',$canonical);
		

		# ~ Check for reserved Canonicals
		if ($canonical == "refresh")
		{
			$this->makeContent(__DIR__.DIRECTORY_SEPARATOR.DATA_DIRECTORY);
			exit("Refreshed!");
		}
		else if ($canonical == "home")
		{
			$this->IS_HOME = true;
		}
		else 
		{
			# ~ Check if the paper exists
			if (array_key_exists($canonical,$this->PAPERS_INDEX_CAN))
			{
				$this->IS_PAPER_INDEX = true;
					
				if (!$this->paperIsValid($canonical))
				{
					exit("The paper you are looking for is not a valid one.");
				}
					
			}
			else if (array_key_exists($canonical,$this->PAPERS_CANONICAL))
			{
				if (!$this->paperIsValid($canonical))
				{
					exit("The obj you are looking for is not a valid one.");
				}
				else
				{
					$this->PAPER_OBJ = $this->PAPERS_CANONICAL[$canonical]['obj'];
				}
			}
			else
			{
				exit("The table you are looking for doesn't exist: $canonical");
			}
		
		
	    }

	}
	
	public function selectJotebook($jotebook_name)
	{
		$this->CURRENT_JOTEBOOK = "OHNP";
		return true;
	}
	
	
	# ~ Check if the requested paper is valid and set PAPER if
		# @param string protocols/smtp
		# @return boolean
		
	protected function paperIsValid($paper_canonical)
	{
		if($this->IS_PAPER_INDEX)
		{
			if (!file_exists($this->PAPERS_INDEX_CAN[$paper_canonical]))
			{
				return false;
			}
			
			# ~ Get the configuration json array for the requested page.

			$this->PAPER = json_decode(file_get_contents($this->PAPERS_INDEX_CAN[$paper_canonical]),true);
		}
		else
		{
			if (!file_exists($this->PAPERS_CANONICAL[$paper_canonical]["file"]))
			{
				return false;
			}
			
			# ~ Get the configuration json array for the requested page.

			$this->PAPER = json_decode(file_get_contents($this->PAPERS_CANONICAL[$paper_canonical]["file"]),true);			
		}
		

		
		# ~ Check the json configuration file 

		if ($this->PAPER === NULL)
		{
			return false;
		}
		else
		{
			return true;
		}
		
	}
	
	# ~ Start the show process
	
	public function run()
	{
		$this->show();
	}
	
	# ~ Load the template files and show the HTML code
	
	protected function show()
	{
		# ~ Load the init template file
		
		include 'templates/'.TEMPLATE_NAME.'/template_init.php';
		
		ob_start();
		
		# ~ Choose the template file to load
		
		if ($this->IS_PAPER_INDEX)
		{
			include("templates/".TEMPLATE_NAME."/paper_index.php");
		}
		else if ($this->IS_HOME)
		{
			
			if (file_exists("templates/".TEMPLATE_NAME."/home.php"))
			{
				include("templates/".TEMPLATE_NAME."/home.php");
			}
			else
			{
				echo $this->makeIndex();
			}
	
		}
		else 
		{
			# ~ Get the object type
			
			$obj_type = $this->PAPER['paragraphs'][$this->PAPER_OBJ]["type"];
			
			switch($obj_type)
			{
				case 'table': 		echo $this->table($this->PAPER['paragraphs'][$this->PAPER_OBJ]); break;
				case 'wiki-page': 	echo $this->wiki_page($this->PAPER['paragraphs'][$this->PAPER_OBJ]);break;
				case 'wiki-shot':   echo $this->wiki_shot($this->PAPER['paragraphs'][$this->PAPER_OBJ]);break;
			}

		}
		
		$page_content = ob_get_contents();
		ob_end_clean();
		
		ob_start();
		
		# ~ Set custom style for the page if required
		if ($this->TEMPLATE_STYLE!='')
		{
			$template['style'] = $this->TEMPLATE_STYLE;
		}
		
		# ~ Load the Body Container of the page

		include("templates/".TEMPLATE_NAME."/post.php");	

		
		$html = ob_get_contents();
		ob_end_clean();
		echo $html;
		
	}
	
	# ~ Make the index of the Jotebook
	
	protected function makeIndex()
	{
		
		if (!file_exists('themes/'.TEMPLATE_NAME.'/index.html'))
		{
			$index = '';
		
			foreach($this->JOTEBOOK_CAT_INDEX as $cat => $papers)
			{
				
				$index .= "<h1>$cat</h1><ul>";
				foreach($papers as $title => $canonical)
				{
					$index .= "<li><a href=\"?p=$canonical\">$title</a></li>";
				} 
				$index .= "</ul>";
				
			}
			
			file_put_contents('themes/'.TEMPLATE_NAME.'/index.html',$index);
		
		}
		else
		{
			$index = file_get_contents('themes/'.TEMPLATE_NAME.'/index.html');
		}
		
		return $index;
	}
	
	protected function wiki_shot($shot_info)
	{
		if (file_exists(DATA_DIRECTORY."/shots/{$shot_info['content']}"))
		{
			include DATA_DIRECTORY."/shots/{$shot_info['content']}";
		}
		else
		{
			return "The wiki-shot file you are looking for is missing.";
		}
		
	}
	
	### Features in Papers ###
	
	# ~ Wiki-Page
	
	/*
	* 	"wiki-page-id":
	*	{
	*		"type":			"wiki-page",
	*		"canonicals": 	[],
	*		"title" : 		"",
	*		"template": 	"template-name",
	*		"page_name": 	"page_name"
	*	}
	*
	*/
	protected function wiki_page($page_info)
	{
		# ~ Search for its specific model file
		if (file_exists(DATA_DIRECTORY."/pages/{$page_info['page_name']}"))
		{
			include(DATA_DIRECTORY."/pages/{$page_info['page_name']}");	
		}
		# ~ Search for its generic model template file
		else if (file_exists(DATA_DIRECTORY."/pages/{$page_info['template']}.php"))
		{
			include(DATA_DIRECTORY."/pages/{$page_info['template']}.php");
		}
		else 
		{
			return "You missed the template {$page_info['template']} in your notebook.";
		}
		
	}
	
	# ~ Table
	/*
	*	"table-id":
	*	{
	*		"type":			"table",
	*		"canonicals": 	[],
	*		"title":   		"Table Title",
	*		"class": 		"",
	*		"reference": 	[],
	*		"columns": [{"text":"Column Name",class="class-name"},{"text":"Column Name 2"}],
	*		"rows"   : 
	*				  [
	*				   [{"text":"Cell Text"},{"text":"(link)(link.com)","md":true}],
	*				   [{"text":"Cell Text",class=""},{"text":"(link)(link.com)","md":true}]
	*				  ]
	*	}
	*/
	protected function table($obj,$show_title=true)
	{
		$table = $show_title ? "<h2>{$obj['title']}</h2>" : "";
		$rows  = '';
			
		# Table's Header 
		foreach ($obj['columns'] as $col)
		{
			$class = isset($col['class']) ? "class='{$col['class']}'" : '';
								  
			if (isset($col['md']))
			{
				$table .= "<th $class>".$this->PARSEDOWN->text($col['text']).'</th>';
			}
			else
			{
				$table .= "<th $class>{$col['text']}</th>"; 
			}
		}
		
		$table = "<thead><tr>$table</tr></thead>";
							  
		# Table's Body 
		foreach ($obj['rows'] as $arr => $row)
		{
			$this_row = '';
			foreach ($row as $info)
			{
				
				$class = isset($info['class']) ? "class='{$info['class']}'" : '';
				if (isset($info['md']))
				{
					$this_row .= "<td $class>".$this->PARSEDOWN->text($info['text']).'</td>';  
				}
				else
				{
					$this_row .= "<td $class>".$info['text'].'</td>';  
				}
									
			}
			
			$rows .= "<tr>$this_row</tr>";
								  
		}
		
		$table .= "<tbody>$rows</tbody>";
		
		$id    = isset($obj['id']) ? $obj['id'] : '';
		$class = isset($obj['class']) ? "class='{$obj['class']}'" : '';
		
		$table =  "<table $class id='$id'>$table</table>";
	
		return $table;
	}
	
	# ~ Paper 
	/*
	 * 	Get elements in the paper file
	 */
	public function paper($destination)
	{
		
		$data = $this->PAPER;
		
		# ~ Set the requested element in $data
		$data = &$data;
		foreach($destination as $path)
		{
			$data = $data[$path] ?? '';
		}
		
		if (isset($data['type']))
		{
			$obj_type = $data['type'];
		
			switch($obj_type)
			{
				case 'table': 		return $this->table($data); break;
				case 'wiki-shot':   return $this->wiki_shot($data);break;
			}
		}
		else 
		{
			return $data;
		}

		
		return "";	
		
	}
	
/* 	# ~ Return Paragraph
	
	public function p($destination)
	{
		$data = $this->PAPER['paragraphs'];
		$data = &$data;
		foreach($destination as $path)
		{
			$data = $data[$path] ?? '';
		}
		
		$obj_type = $data['type'];
		
		switch($obj_type)
		{
				case 'table': 		return $this->table($this->PAPER['paragraphs'][$path]); break;
				# case 'wiki-page': 	return $this->wiki_page($this->PAPER['paragraphs'][$path]);break;
		}
		
		return "";	
		
	} */
	
	public function md($text)
	{
		return $this->PARSEDOWN->text($text);
	}
	
	# Exit Function
	
	protected function page404()
	{
		exit("404 - Page Not Found");
	}
	
	
	# ~ Initialatization functions
	
	# ~ Elaborate the DATA_DIRECTORY and makes the papers and suggestions file
	protected function  makeContent($dir, &$results = [])
	{
		# ~ Get all files in the current dir
		
		$files = scandir($dir);

		foreach($files as $key => $value)
		{
			# Check for the realpath
			$path = realpath($dir.DIRECTORY_SEPARATOR.$value);
			
			if(!is_dir($path))
			{
				# Check if is a json file
				if(pathinfo($path, PATHINFO_EXTENSION) == 'json')
				{
					
					# Get the paper's data
					
					$paper = json_decode(file_get_contents($path),true);
					
					# Validate the paper
					if ($paper === NULL)
					{
						exit("Bad Paper File: " . $path);
					}
					
					
					# ~ Check if the json file is not a page
					
					# Paper type for future developments
					
					if (!isset($paper['type']) )
					{
						
						# ~ Set the canonicals for the files
						if (isset($paper['canonicals']) && is_array($paper['canonicals']))
						{
							# Add canonicals for the page to the index database 
							foreach($paper['canonicals'] as $can_index)
							{
								$results['index'][$can_index] = $path;  
							}
							
							
							if(isset($paper['canonicals']) && isset($paper['canonicals'][0]))
							{
								$can_index = $paper['canonicals'][0];
								# ~ Set the paper into the Jotebook Index
								if (!isset($paper['info']['category']))
								{
									if (!isset($results['jotebook_cat_index']) || !isset($results['jotebook_cat_index']['Uncategorized']))
									{
										$results['jotebook_cat_index']['Uncategorized'] = [];
									}
									
									$results['jotebook_cat_index']['Uncategorized'] += [$paper['info']['title'] => $can_index];
								}
								else
								{
									if (!isset($results['jotebook_cat_index']) || !isset($results['jotebook_cat_index'][$paper['info']['category']]))
									{
										$results['jotebook_cat_index'][$paper['info']['category']] = [];
									}
									
									$results['jotebook_cat_index'][$paper['info']['category']] += [ $paper['info']['title'] => $can_index];
								}
							}
							
						}
					
						# ~ Set the canonicals for the objects in files
						foreach($paper['paragraphs'] as $paragraph_name => $paragraph_info)
						{
							if (isset($paragraph_info['canonicals']))
							{
								foreach($paragraph_info['canonicals'] as $can_par)
								{
									$results['translate'][$can_par] = ["file"=>$path,"obj"=>$paragraph_name];
								}
							}
						}
					}
				}
			}
			else if($value != "." && $value != ".." && !in_array($value,$this->RESERVED_FOLDER_NAMES))
			{
				$this->makeContent($path, $results);
			}
		}
			
		# ~ Make Suggestions for template's API
		$suggestions = array_merge(array_keys($results['translate']),array_keys($results['index']));
		sort($suggestions);
		
		file_put_contents(__DIR__.'/themes/'.TEMPLATE_NAME.'/suggestions.js',"var suggestions = " . json_encode($suggestions) . ";");
		file_put_contents(__DIR__.DIRECTORY_SEPARATOR.'config/papers.json',json_encode($results));
	}
	
	# ~ Initialize the jotebook content
	
	public function init()
	{
		$this->getDirContents(__DIR__.DIRECTORY_SEPARATOR.DATA_DIRECTORY);
	}
	
}