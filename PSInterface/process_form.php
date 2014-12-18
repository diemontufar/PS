<?php
/**
 * Author: Diego Montufar
 * Date: 05/12/2014
 */

session_start(); 
set_error_handler("showError");
date_default_timezone_set('Australia/Victoria');

// define form variables and set to empty values
$name = $helix = $chain = $first = $last = $atom_type = $model = $ref_type = $conformer = $radius = $seed = $scan = $pdb_file = $answer = $pdbId = "";


//define variables for executing command and creating directories
$command = "./PS ";
$output_file_name = "";
$parent_directory = "files";
$input = "/input/";
$output = "/output/";
$current_input_dir = "";
$current_output_dir = "";
$_SESSION['processed'] = "false";


/** Get values from the user input **/
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
  $name = test_input($_POST["name"]);
  $helix = test_input($_POST["helix"]);
  $chain = test_input($_POST["chain"]);
  $first = test_input($_POST["first"]);
  $last = test_input($_POST["last"]);
  $atom_type = test_input($_POST["atom_type"]);
  $model = test_input($_POST["model"]);
  $ref_type = test_input($_POST["ref_type"]);
  $conformer = test_input($_POST["conformer"]);
  $radius = test_input($_POST["radius"]);
  $seed = test_input($_POST["seed"]);
  $scan = test_input($_POST["scan"]);
  $pdbId = test_input($_POST["pdb_id"]);
  $process = false;
  $answer = test_input($_POST['process']);  
	if ($answer == "manual") {          
	    $process = true;     
	}
	else if($answer == "auto"){
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
	verifyDirectories();
	//Upload pdb file to input directory
	downloadFileToServer();
	//Construct command line
	$command = $command.constructCommand();
	//Run PS program on the server
	executePS($command);
	//$_SESSION['response'] = "The file was successfully uploaded with command: ".$command;
	$_SESSION['response'] = "The file was successfully processed, please check PS Analysis on the Results tab. ";
	$_SESSION['image'] = "success"; 
	$_SESSION['processed'] = "true";
}else{
	$_SESSION['response'] = "File not found on RCSB Protein Data Bank ";
	$_SESSION['image'] = "error";
}
header("Location: index.php");

/** Download result from REST PDB Web Service: **/
function getWebServiceResponse($pdb_id){

	$curl = curl_init();

	$url = 'http://www.rcsb.org/pdb/rest/describePDB?structureId=';
	
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
	
	global $process, $current_input_dir, $pdb_file, $current_output_dir, $name;
	
	if ($process == true){
		exec($comm,$output);
	}else{
		$comm2 = "./automated-ps ".$current_input_dir. $pdb_file." ".$current_output_dir.$name.".out";	
		exec($comm2,$output);
	}
	return $output;

}

/** This method uploads the file from the user input to a directory on the server **/
function downloadFileToServer(){

	global $current_input_dir,$pdb_file,$pdbId;
	
	$pdb_file = $pdbId.'.pdb';
	$target_file = $current_input_dir.$pdb_file;

	$file_url = 'http://www.rcsb.org/pdb/download/downloadFile.do?fileFormat=pdb&compression=NO&structureId='.$pdbId;

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
	$name = $name.'_'.date('H-i');
	
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
function verifyDirectories(){

	global $parent_directory, $input, $output, $current_output_dir,$current_input_dir;
	
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

?>
