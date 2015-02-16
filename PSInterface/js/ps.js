/** 
* Author:      Diego Montufar
* Date:        22/01/2015
* Description: JavaScript file containing all the basic elements behaviour.
**/

/*
setTimeout(function() {
    $('#msgWarning').fadeOut('slow');
}, 10000);
*/
setTimeout(function() {
    $('#msgSuccess').fadeOut('slow');
}, 10000);

setTimeout(function() {
    $('#msgError').fadeOut('slow');
}, 10000);

/*Escape button pressed */
$(document).keyup(function(e) {

  if (e.keyCode == 27) { 

	THREEx.FullScreen.cancel();
	document.getElementById('full-img').src = "images/fullscreen.png"
	document.getElementById('full-img').title = "Go Fullscreen";

   }   // esc
});

/** This is the first method called when loading the page **/
$(window).load(function() {
	/*var myElement = document.getElementById("section-ps"),
	    myResizeFn = function(){
	    		var sizeSection = $("#section-ps").css("height");
	    		var sizeContainer = $("#nav-container").css("height");

	    		sizeSection = sizeSection.replace("px","");
	    		sizeContainer = sizeContainer.replace("px","");	    		

	    		if (parseInt(sizeSection) >= parseInt(sizeContainer) && sizeSection!="" && sizeContainer!=""){
	        		document.getElementById("side-bar").style.height=$("#section-ps").css("height");
	        	}
	    };
		addResizeListener(myElement, myResizeFn);
		//removeResizeListener(myElement, myResizeFn);
*/
	if (!webgl_detect(this)){
		//document.getElementById('msgWarning').style.display = "block";
	}else{
		//document.getElementById('msgWarning').style.display = "none";
	}	
	document.getElementById('msgWarning').style.display = "none";
	$(".loader").fadeOut("slow");
	showElement('loader',false);
	document.getElementById('file-img').style.visibility = "hidden";

	if (document.getElementById("processedFlag").value == "true" && $('#field30 option').length >=3 ){
		showElement('chooseStructure',true);
		showElement('chooseStructureNone',false);
	}else{
		showElement('chooseStructure',false);
		showElement('chooseStructureNone',true);
	}
	showElement('downloadImage',false);
	showElement('downloadAllImage',false);
})



/** Enable/Disable Form fields **/
function enable(setting){

	if(!setting) {
		hideFields(true);	
		document.getElementById('selection').value="auto";	
		disableElement('field3',true);
		disableElement('field4',true);
		disableElement('field16',true);
		disableElement('field17',true);
		disableElement('field7',true);
		disableElement('field18',true);
		disableElement('field8',true);
		disableElement('field9',true);
		disableElement('field11',true);
		disableElement('field12',true);
		disableElement('field14',true);
	}else if(setting) {
		hideFields(false);
		document.getElementById('selection').value="manual";
		disableElement('field3',false);
		disableElement('field4',false);
		disableElement('field16',false);
		disableElement('field17',false);
		disableElement('field7',false);
		disableElement('field18',false);
		disableElement('field8',false);
		disableElement('field9',false);
		disableElement('field11',false);
		disableElement('field12',false);
		disableElement('field14',false);
	}
}

/** Show/Hide Form fields **/
function hideFields(setting){

	if(!setting) {
		showElement('field3-container',true);
		showElement('field4-container',true);
		showElement('field16-container',true);
		showElement('field17-container',true);
		showElement('field7-container',true);
		showElement('field18-container',true);
		showElement('field40-container',true);
		showElement('field9-container',true);
		showElement('field11-container',true);
		showElement('field12-container',true);
		showElement('field14-container',true);
	}else if(setting) {
		showElement('field3-container',false);
		showElement('field4-container',false);
		showElement('field16-container',false);
		showElement('field17-container',false);
		showElement('field7-container',false);
		showElement('field18-container',false);
		showElement('field40-container',false);
		showElement('field9-container',false);
		showElement('field11-container',false);
		showElement('field12-container',false);
		showElement('field14-container',false);
	}

}

/** Enable/Disable element  **/
function disableElement(id,option){
	if (option){
		document.getElementById(id).disabled = true;
	}else{
		document.getElementById(id).disabled = false;
	}
}

/** Show/Hide element  **/
function showElement(id,option){
	if (option){
		document.getElementById(id).style.display = 'block';
	}else{
		document.getElementById(id).style.display = 'none';
  	}
}


/** List Box Behaviour **/
function getCombo(sel) {
    var value = sel.value;
    var path = document.getElementById('filePath').value;
    
    if (value != '-'){ 	
	    document.getElementById('file-img').style.visibility = "visible";
	    var name_file = value.concat(".out");
	    var file = path.concat(name_file);
	    var name_file = value.concat(".out");
	    document.getElementById('file-img').href = file;
	    document.getElementById('file-img').download = name_file;
	    readFile(file);
	    showElement('fileDisplayArea',true);

	if ($('#field30 option').length >= 3){
		showElement('chooseStructure',false);
		showElement('chooseStructureNone',false);
	}else{
		showElement('chooseStructure',false);
		showElement('chooseStructureNone',true);
	}
	    showElement('downloadImage',true);
	    if(document.getElementById('zipFlag').value != ''){	
	    	showElement('downloadAllImage',true);
	    }
	    
    }else{
	    document.getElementById('file-img').style.visibility = "hidden";
		showElement('fileDisplayArea',false);

        if ($('#field30 option').length >= 3){
			showElement('chooseStructure',true);
			showElement('chooseStructureNone',false);
		}else{
			showElement('chooseStructure',false);
			showElement('chooseStructureNone',true);
		}
		showElement('downloadImage',false);
		showElement('downloadAllImage',false);
    }
}


/** List Box Visualization Behaviour **/
function getComboVis(sel) {
    var value = sel.value;
    var path = document.getElementById('filePath').value;
    var pdb_file = document.getElementById('pdbPath').value;

    document.getElementById('hetatm-newpdb').value = "";
    document.getElementById('hetatm-per').value = "";

    if (value != 'None' && value != 'All'){ 
	    var name_file = value.concat(".out");
		var file = path.concat(name_file);	
	    readSingleOutputFile(file);
	}else if (value == 'All'){
		readAllOutputFiles(path);
	}else if (value == 'None'){
		document.getElementById('hetatm-values').value = "";
	}

	$.when.apply(this, readSingleOutputFile)
    .done(function(){
        // when all the data calls are successful you can access the data via
        var pdb_file = document.getElementById('pdbPath').value;
	    getCurrentHETATMsFromPDB(pdb_file); //using $GET
        console.log('Finished reading single files!!!!');
    });

}


/** Read files to show in display area **/
function readFile(file) {

	$.get(file, function(data) {
		var lines = null;
		var records = "";
		var pre = '<pre style="font-size:11px;">';	
		data = pre.concat(data);
		data = data.concat("</pre>");	
 		$('#fileDisplayArea').html(data);
	});
}

/** Read single output file contents **/
function readSingleOutputFile(file) {

	$.get(file, function(data) {
		var lines = null;
		var records = "";

 		/* Take only HETATM records and store them in a textarea for visualization */
 		lines = data.split("\n");
    
      	for (i=0; i<lines.length ;i++){
			if (lines[i].indexOf("HETATM") > -1){
				records = records + lines[i] + "\n";
			}
		}

		document.getElementById('hetatm-values').value = records;

	});
}


/** Loading gif **/
function showLoadingMessage(option){

	if (option){
		$('#loader').css({"display":"block"});
	}else{
		$('#loader').css({"display":"none"});
	}

	return true;
}

/* Tabs behaviour */
$(function(){
  $('ul.tabs li:first').addClass('active');
  $('.blockTabs article').hide();
  $('.blockTabs article:first').show();
  $('ul.tabs li').on('click',function(){
    $('ul.tabs li').removeClass('active');
    $(this).addClass('active')
    $('.blockTabs article').hide();
    var activeTab = $(this).find('a').attr('href');
    $(activeTab).show();
    return false;
  });
})


/* Close message */
function closeSuccess(){
	showElement("msgSuccess",false);
}

function closeError(){
	showElement("msgError",false);
}

/*Side Bar behaviour */
function deactivateSide(){
	$( "#side-bar" ).toggle( "slide" );
	showElement("less",false);
	showElement("more",true);
}

function activateSide(){
	$( "#side-bar" ).toggle( "slide" );
	showElement("more",false);
	showElement("less",true);
}

/*
function resizeNavBar(){
	document.getElementById("side-bar").style.height = $("#section-ps").css("height");
}
*/

/** Confirm before close window **/
/*
window.onbeforeunload = function() {
    return 'Attention!';
}
*/

/** Handle Browser Fallbacks **/

// Checks if attribute is supported by a browser
/*
function attributeSupported(attribute) {
	return (attribute in document.createElement("input"));
}
*/

/** Resize Detection **/

(function(){
  var attachEvent = document.attachEvent;
  var isIE = navigator.userAgent.match(/Trident/);
  console.log(isIE);
  var requestFrame = (function(){
    var raf = window.requestAnimationFrame || window.mozRequestAnimationFrame || window.webkitRequestAnimationFrame ||
        function(fn){ return window.setTimeout(fn, 20); };
    return function(fn){ return raf(fn); };
  })();
  
  var cancelFrame = (function(){
    var cancel = window.cancelAnimationFrame || window.mozCancelAnimationFrame || window.webkitCancelAnimationFrame ||
           window.clearTimeout;
    return function(id){ return cancel(id); };
  })();
  
  function resizeListener(e){
    var win = e.target || e.srcElement;
    if (win.__resizeRAF__) cancelFrame(win.__resizeRAF__);
    win.__resizeRAF__ = requestFrame(function(){
      var trigger = win.__resizeTrigger__;
      trigger.__resizeListeners__.forEach(function(fn){
        fn.call(trigger, e);
      });
    });
  }
  
  function objectLoad(e){
    this.contentDocument.defaultView.__resizeTrigger__ = this.__resizeElement__;
    this.contentDocument.defaultView.addEventListener('resize', resizeListener);
  }
  
  window.addResizeListener = function(element, fn){
    if (!element.__resizeListeners__) {
      element.__resizeListeners__ = [];
      if (attachEvent) {
        element.__resizeTrigger__ = element;
        element.attachEvent('onresize', resizeListener);
      }
      else {
        if (getComputedStyle(element).position == 'static') element.style.position = 'relative';
        var obj = element.__resizeTrigger__ = document.createElement('object'); 
        obj.setAttribute('style', 'display: block; position: absolute; top: 0; left: 0; height: 100%; width: 100%; overflow: hidden; pointer-events: none; z-index: -1;');
        obj.__resizeElement__ = element;
        obj.onload = objectLoad;
        obj.type = 'text/html';
        if (isIE) element.appendChild(obj);
        obj.data = 'about:blank';
        if (!isIE) element.appendChild(obj);
      }
    }
    element.__resizeListeners__.push(fn);
  };
  
  window.removeResizeListener = function(element, fn){
    element.__resizeListeners__.splice(element.__resizeListeners__.indexOf(fn), 1);
    if (!element.__resizeListeners__.length) {
      if (attachEvent) element.detachEvent('onresize', resizeListener);
      else {
        element.__resizeTrigger__.contentDocument.defaultView.removeEventListener('resize', resizeListener);
        element.__resizeTrigger__ = !element.removeChild(element.__resizeTrigger__);
      }
    }
  }
})();


/*
$(document).ready(function () {

	var formElement = "#PSForm";
	var formElementRequired = "#PSForm [required]";

	//Required attribute fallback
	$(formElement).submit(function() {
		if (!attributeSupported("required") || navigator.userAgent.search("Safari")>=0 && navigator.userAgent.search("Chrome")<0) {
			showLoadingMessage(true);
			//If required attribute is not supported or browser is Safari (Safari thinks that it has this attribute, but it does not work), then check all fields that has required attribute 
			$(formElementRequired).each(function(index) {
				if (!$(this).val()) {
					//If at least one required value is empty, then ask to fill all required fields.
					alert('hey');
					showLoadingMessage(true);
					return false;
				}
			});	
		} 
		return true; //This is a test form and I'm not going to submit it
	});	

/*	
	//Pattern attribute fallback
	if (!attributeSupported("pattern") || ($.browser.safari)) {
		//If pattern attribute is not supported or browser is Safari (Safari thinks that it has this attribute, but it does not work), then check the field on change
		$('#attrPattern').change(function() {
			validateRegExp($(this).val(), formValidationRules.name); 
		});
	} 	

	//Field type number
	if (!inputTypeSupported("number")) {
		$('#typeNumber').change(function() {
			if ($(this).val()) {
				if (!$.isNumeric($(this).val())) {
					alert("Set numeric value.");
				}
			}
		});
	}
	
	//Field type email
	if (!inputTypeSupported("email") || $.browser.safari) {  
		$('#typeEmail').change(function() {
			validateRegExp($(this).val(), formValidationRules.email); 
		});	
	}	
	
});	
*/


/* Read all output files and get only HETATM records */
function readAllOutputFiles(path){

		var i;
		var name_file;
		var file;

		//clear textarea:
		document.getElementById('hetatm-values').value = "";

		var options = document.getElementById('glmol01_psresults').options;

		for (i=0 ; i<options.length ; i++){

			if (options[i].value !='All' && options[i].value != 'None'){

				name_file = options[i].value.concat(".out");
				file = path.concat(name_file);

				$.get(file, function(data) {
					var records = "";
				    var lines = null;
			        lines = data.split("\n");
			    	
			      	for (i=0; i<lines.length ;i++){
						if (lines[i].indexOf("HETATM") > -1){

							if (i != lines.length -2){
								records = records + lines[i] + "\n";
							}else{
								records = records + lines[i];
							}
						}
					}

					document.getElementById('hetatm-values').value = document.getElementById('hetatm-values').value + records;
			    });
			}
		}
}

/* Reorder index of HETATMs records */
function reorderHETATMs(){

	var hetatmList = document.getElementById('hetatm-values').value;
	var list = hetatmList.split("\n");

	var records = "";
	var temp = "";
	var beg = 7;
	var end = 11;
	var next_number = 0;
	var finalList = "";

	var i;

	for (i=0; i<list.length -1;i++){

		temp ="";

		temp = removeAt(list[i],beg,end);

		var count = i + 1;
		var str = count.toString();
		
		if (i+1<10){
			str = "   " + str;
		}else if (i+1 >=10 && i+1 <100){
			str = "  " + str;
		}else if(i+1 >=100 && i+1 <1000){
			str = " " + str;
		}else{
			str = "" + str;
		}

		if (i != list.length - 1){
			records = records + replaceAt(temp,beg,str+ " ") + "\n";
	 	}else{
	 		records = records + replaceAt(temp,beg,str+ " ");
	 	}

	}

	document.getElementById('hetatm-values').value = records;

}


function reinsertHETATMsToPDB(pdb_file){

	console.log("I'm starting to reinsert HETATMs to PDB file");
	var lines = null;
	var records = "";
	var i;
	

	$.get(pdb_file, function(data) {
      	lines = data.split("\n");
      	var placeFound = false;
      	
      	for (i=0; i<lines.length ;i++){

      		if (placeFound){
      			var hetatms = document.getElementById('hetatm-values').value;
      			records = records + hetatms;
      			placeFound = false;
      		}

      		if (lines[i].substring(0,3) == "TER"){
      			placeFound = true;
      		}

      		if (lines[i].substring(0,6) != "HETATM" && lines[i].substring(0,6) != "      "){
      			if (i != lines.length - 2){
					records = records + lines[i] + "\n";
				}else{
					records = records + lines[i];
				}

			}
		}

		document.getElementById('hetatm-newpdb').value = records;
		glmol01.loadMoleculeStr(null,document.getElementById('hetatm-newpdb').value);
        glmol01.defineRepresentation = defineRepFromController;
        glmol01.rebuildScene();
        glmol01.show();

    });
	
    return false;
}

function mixAllHETATMs(){
	/* join with ps HETATMs */
	if (document.getElementById('hetatm-current').value != ""){
		document.getElementById('hetatm-values').value =  document.getElementById('hetatm-current').value + document.getElementById('hetatm-values').value;
	}

}


function getCurrentHETATMsFromPDB(pdb_file){

	var lines = null;
	var records = "";
	var i;

	$.get(pdb_file, function(data) {
      	lines = data.split("\n");
      	
      	for (i=0; i<lines.length ;i++){

      		if (lines[i].substring(0,3) == "TER"){
      			document.getElementById('hetatm-per').value = lines[i].substring(6,11);
      		}

			if (lines[i].substring(0,6) == "HETATM"){
				if (i != lines.length - 2){
					records = records + lines[i] + "\n";
				}else{
					records = records + lines[i];
				}
			}
		}

		document.getElementById('hetatm-current').value = records;

    });
	
    return false;

} 


function replaceAt(s, n, t) {
    return s.substring(0, n) + t + s.substring(n + 1);
}

function removeAt(str,beg,end){
	return str.replace(str.substring(beg, end), "");
}


           




