<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1, minimum-scale=1, maximum-scale=2">
    
    <!-- LTU //-->
    <meta name="description" content="PS (Pandus Semita), is a program for the analysis of helix geometry" />
    <meta name="keywords" content="Alpha helix, 310 helix, Pi helix, Radius of curvature, Phase yield, Pitch, Helix analysis, PS" />
    <meta name="audience" content="students, staff members, researchers" />
    <meta name="author" content="La Trobe University" />
    <meta name="assetcreated" content="04 December 2014" />

    <title>PS Helix Analysis</title>
    
    <!-- 59031 -->
    <link href="http://www.latrobe.edu.au/_media/la-trobe-api/v5/v5/default/core.css" rel="stylesheet" media="screen" />
    <link href="http://www.latrobe.edu.au/_media/la-trobe-api/v5/v5/default/styles.css" rel="stylesheet" media="screen" />
    <link href="http://www.latrobe.edu.au/_media/la-trobe-api/v5/v5/default/print.css" rel="stylesheet" media="print" />
    <link href="http://www.latrobe.edu.au/__data/assets/css_file/0006/564144/jquery-ui-1.10.3.css" rel="stylesheet" media="all" /> 
    
    <script>
    (function(){
      // if firefox 3.5+, hide content till load (or 3 seconds) to prevent FOUT
      var d = document, e = d.documentElement, s = d.createElement('style');
      if (e.style.MozTransform === ''){ // gecko 1.9.1 inference
        s.textContent = 'body{visibility:hidden}';
        var r = document.getElementsByTagName('script')[0];
        r.parentNode.insertBefore(s, r);
        function f(){ s.parentNode && s.parentNode.removeChild(s); }
        addEventListener('load',f,false);
        setTimeout(f,3000); 
      }
    })();
    </script>    
    
    <link href="css/style.css" media="screen" rel="stylesheet" type="text/css"/> 

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
</head>
<body>


<!--LAYOUT-->
<div class="layout">

  <main class="main-content" role="main" id="content">
  <div id="bodyContent" class="content-wrapper">

<!---------------------------------------------------------------------------------------------------------------------->

<!--WARNING - This is a linked div -->
<h1>Helix Analysis</h1>

<?php 
	session_start(); 
	if(!empty($_SESSION["response"])) {
	echo '<div class="live-tab-content" style="margin-bottom:5px; padding: 10px;">'.$_SESSION['response'];
	if (!empty($_SESSION["image"])){	
		echo '<img style="padding: 0; float: none;" src="images/'.$_SESSION["image"].'.ico" />';
	}
	echo '</div>';
}?>      
<?php unset($_SESSION["response"]); unset($_SESSION["image"]);?>


<div class="tabs" >
<div id="param" class="tab-content double-lists">
<h2 class="tab-title">PDB Helix Analysis</h2>

<div class="PSForm-container submit">
     
<form class="PSForm" method="post" enctype="multipart/form-data" action="process_form.php">
          
	  <div id="field2-container" class="field f_100">
			<div class="align-left">
				<label for="field2">Name:</label>
			</div>
			<div class="align-right">
				<input name="name" id="field2" required="required" type="text" placeholder="e.g. Alpha Helix Example" title="Please don't leave this field empty">
				<span class="fieldtip">Enter a name for the job</span>
			</div>
	  </div>

	  <div id="field20-container" class="field f_100">
			<div class="align-left">
				<label for="field2">Process:</label>
			</div>
			    <div class="radio-toolbar" style="width:600px;">  
				<input id="manual" checked="true" type="radio" name="process" value="manual" onClick="enable(true)">  
				<label for="manual">Manual</label>  
				<input id="auto" type="radio" name="process" value="auto" onClick="enable(false)">  
				<label for="auto">Automated</label>  
			    </div>  
	  </div>

	   <div id="field3-container" class="field f_100">
			<div class="align-left">
				<label for="field3">Helix:</label>
			</div>
			<div class="align-right">
				<input name="helix" id="field3" type="number">
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
			<div class="align-right">
				<input pattern="[0-9a-zA-Z]+(,[0-9a-zA-Z]+){0,4}" name="atom_type" id="field7" type="text" placeholder="e.g. CA" title="Up to 5 comma separated values">
				<span class="fieldtip">Name of atom(s) to select</span>
			</div>
	  </div>  
      
	  <div id="field18-container" class="field f_100">
			<div class="align-left">
				<label for="field18">Model:</label>
			</div>
			<div class="align-right">
				<input max="999" min="1" name="model" id="field18" type="number">
				<span class="fieldtip">Model number in a NMR PDB file</span>
			</div>
	  </div>  
	  
	  <div id="field18-container" class="field f_100">
			<div class="align-left">
				<label for="field8">Reference Type:</label>
			</div>
			<div class="align-right">
				<input pattern="[0-9a-zA-Z]+(,[0-9a-zA-Z]+){0,4}" name="ref_type" id="field8" type="text" placeholder="e.g. C,N,O" title="Up to 5 comma separated values">
				<span class="fieldtip">List of atom types</span>
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
			<div class="align-right">
				<input pattern="[0-9]+(\.[0-9][0-9]?)?" name="radius" id="field11" type="text" >
				<span class="fieldtip">Fixed value of great circle radius</span>
			</div>
	  </div> 
	  
	  <div id="field12-container" class="field f_100">
			<div class="align-left">
				<label for="field12">Seed (&#8491;):</label>
			</div>
			<div class="align-right">
				<input pattern="[0-9]+(\.[0-9][0-9]?)?" name="seed" id="field12" type="text">
				<span class="fieldtip">Initial value of great circle radius</span>
			</div>
	  </div>
	  
	  <div id="field14-container" class="field f_100">
			<div class="align-left">
				<label for="field14">Scan:</label>
			</div>
			<div class="align-right">
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
	  
	  
	  <div id="file-pdb-container" class="field f_100" style="margin-top:10px;">
			<div class="align-left">
				<label for="file-pdb">RCSB PDB ID:</label>
			</div>
			<div class="align-right" >
				<input name="pdb_id" id="pdb_id" type="text" required="required">
				<span class="fieldtip">ID of the structure</span>
			</div>
	  </div>

	  <div id="form-submit" class="field f_100 clearfix submit" style="width:90%;">
               <input value="Process" type="submit">
          </div>
     </form> 
     
</div>
</div>


<div id="showFileContent" class="tab-content double-lists" style="<?php if(empty($_SESSION['output'])) {echo 'display:none;';}else if(!empty($_SESSION['output'])){echo 'display:block;';}?>">
<h2 class="tab-title" >Results</h2>
<input type="hidden" name="processedFlag" id="processedFlag" value="<?php if($_SESSION['processed']=='true') {echo $_SESSION['processed'];}else{echo 'false';}?>" />
<table style="width:100%">
  <tr>
    <td>PDB ID</td>
    <td><?php if(!empty($_SESSION['pdbId'])) {echo $_SESSION['pdbId'];}?></td> 
  </tr>
  <tr>
    <td>Description</td>
    <td><?php if(!empty($_SESSION['description'])) {echo $_SESSION['description'];}?></td> 
  </tr>
  <tr>
    <td>Details</td>
    <td><a href="http://www.rcsb.org/pdb/explore.do?structureId=<?php if(!empty($_SESSION['pdbId'])) {echo $_SESSION['pdbId'];}?>"><?php if(!empty($_SESSION['pdbId'])) {echo $_SESSION['pdbId'];}?></a></td> 
  </tr>
  <tr>
    <td>PS Analysis Results</td>
    <td>
<div style="<?php if(!empty($_SESSION['output'])) {echo 'display:block;';}else if(empty($_SESSION['output'])){echo 'display:none;';}?>">
      <span><a href="<?php if(!empty($_SESSION['output'])) {echo $_SESSION['output'];}?>" download="<?php if(!empty($_SESSION['output-file-name'])) {echo $_SESSION['output-file-name'];}?>"><img style="padding: 0; float: none; width:40px;" src="images/file-icon.png" /></a></span><br>
</div>
    </td> 
  </tr>
</table>

<div id="fileDisplayArea">
<?php
    $myfilename = $_SESSION['output'];
    if(file_exists($myfilename)){
	$array = explode("\n", file_get_contents($myfilename));
	echo '<pre style="font-size:11px;">';
	foreach($array as $key => $val)
	{
	   
		echo $val."</br>";
	}	
	echo "</pre>";
    }
?>
</div>
<?php unset($_SESSION["output"]); unset($_SESSION["output-file-name"]);unset($_SESSION["pdbId"]);unset($_SESSION["description"]);unset($_SESSION["processed"]);?>
</div>

<p></p>

</main>

<div id="sidebar">
  <h1>
  PS (Pandus Semita) 
  </h1>
  <p> 
  <h2>A program for the analysis of helix geometry</h2>
  </p>
<h3>Description</h3>
<p></p>
<p>
PS determines the parameters that define the great circle arc that minimises the variance in distance from this arc to a collection of points that defines each monomer in the helical chain. 
Properties of helices include helix radius, pitch and radius of curvature. This approach can be applied to any helical structure of proteins, DNA or carbohydrates.</p>

<p>
Supporting information may be found in the online version of this <a href="http://www.sciencedirect.com/science/article/pii/S109332631100163X">Article</a>, containing an analysis of helices in DNA, left- and right-handed helices, heparin and collagen, 
five PDB format files for the ideal 3<sub>10</sub>, alpha, pi, polyproline-I and polyproline-II helices, an example output, instructions for use of the program, 
the FORTRAN source code for the program PS, and an example Perlscript for the automated analysis of PDB-format files.

</p>

</div>

</div> <!--END LAYOUT-->




<!---------------------------------------------------------------------------------------------------------------------->

<script src="http://www.latrobe.edu.au/__data/assets/js_file/0004/564142/jquery-1.8.3.min.js"></script>
<script src="http://www.latrobe.edu.au/__data/assets/js_file/0005/564143/jquery-ui-1.10.3.min.js"></script>

<!-- MODERNIZR -->
<script src="http://www.latrobe.edu.au/__data/assets/js_file/0005/541562/modernizr.custom.js"></script>
<!-- BEHAVIOURS-->
<script src="http://www.latrobe.edu.au/__data/assets/js_file/0006/547836/behavioursjs.js"></script>
<!--UNSLIDER-->
<script src="http://www.latrobe.edu.au/__data/assets/js_file/0005/548843/unlisderjs.js"></script>
<!--MOUSEWHEEL-->
<script src="http://www.latrobe.edu.au/__data/assets/js_file/0006/549627/jquery.mousewheel.js"></script>
<!-- LAZY LOAD -->
<script src="http://www.latrobe.edu.au/__data/assets/js_file/0008/590552/jquery.lazyload.min.js"></script>

<script type="text/javascript" src="http://www.latrobe.edu.au/__data/assets/js_file/0009/122868/jquery-cycle-litejs.js"></script>

<script type="text/javascript">
$('#news-ticker').cycle({ 
    prev:   '#btn_prev', 
    next:   '#btn_next'
});

if ($('.news-item').length < 2){
  $('.newsnav').remove();
};

</script><script type="text/javascript">
  $('#nav-breadcrumb').html('You are here: <a href="http://www.latrobe.edu.au">University home</a>&nbsp;&gt;&nbsp;<a href="/students">Students</a>');
</script>

</script>
<script type="text/javascript">
function enable(setting){

if(!setting) {
	document.getElementById('field3').disabled = true;
	document.getElementById('field4').disabled = true;
	document.getElementById('field16').disabled = true;
	document.getElementById('field17').disabled = true;
	document.getElementById('field7').disabled = true;
	document.getElementById('field18').disabled = true;
	document.getElementById('field8').disabled = true;
	document.getElementById('field9').disabled = true;
	document.getElementById('field11').disabled = true;
	document.getElementById('field12').disabled = true;
	document.getElementById('field14').disabled = true;

}else if(setting) {

	document.getElementById('field3').disabled = false;
	document.getElementById('field4').disabled = false;
	document.getElementById('field16').disabled = false;
	document.getElementById('field17').disabled = false;
	document.getElementById('field7').disabled = false;
	document.getElementById('field18').disabled = false;
	document.getElementById('field8').disabled = false;
	document.getElementById('field9').disabled = false;
	document.getElementById('field11').disabled = false;
	document.getElementById('field12').disabled = false;
	document.getElementById('field14').disabled = false;
	}
}

</script>

<script type="text/javascript">

document.addEventListener('DOMContentLoaded', function() {
if (document.getElementById("processedFlag").value == "true"){
		document.getElementById("tab-showFileContent").style.display = "block";
	}else {
		document.getElementById("tab-showFileContent").style.display = "none";
	}
}, false);

</script>

<script src="http://www.latrobe.edu.au/__data/assets/js_file/0018/256041/autosuggest_v1_1.js"></script>
<!-- <script src="//www.latrobe.edu.au/__data/assets/js_file/0018/256041/autosuggest_v1_1.js"></script> -->


<!-- Begin Code -->
<noscript> <div><img src="http://webstat.latrobe.edu.au/i.latrobe?ct=cms" alt="" /></div> </noscript>
<script type="text/javascript" src="http://webstat.latrobe.edu.au/jscripts/latrobe_cms.js"></script>
<!-- End Code -->

<!-- Google Tag Manager -->
<noscript><iframe src="http://www.googletagmanager.com/ns.html?id=GTM-PK7MLW"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PK7MLW');</script>
<!-- End Google Tag Manager -->

<!--endnoindex-->

</body>
</html>
