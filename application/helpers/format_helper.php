<?php
	function output_to_json($current, $result){
		$current->output->set_content_type('application/json');
		$current->output->set_output(json_encode($result));		
	}
	
	function generate_random_keys($length) 
	{ 
		$random= "";
		srand((double)microtime()*1000000);

		$data = "AbcDE123IJKLMN67QRSTUVWXYZ"; 
		$data .= "aBCdefghijklmn123opq45rs67tuv89wxyz"; 
		$data .= "0FGH45OP89";

		for($i = 0; $i < $length; $i++) 
		{ 
			$random .= substr($data, (rand()%(strlen($data))), 1); 
		}

		return $random; 
	}
	
	function read_default_styles($filename)
	{
		$filestring = file_get_contents($filename);
		$filearray = explode("\n", $filestring);

		return $filearray;
	}
?>