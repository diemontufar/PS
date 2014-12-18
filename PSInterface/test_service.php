<?php
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

    //echo $result;


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



//foreach ($xml->PDB as $pdb_details) {
	 //echo "$pdb_details['structureId'], $pdb_details['title'], $pdb_details['pubmedId'], $pdb_details['resolution'] <br/>\n";
	//echo $pdb_details['structureId'].','.$pdb_details['title'];

	//if (!empty($pdb_details['structureId'])){
	//	$file = 'http://www.rcsb.org/pdb/download/downloadFile.do?fileFormat=pdb&compression=NO&structureId=';

		
	//}
?>
