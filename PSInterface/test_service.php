<?php

// a simple function that logs user-aborted scripts
//$pid = "";
/*
function catch_user_abort() {

   global $pid;

   if (connection_aborted()){
	mkdir("files/output/output/".$pid, 0777, true);
   }

   if (connection_aborted() && $pid!=""){
      killProcess($pid);
   }
}
*/


		$directory = "files/output/2015-01-28/fer_20-26-05";
		//$scanned_directory = array_diff(scandir($directory), array('..', '.'));
		$scanned_directory = scandir($directory);
		echo $scanned_directory;
		
 	        for ($i = 0; $i < count($scanned_directory); ++$i) {
 	          $ext = pathinfo($scanned_directory[$i]);
 	          if ($ext['extension'] == "out"){
				  $name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $scanned_directory[$i]);
				  $scanned_directory[$i] = trim($name);
			  }else{
			  	  $scanned_directory[$i] = "";
			  }
		}
		
		$scanned_directory[1] = '-';

		for ($i = 0; $i < count($scanned_directory); ++$i) {
			if (!empty(trim($scanned_directory[$i]))){    
				echo "<div>";
				echo $scanned_directory[$i];
				echo "</div>";
			}
		}


/*
	$curl = curl_init();
        $pdb_id = '3I5F';
	$url = 'http://www.rcsb.org/pdb/rest/describePDB?structureId=';

	$url = $url.$pdb_id;

	$client = curl_init($url);
	curl_setopt($client, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($client);
	curl_close($client);

	$xml = simplexml_load_string($response);

	foreach ($xml->PDB as $pdb_details) {
	 //echo "$pdb_details['structureId'], $pdb_details['title'], $pdb_details['pubmedId'], $pdb_details['resolution'] <br/>\n";
	echo $pdb_details['structureId'].','.$pdb_details['title'];	
	}
*/
    //echo $result;


//$output = shell_exec('./automated-ps files/input/2015-01-15/3I5F.pdb files/output/2015-01-15/test_xx_16-34/');
//echo "<pre>$output</pre>";


/*
$array1 = $array2 = array("80.out", ".", "1.out", "5.out", "10.out", "3.out", "100.out", "..");

natsort($array2);
echo "\nNatural order sorting\n";
print_r($array2);
*/

	//global $current_output_dir;

	//$output =  shell_exec("./automated-ps files/input/2015-01-23/3LOH.pdb files/output/output/");
	
	//echo PsExec('./automated-ps files/input/2015-01-23/3I5F.pdb files/output/output/'.' > /dev/null 2>&1 & echo $!');

/*
	if ($process){
		//Normal Process
		exec($comm,$output);
	}else{
		//Automatic Process
		$auto_command = $auto_command.$current_input_dir. $pdb_file." ".$current_output_dir;	
		$output = shell_exec($auto_command);
	}
*/
	/*
function killProcess($id){
	exec("kill -9 ".$id);
}

function PsExec($commandJob) { 

    global $pid;

    $command = $commandJob; 
    exec($command ,$op); 
    $pid = (int)$op[0]; 
    
    if($pid!="") return $pid; 

    return false; 
}
*/
/** Download result from REST PDB Web Service: **/
/*
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
} */



//foreach ($xml->PDB as $pdb_details) {
	 //echo "$pdb_details['structureId'], $pdb_details['title'], $pdb_details['pubmedId'], $pdb_details['resolution'] <br/>\n";
	//echo $pdb_details['structureId'].','.$pdb_details['title'];

	//if (!empty($pdb_details['structureId'])){
	//	$file = 'http://www.rcsb.org/pdb/download/downloadFile.do?fileFormat=pdb&compression=NO&structureId=';

		
	//}
// register a handler function to be called at script end
//register_shutdown_function ('catch_user_abort');
?>
