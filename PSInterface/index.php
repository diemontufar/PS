<?php
session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <!--<link href="http://www.latrobe.edu.au/_media/la-trobe-api/v5/v5/default/core.css" rel="stylesheet" media="screen" />-->
    <!--<link href="http://www.latrobe.edu.au/_media/la-trobe-api/v5/v5/default/styles.css" rel="stylesheet" media="screen" />
    <link href="http://www.latrobe.edu.au/_media/la-trobe-api/v5/v5/default/print.css" rel="stylesheet" media="print" />
    <link href="http://www.latrobe.edu.au/__data/assets/css_file/0006/564144/jquery-ui-1.10.3.css" rel="stylesheet" media="all" />  -->

    <!-- CSS Styles: 
    <link rel="stylesheet" href="css/styles.css" media="screen" />
    <link rel="stylesheet" href="css/print.css" media="print" />-->
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="css/jquery-ui-1.10.3.css"  media="all" />
    <link rel="stylesheet" href="css/ps-styles.css" media="screen" type="text/css"/> 

    <!-- Javascript -->
    <script type="text/javascript" src="js/jquery.min.js"></script> <!-- Verison: 1.9.1 -->
    <script type="text/javascript" src="js/ps.js"></script>


</head>

<body>

<div id="loader" class="loader" style="display: none">
	<div>
		<img id="loading" src="images/page-loader.gif" alt="Loading..." />
	</div>
	<div class="loader-texts">
	  This could take a few minutes, please wait...
	</div>
</div>

<header>
	<div class="toggle-button">
		<a onclick="activateSide();">
			<img class="sidebar" id="more" src="images/more.png" />
		</a>
		<a onclick="deactivateSide();">
			<img class="sidebar" id="less" src="images/less.png" />
		</a>
	</div>
	<div class="main-title">
		<h1>Helix Geometry Analysis</h1>
	</div>
</header>

<nav id="side-bar">
<div class="nav-container" id="nav-container">
	<h1>
	  PS (Pandus Semita) 
	  </h1>
	  <p> 
	  <h2>A program for the analysis of helix geometry</h2>
	  </p>
	<h3>Description</h3>
	<p>
	The structure of helices within proteins is often distorted from the ideal linear topology. Curvature of the helix axis can be measured by determining the radius of a circle fit to the axis. Described here is a method of defining a curved path that places backbone atoms (usually C&#x3B1;) equidistantly from the path. The variance in the distance of backbone atoms from the helix axis is minimised to produce the parametric equations that describe the intersection of a sphere and a plane. The geometric properties of the helix (including helix radius, radius of curvature, and pitch) can be readily obtained from these equations. The approach is applicable to any form of helix, can use any atom in the peptide to determine the axis, can be applied to any polypeptide including mixed &#x3B1;/&#x3B2; peptides, and does not rely on a regular spacing of peptide monomers in the polypeptide chain.
	</p>
	<p></p>

	<img src="images/Helix.png" alt="Helix" style="width:90%;height:90%;">
	<p>
	PS determines the parameters that define the great circle arc that minimises the variance in distance from this arc to a collection of points that defines each monomer in the helical chain. 
	Properties of helices include helix radius, pitch and radius of curvature. This approach can be applied to any helical structure of proteins, DNA or carbohydrates.</p>

	<p>
	Supporting information may be found in the online version of this <a target="_blank" href="http://www.sciencedirect.com/science/article/pii/S109332631100163X">Article</a>, containing an analysis of helices in DNA, left- and right-handed helices, heparin and collagen, 
	five PDB format files for the ideal 3<sub>10</sub>, alpha, pi, polyproline-I and polyproline-II helices, an example output, instructions for use of the program.

	</p>
	<p></p>
</div>
</nav>


<section class="blockTabs" id="section-ps">

<?php 
	if(!empty($_SESSION["response"])) {
	  if (!empty($_SESSION["image"])){	
		  
		  if ($_SESSION["image"]=="success"){
		  echo '<div class="alert alert-success" id="msgSuccess">';
		  echo '<a class="close" data-dismiss="alert" onclick="closeSuccess();">x</a>';
		  echo '<img src="images/'.$_SESSION["image"].'.png" />&nbsp;';
		  echo '<strong>Success!&nbsp;</strong>';  
		  }else{
		    echo '<div class="alert alert-error" id="msgError">';
		    echo '<a class="close" data-dismiss="alert" onclick="closeError();">x</a>';
		    echo '<img src="images/'.$_SESSION["image"].'.png" />&nbsp;';
		    echo '<strong>Error!&nbsp;</strong>'; 
		  }
	  }	
	echo $_SESSION['response'];
	echo '</div>';
	}
	unset($_SESSION["response"]); 
	unset($_SESSION["image"]);
?>      

<ul class="tabs">
    <li class="active"><a href="#tab1">Process</a></li>
    <div id="tab-showFileContent">
    <li><a href="#tab2" >Results</a></li>
    </div>
</ul>

<article id="tab1">
<h2 class="tab-title">PDB Helix Analysis</h2>

<div class="instructions" >
Please fill out all the required fields (*). Choose between "Manual Settings" whether you want to analyse a particular Helix structure or "Automatic" if you want to analyse all the Helix structures within a PDB file. 
</div>

<form class="PSForm" id="PSForm" method="post" enctype="multipart/form-data" action="process_form.php" onsubmit="return showLoadingMessage(true);">
          
	  <div id="field2-container" class="field f_100">
			<div class="align-left">
				<label for="field2">Name<sup>*</sup>:</label>
			</div>
			<div class="align-right">
				<input name="name" id="field2" required="required" type="text" placeholder="e.g. Alpha Helix Structure" title="Please don't leave this field empty">
				<span class="fieldtip">Enter a name for your job</span>
			</div>
	  </div>

	  <div id="field20-container" class="field f_100">
			<div class="align-left">
				<label for="field2">Process with:</label>
			</div>
			    <div class="radio-toolbar radio-positioning">  
				<input id="manual" type="radio" name="process" value="manual" onClick="enable(true)">  
				<label for="manual">Manual Settings</label>  
				<input id="auto" checked="true" type="radio" name="process" value="auto" onClick="enable(false)">  
				<label for="auto">&nbsp;&nbsp;Automatic&nbsp;&nbsp;</label>  
				<input type="hidden" name="selection" id="selection" />
			    </div>  
	  </div>

	   <div id="field3-container" class="field f_100">
			<div class="align-left">
				<label for="field3">Helix:</label>
			</div>
			<div class="align-right">
				<input name="helix" max="999" min="1" id="field3" type="number">
				<span class="fieldtip">Index of helix in pdb file</span>
			</div>
	  </div>

	  <div id="field4-container" class="field f_100">
			<div class="align-left">
				<label for="field4">Chain:</label>
			</div>
			<div class="align-right">
				<input name="chain" id="field4" type="text" placeholder="e.g. A">
				<span class="fieldtip">Chain identifier</span>
			</div>
	  </div>
         
      <div id="field16-container" class="field f_100">
			<div class="align-left">
				<label for="field16">First Residue:</label>
			</div>
			<div class="align-right">
				<input max="999" min="1" name="first" id="field16" type="number">
				<span class="fieldtip">Number of first residue</span>
			</div>
	  </div>   
          
      <div id="field17-container" class="field f_100">
			<div class="align-left">
				<label for="field17">Last Residue:</label>
			</div>
			<div class="align-right">
				<input max="999" min="1" name="last" id="field17" type="number">
				<span class="fieldtip">Number of last residue</span>
			</div>
	  </div>      
      
	   <div id="field7-container" class="field f_100">
			<div class="align-left">
				<label for="field7">Atom Type:</label>
			</div>
			<div class="align-right big-fieldtip">
				<input pattern="[0-9a-zA-Z]+(,[0-9a-zA-Z]+){0,4}" name="atom_type" id="field7" type="text" placeholder="e.g. CA" title="Up to 5 comma separated list of values">
				<span class="fieldtip input-big-tooltip">Atom types used to define the helix</span>
			</div>
	  </div>  
      
	  <div id="field18-container" class="field f_100">
			<div class="align-left">
				<label for="field18">Model:</label>
			</div>
			<div class="align-right big-fieldtip">
				<input max="999" min="1" name="model" id="field18" type="number">
				<span class="fieldtip input-big-tooltip">Model number in a NMR PDB file</span>
			</div>
	  </div>  
	  
	  <div id="field40-container" class="field f_100">
			<div class="align-left">
				<label for="field8">Reference Type:</label>
			</div>
			<div class="align-right big-fieldtip">
				<input pattern="[0-9a-zA-Z]+(,[0-9a-zA-Z]+){0,4}" name="ref_type" id="field8" type="text" placeholder="e.g. C,N,O" title="Up to 5 comma separated list of values">
				<span class="fieldtip input-big-tooltip">Atom types to measure distance form helix axis</span>
			</div>
	  </div>  
          
      <div id="field9-container" class="field f_100">
			<div class="align-left">
				<label for="field9">Conformer:</label>
			</div>
			<div class="align-right">
				<input name="conformer" id="field9" type="text">
				<span class="fieldtip">Choice of conformer</span>
			</div>
	  </div>    
          
	  <div id="field11-container" class="field f_100">
			<div class="align-left">
				<label for="field11">Radius (&#8491;):</label>
			</div>
			<div class="align-right big-fieldtip">
				<input pattern="[0-9]+(\.[0-9][0-9]?)?" name="radius" id="field11" type="text" >
				<span class="fieldtip input-big-tooltip">Fixed value of great circle radius</span>
			</div>
	  </div> 
	  
	  <div id="field12-container" class="field f_100">
			<div class="align-left">
				<label for="field12">Seed (&#8491;):</label>
			</div>
			<div class="align-right big-fieldtip">
				<input pattern="[0-9]+(\.[0-9][0-9]?)?" name="seed" id="field12" type="text">
				<span class="fieldtip input-big-tooltip">Initial value of great circle radius</span>
			</div>
	  </div>
	  
	  <div id="field14-container" class="field f_100">
		<div class="align-left">
			<label for="field14" >Scan:</label>
		</div>
		<div class="align-right big-select">
			<select name="scan" id="field14">
				<option id="field14-0" value="">
					-
				</option>
				<option id="field14-1" value="Fine">
					Fine
				</option>
				<option id="field14-2" value="Medium">
					Medium
				</option>
				<option id="field14-3" value="Coarse">
					Coarse
				</option>
			</select>
		</div>
	  </div>
	  
	  
	  <div id="file-pdb-container" class="field f_100">
			<div class="align-left">
				<label for="pdb_id">RCSB PDB ID<sup>*</sup>:</label>
			</div>
			<div class="align-right" >
				<input name="pdb_id" id="pdb_id" type="text" required="required" pattern="^[a-zA-Z0-9]+$" placeholder="e.g. 3I5I" title="Please don't leave this field empty, only alphanumeric characters">
				<span class="fieldtip">Structure ID</span>
			</div>
	  </div>

	  <div id="form-submit" class="field f_100 submit-button submit">
               <input id="submit" value="Process" type="submit" >
      </div>
     </form> 
</article>

<article id="tab2">
<div id="showFileContent" class="tab-content double-lists" style="<?php if(empty($_SESSION['output'])) {echo 'display:none;';}else if(!empty($_SESSION['output'])){echo 'display:block;';}?>">
<h2 class="tab-title" >Results</h2>
<input type="hidden" name="processedFlag" id="processedFlag" value="<?php if($_SESSION['processed']=='true') {echo $_SESSION['processed'];}else{echo 'false';}?>" />
<input type="hidden" name="errorFlag" id="errorFlag" value="<?php if($_SESSION['error']=='true') {echo $_SESSION['error'];}else{echo 'false';}?>" />
<table class="tableOption">
  <tr>
    <td>PDB ID</td>
    <td><a target="_blank" href="http://www.rcsb.org/pdb/explore.do?structureId=<?php if(!empty($_SESSION['pdbId'])) {echo $_SESSION['pdbId'];}?>"><?php if(!empty($_SESSION['pdbId'])) {echo $_SESSION['pdbId'];}?></a></td> 
  </tr>
  <tr>
    <td>Description</td>
    <td><?php if(!empty($_SESSION['description'])) {echo $_SESSION['description'];}?></td> 
  </tr>
  <tr>
    <td>PS Analysis Results</td>
    <td>
	<div id="chooseStructure" >
		Please select a HELIX structure number.
	</div>
	
	<div id="downloadAllImage" style="<?php if(!empty($_SESSION['isZip'])) {echo 'display:block;';}else if(empty($_SESSION['isZip'])){echo 'display:none;';}?>">
      	<span>
	<a id="all-file-img" href="<?php if(!empty($_SESSION['output-zip-name'])) {echo $_SESSION['output-zip-name'];}?>" download="<?php if(!empty($_SESSION['isZip'])) {echo $_SESSION['isZip'];}?>">
	<img src="images/all-files-icon.png" />Download all files
	</a>
	</div>
    </td> 
  </tr>
  <tr>
  <td>HELIX structure</td>
  <td>
	<div class="align-right">
		<select name="helix_files" id="field30" onchange="getCombo(this)">

		<?php
		$directory = $_SESSION['path-to-output'];
		$scanned_directory = scandir($directory);
		
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
		sort($scanned_directory);

		for ($i = 0; $i < count($scanned_directory); ++$i) {
			if (!empty(trim($scanned_directory[$i]))){    
				echo '<option id="field30-'.$scanned_directory[$i].'" value="'.$scanned_directory[$i].'">';
				echo $scanned_directory[$i];
				echo '</option>';
			}
		}
		?>

	</select>
	</div>
	<div id="downloadImage" style="<?php if(!empty($_SESSION['output'])) {echo 'display:block;';}else if(empty($_SESSION['output'])){echo 'display:none;';}?> display: inline-block;">
      	<span>
	<a id="file-img" href="<?php if(!empty($_SESSION['output'])) {echo $_SESSION['output'];}?>" download="<?php if(!empty($_SESSION['output-file-name'])) {echo $_SESSION['output-file-name'];}?>">
	<img src="images/file-icon.png" />Download File
	</a>
	</div>

  </td>
  </tr>
        <?php

	if (!empty($_SESSION["time"]) && !empty($_SESSION["structures"]) && !empty($_SESSION["successfuly"]) && !empty($_SESSION["errors"])) {

		echo '<tr>';
		  echo '<td>Execution time</td>';
		 	echo '<td>'.$_SESSION["time"].'</td>';
		echo '</tr>';
		echo '<tr>';
		  echo '<td>No. of Structures</td>';
			echo '<td>'.$_SESSION["structures"].'</td>';
		echo '</tr>';
		echo '<tr>';
		  echo '<td>Successfuly processed</td>';
			echo '<td>'.$_SESSION["successfuly"].'</td>';
		echo '</tr>';
		echo '<tr>';
		  echo '<td>Non processed</td>';
		       echo '<td>'.$_SESSION["errors"].'</td>';
		echo '</tr>';
	}

        unset($_SESSION["time"]);
	unset($_SESSION["structures"]);
	unset($_SESSION["successfuly"]);
	unset($_SESSION["errors"]);
	?>

</table>

<input type="hidden" name="filePath" id="filePath" value="<?php echo $_SESSION['path-to-output']?>" />
<input type="hidden" name="zipFlag" id="zipFlag" value="<?php echo $_SESSION['isZip']?>" />
<div id="fileDisplayArea">


</div>
<?php unset($_SESSION["output"]); unset($_SESSION["output-file-name"]);unset($_SESSION["pdbId"]);unset($_SESSION["description"]);unset($_SESSION["processed"]);unset($_SESSION["isZip"]);unset($_SESSION["output-zip-name"]);unset($_SESSION["error"]);?>
</div>

<p></p>
</article>

</section>


<footer>

<div id="footer-wrapper">
<div id="footer-left" class="footerdivs"></div>
<div id="footer-right" class="footerdivs">
<strong>Prof Brian Smith</strong><br>
Head, Department of Chemistry and Physics<br>
La Trobe Institute for Molecular Science | La Trobe University | Victoria 3086 Australia | <a target="_blank" href="http://www.latrobe.edu.au/scitecheng/about/staff/profile?uname=b8smith">Web</a><br>
P +61 (0)3 9479 3245 | F +61 (0)3 9479 1266
</div>
</div>


</footer>


</script><script type="text/javascript">
	document.addEventListener('DOMContentLoaded', function() {
		enable(false);
	    	document.getElementById("side-bar").style.display = "none";
		showElement("less",false);
		showElement("more",true);

		if (document.getElementById("processedFlag").value == "true"){
			document.getElementById("tab-showFileContent").style.display = "block";
		
		}else {
			document.getElementById("tab-showFileContent").style.display = "none";
			document.getElementById('selection').value="auto";
		}

	}, false);
</script>

</body>
</html>
