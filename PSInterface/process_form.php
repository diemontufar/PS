<?php
/**
 * Author: Diego Montufar
 * Date: 05/12/2014
 */

session_start(); 
set_error_handler("showError");

// Parse without sections
$ini_parameters = parse_ini_file("config/web-config.ini");
//print_r($ini_parameters);

date_default_timezone_set($ini_parameters['timezone']);

// define form variables and set to empty values
$name = $helix = $chain = $first = $last = $atom_type = $model = $ref_type = $conformer = $radius = $seed = $scan = $pdb_file = $answer = $pdbId = "";


//define variables for executing command and creating directories
$command = $ini_parameters['ps_location']." ";
$auto_command = $ini_parameters['automated_location']." ";
$output_file_name = "";
$parent_directory = "files";
$input = "/input/";
$output = "/output/";
$current_input_dir = "";
$current_output_dir = "";
$_SESSION['processed'] = "false";
$process = false;


/** Get values from the user input **/
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
  global $process;

  $name = (isset($_POST["name"]) ? test_input($_POST["name"]) : '');
  $helix = (isset($_POST["helix"]) ? test_input($_POST["helix"]) : '');
  $chain = (isset($_POST["chain"]) ? test_input($_POST["chain"]) : '');
  $first = (isset($_POST["first"]) ? test_input($_POST["first"]) : '');
  $last = (isset($_POST["last"]) ? test_input($_POST["last"]) : '');
  $atom_type = (isset($_POST["atom_type"]) ? test_input($_POST["atom_type"]) : '');
  $model = (isset($_POST["model"]) ? test_input($_POST["model"]) : '');
  $ref_type = (isset($_POST["ref_type"]) ? test_input($_POST["ref_type"]) : '');
  $conformer = (isset($_POST["conformer"]) ? test_input($_POST["conformer"]) : '');
  $radius = (isset($_POST["radius"]) ? test_input($_POST["radius"]) : '');
  $seed = (isset($_POST["seed"]) ? test_input($_POST["seed"]) : '');
  $scan = (isset($_POST["scan"]) ? test_input($_POST["scan"]) : '');
  $pdbId = (isset($_POST["pdb_id"]) ? test_input($_POST["pdb_id"]) : '');


  /*Check if there is some required empty field */ 
  if (empty($name) || empty($pdbId)){
  	$_SESSION['response'] = $ini_parameters['error_fields'];
	$_SESSION['image'] = "error";
	$_SESSION['error'] = "true";
	header("Location: index.php");
  	die();
  }

  /*Check if there is Internet Connection */ 
  if (!isConnected('http://www.rcsb.org')){
  	$_SESSION['response'] = $ini_parameters['error_connection'];
	$_SESSION['image'] = "error";
	$_SESSION['error'] = "true";
	header("Location: index.php");
  	die();
  }
  
  if (test_input($_POST['selection'])=="manual"){
	$process = true;
  }else{
	$process = false;
  }

}
/**Here are the main method calls **/

//Connect with PDB Web Server
$resp = getWebServiceResponse($pdbId);

if(!empty($resp)){

	$sId = $tit = "";	

	foreach ($resp->PDB as $pdb_details) {
	   $sId = $sId.$pdb_details['structureId'];
	   $tit = $tit.$pdb_details['title'];
	}

	$_SESSION['pdbId'] = $sId;
	$_SESSION['description'] = $tit;

	//Create Directories to store files
	verifyDirectories($name);
	//Upload pdb file to input directory
	downloadFileToServer();
	//Construct command line
	$command = $command.constructCommand();
	//Run PS program on the server
	$r = executePS($command);
        
	//Generate compressed file
	
	$files = array_diff(scandir($current_output_dir), array('..', '.'));
	
	if (count($files) > 1){
		unset($files[0],$files[1]);
		//var_dump($files);
		sort($files);
		$result = create_zip($current_output_dir,$files,$current_output_dir.changeFileName($name).'.zip');

		if ($result){
			$_SESSION['isZip']=changeFileName($name).'.zip'; //Zip file name as well
			$_SESSION['output-zip-name']=$current_output_dir.changeFileName($name).'.zip'; //path to the zip file
		}
	}
	readStatistics();
	//Show response to the client
	$_SESSION['response'] = $ini_parameters['success_msg'];
	$_SESSION['image'] = "success"; 
	$_SESSION['processed'] = "true";
}else{
	//Show error to the client	
	$_SESSION['response'] = $ini_parameters['error_msg'];
	$_SESSION['image'] = "error";
	$_SESSION['error'] = "true";
}

header("Location: index.php");

/** Check web service connection **/
function isConnected($url){

	if(!filter_var($url,FILTER_VALIDATE_URL)){
		return false;
	}

	$curlInit = curl_init($url);
	curl_setopt($curlInit, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($curlInit, CURLOPT_HEADER, true);
	curl_setopt($curlInit, CURLOPT_NOBODY, true);
	curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($curlInit);

	curl_close($curlInit);

	if ($response) return true;

	return false;
}

/**Read final statistics**/
function readStatistics(){

	global $current_output_dir,$process,$ini_parameters;

	if ($process){
		return;
	}

	$file_name = $ini_parameters['stats_file'];
	$file_name = $current_output_dir.$file_name;	
	$i = 0;
	$stats = array("", "", "", "");

	$handle = @fopen($file_name, "r");
	if ($handle) {
	    while (($buffer = fgets($handle, 4096)) !== false) {
		$stats[$i] = $buffer;
		$i++;
	    }
	    if (!feof($handle)) {
		echo "Reading statistics file\n";
	    }
	    fclose($handle);
	}
        
	if (!empty($stats)){

		if ($stats[0] > 60){
			$_SESSION['time'] = round(($stats[0]/60),2).' min';
		}else{
			$_SESSION['time'] = round($stats[0],2).' sec';
		}
		$_SESSION['structures'] = $stats[1];
		$_SESSION['successfuly'] = $stats[2];
		$_SESSION['errors'] = $stats[3];
	}
}


/** Download result from REST PDB Web Service: **/
function getWebServiceResponse($pdb_id){

	global $ini_parameters;

	$curl = curl_init();

	$url = $ini_parameters['PDB_description_URL'];
	
	$url = $url.$pdb_id;

	$client = curl_init($url);
	curl_setopt($client, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($client);
	curl_close($client);

	$xml = simplexml_load_string($response);

	return $xml;
} 

/** Executes the PS program on the server, $comm is the comand line constructed with parameters for the PS program **/
function executePS($comm){
	
	global $process, $current_input_dir, $pdb_file, $current_output_dir, $name, $auto_command;
	$output="";	

	if ($process){
		//Normal Process
		exec($comm,$output);
	}else{
		//Automatic Process
		$auto_command = $auto_command.$current_input_dir. $pdb_file." ".$current_output_dir;	
		$output = shell_exec($auto_command);
	}
	return $output;
}

/** This method uploads the file from the user input to a directory on the server **/
function downloadFileToServer(){

	global $current_input_dir,$pdb_file,$pdbId,$ini_parameters;
	
	$pdb_file = $pdbId.'.pdb';
	$target_file = $current_input_dir.$pdb_file;

	$file_url = $ini_parameters['PDB_file_URL'].$pdbId;

	$ch = curl_init($file_url);
	$fp = fopen($target_file, 'wb');
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_exec($ch);
	curl_close($ch);
	fclose($fp);
}

/** Constructs the command line to be executed by the server with all the parameters for the PS program
*   if some non-required parameter is empty will be ignored
**/
function constructCommand(){

	global $name , $helix , $chain , $first , $last , $atom_type , $model , $ref_type , $conformer , $radius , $seed , $scan , $pdb_file;
	global $current_input_dir,$current_output_dir;

	$cmd = "";
	$cmd = $cmd . "-i " . $current_input_dir. $pdb_file . " ";

	if (!empty($helix)){
		$cmd = $cmd . "-H " . $helix . " ";
	}
	
	if (!empty($chain)){
		$cmd = $cmd . "-c " . $chain. " ";
	}
	
	if (!empty($first)){
		$cmd = $cmd . "-f " . $first. " ";
	}
	
	if (!empty($last)){
		$cmd = $cmd . "-l " . $last. " ";
	}
	
	if (!empty($atom_type)){
		$cmd = $cmd . "-a " . $atom_type. " ";
	}
	
	if (!empty($model)){
		$cmd = $cmd . "-m " . $model. " ";
	}
	
	if (!empty($ref_type)){
		$cmd = $cmd . "-r " . $ref_type. " ";
	}
	
	if (!empty($conformer)){
		$cmd = $cmd . "-k " . $conformer. " ";
	}
	
	if (!empty($radius)){
		$cmd = $cmd . "-R " . $radius. " ";
	}
	
	if (!empty($seed)){
		$cmd = $cmd . "-s " . $seed. " ";
	}
	
	if (!empty($scan) && $scan != "-") {
		$cmd = $cmd . "-S " . strtolower($scan). " ";
	}
	
	$name = changeFileName($name);
	
	$_SESSION['output'] = $current_output_dir . $name . ".out";
	$_SESSION['output-file-name'] = $name . ".out";
	$cmd = $cmd . "-o " . $current_output_dir . $name . ".out";
	
	
	return $cmd;
}

/** This method changes the variable "name" with underscores in order to use it for the output file name **/
function changeFileName($string){

	$string = strtolower($string);
	$string = preg_replace("/[^a-z0-9_\s-]/","",$string);
	$string = preg_replace("/[\s-]+/"," ",$string);
	$string = preg_replace("/[\s]/","_",$string);
	
	return $string;

}

/** Creates file directory structure for storing input and output files on the server
*   each directory is named by the current date
**/
function verifyDirectories($job_name){

	global $parent_directory, $input, $output, $current_output_dir,$current_input_dir,$pdbId;
	
	//Create directory files if it does not exist:
	if (!file_exists($parent_directory)) {
		mkdir($parent_directory, 0777, true);
	}

	//Create subdirectories if the does not exist
	$input_dir = $parent_directory.$input;
	$output_dir = $parent_directory.$output;
	
	if (!file_exists($input_dir)) {
		mkdir($input_dir, 0777, true);
	}
	
	if (!file_exists($output_dir)) {
		mkdir($output_dir, 0777, true);
	}
	
	//Create current directory for storing input and output files:
	$today = date("Y-m-d"); //get the current date
	$current_input_dir = $input_dir . $today . "/";
	$current_output_dir = $output_dir . $today . "/";
	
	if (!file_exists($current_input_dir)) {
		mkdir($current_input_dir, 0777, true);
	}
	
	$job_name = changeFileName($job_name);
	$time = date("H-i-s"); //get the current time
	$current_output_dir = $current_output_dir.$job_name."_".$time."/";
	$_SESSION['path-to-output'] = $current_output_dir;

	$_SESSION['path-to-input'] = $current_input_dir."/".$pdbId.'.pdb';

	if (!file_exists($current_output_dir)) {
		mkdir($current_output_dir, 0777, true);
	}
}

/** Clean user variables **/
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

/** error handling **/
function showError($errno,$errstr){
	echo "<b>Error:</b>[$errno] $errstr<br>";
	echo "Ending Script";
	die();
}

/**Message Handling **/
function showMessage($msg){
	echo "<b>$msg</b><br>";
}

/* creates a compressed zip file */
function create_zip($path='',$files = array(),$destination = '',$overwrite = false) {
	
	global $ini_parameters;

	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite) { return false; }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($path.$file)) {
				if ($file != $ini_parameters['stats_file']){
					$valid_files[] = $path.$file;
				}
			}
		}
	}
	//if we have good files...

	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		//add the files
		foreach($valid_files as $file) {
			$new_filename = substr($file,strrpos($file,'/') + 1);
			$zip->addFile($file,$new_filename);
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
		//close the zip -- done!
		$zip->close();
		
		//check to make sure the file exists
		return file_exists($destination);
	}
	else
	{
		return false;
	}
}

?>
