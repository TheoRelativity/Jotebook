<?php
class Jotebook 
{
	protected
	# ~ It Contains the decoded  json array of the requested page 
	$PAPER,
		
	# ~ It Contains the requested object 
	$PAPER_OBJ = '',
		
	# ~ Requested for Index?
	$IS_PAPER_INDEX = false,
		
	# ~ Requested for Home?
	$IS_HOME = false,
		
	# ~ Load CSS dinamically from the Template files
	$TEMPLATE_STYLE = '';
	
	public $PARSEDOWN;
	
	static $version = "0.0.0-dev";
	
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
			include("templates/".TEMPLATE_NAME."/home.php");
		}
		else 
		{
			# ~ Get the object type
			
			$obj_type = $this->PAPER['paragraphs'][$this->PAPER_OBJ]["type"];
			
			switch($obj_type)
			{
				case 'table': echo $this->table($this->PAPER['paragraphs'][$this->PAPER_OBJ]);
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

	protected function table($obj)
	{
		$table = "<h2>{$obj['title']}</h2>";
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
	
	public function t($destination,$HTML)
	{
		$data = $this->PAPER['paragraphs'];
		$data = &$data;
		foreach($destination as $path)
		{
			$data = $data[$path] ?? '';
		}
		
		return $HTML ? $data : (is_array($data) ? $data : htmlspecialchars($data));	
		
	}
	
	public function md($destination)
	{
		
		$data = $this->PAPER;
		$data = &$data;
		foreach($destination as $path)
		{
			$data = $data[$path] ?? '';
		}
		
		return $this->PARSEDOWN->text($data);
	}
	
	# Exit Function
	
	protected function page404()
	{
		exit("404 - Page Not Found");
	}
	
	# ~ Elaborate the DATA_DIRECTORY and makes the papers and suggestions file
	
	protected function  makeContent($dir, &$results = [])
	{
		$files = scandir($dir);

		foreach($files as $key => $value)
		{
			$path = realpath($dir.DIRECTORY_SEPARATOR.$value);
			
			if(!is_dir($path))
			{
				
				if(pathinfo($path, PATHINFO_EXTENSION) == 'json')
				{
				
					$paper = json_decode(file_get_contents($path),true);
					
					if ($paper === NULL)
					{
						exit("Bad Configuration File: " . $path);
					}
					
					if (isset($paper['canonicals']))
					{
						foreach($paper['canonicals'] as $can_index)
						{
							$results['index'][$can_index] = $path;  
						}
					}
					
					
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
			else if($value != "." && $value != ".." && $value != "external")
			{
				$this->makeContent($path, $results);
			}
		}
		/*
		 * Save Suggestions into the Template folder
		*/
		
		$suggestions = array_merge(array_keys($results['translate']),array_keys($results['index']));
		sort($suggestions);
		file_put_contents(__DIR__.'/themes/'.TEMPLATE_NAME.'/suggestions.js',"var suggestions = " . json_encode($suggestions) . ";");
		file_put_contents(__DIR__.DIRECTORY_SEPARATOR.'config/papers.json',json_encode($results));
	}
	
	public function init()
	{
		$this->getDirContents(__DIR__.DIRECTORY_SEPARATOR.DATA_DIRECTORY);
	}
	
}