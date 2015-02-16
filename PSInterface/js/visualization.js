/** 
* Author:      Diego Montufar
* Date:        9/02/2015
* Description: Visualization tools hepler for using Glmol and Three.js
**/

var glmol01 = null;

if (!webgl_detect(this)){
	console.log('Disabling Visualization Tools...');
}else{
	glmol01 = new GLmol('glmol01', true);
}
 

/* Load PDB file inside canvas */
function renderPDBFile(file) {
   $.get(file, function(data) {
      glmol01.loadMoleculeStr(null,data);
      var color = document.getElementById('glmol01_bgcolor').value;
   });
}

/* Load PDB file inside canvas */
function loadPSHETATMs(data) {
      glmol02.loadMoleculeStr(null,data);
      console.log('HETATMs loaded!');
}

/* Full Screen option */
function goFullScreen(){
   if (THREEx.FullScreen.activated() && THREEx.FullScreen.available()){
      THREEx.FullScreen.cancel();
      document.getElementById('full-img').src = "images/fullscreen.png"
      document.getElementById('full-img').title = "Go Fullscreen";
   }else{
      THREEx.FullScreen.request(document.getElementById("glmol01"));
      document.getElementById('full-img').src = "images/exit-fullscreen.png"
      document.getElementById('full-img').title = "Exit Fullscreen";
   }
}

/* Take screenshot option */
function saveImage() {
   glmol01.show();
   var imageURI = glmol01.renderer.domElement.toDataURL("image/png");
   window.open(imageURI);
}

/* Reset parameters and re-render canvas */
function resetVis(){

    document.getElementById('glmol01_color').value = 'chainbow';
    document.getElementById('glmol01_bgcolor').value = '0x000000';
    document.getElementById('glmol01_projection').value = 'perspective';
    document.getElementById('glmol01_mainchain').value = 'thickRibbon';
    document.getElementById('glmol01_nb').value = 'nb_cross';
    document.getElementById('glmol01_hetatm').value = 'sphere';
    document.getElementById('glmol01_base').value = 'nuclLine';
    document.getElementById('glmol01_line').checked = false;
    document.getElementById('glmol01_doNotSmoothen').checked = false;
    document.getElementById('glmol01_cell').checked = false;
    document.getElementById('glmol01_biomt').checked = false;
    document.getElementById('glmol01_packing').checked = false;
    document.getElementById('glmol01_symopHetatms').checked = false;

    reloadVis();

}

/* Detect if web browser supports WebGL */
function webgl_detect(return_context)
{
    if (!!window.WebGLRenderingContext) {
        var canvas = document.createElement("canvas"),
             names = ["webgl", "experimental-webgl", "moz-webgl", "webkit-3d"],
           context = false;
 
        for(var i=0;i<4;i++) {
            try {
                context = canvas.getContext(names[i]);
                if (context && typeof context.getParameter == "function") {
                    // WebGL is enabled
                    if (return_context) {
                        // return WebGL object if the function's argument is present
                        return {name:names[i], gl:context};
                    }
                    // else, return just true
                    return true;
                }
            } catch(e) {}
        }
 
        // WebGL is supported, but disabled
        return false;
    }
 
    // WebGL not supported
    return false;
}

/* Update changes to canvas */
function reloadVis(){

   $.when.apply(this, getCurrentHETATMsFromPDB)
    .done(function(){
        // when all the data calls are successful you can access the data via
        console.log('Finished reading current HETATMs!!!!');
        console.log('Starting mixing all');
        mixAllHETATMs(); //not using $GET
        reorderHETATMs(); //not using $GET

        var pdb_file = document.getElementById('pdbPath').value;
        reinsertHETATMsToPDB(pdb_file);
        
    });

    if(document.getElementById('glmol01_projection').value == 'None'){
        console.log('accessed as None');
        glmol01.defineRepresentation = defineRepFromController;
        glmol01.rebuildScene();
        glmol01.show();
    }
}

/* Form Controller */
function defineRepFromController() {
   var idHeader = "#glmol01_";

var time = new Date();
   var all = this.getAllAtoms();
   if ($(idHeader + 'biomt').attr('checked') && this.protein.biomtChains != "") all = this.getChain(all, this.protein.biomtChains);
   var allHet = this.getHetatms(all);
   var hetatm = this.removeSolvents(allHet);
   console.log('Abajo esta hetatm');
   console.log(hetatm);

console.log("selection " + (+new Date() - time)); time = new Date();

   this.colorByAtom(all, {});  
   var colorMode = $(idHeader + 'color').val();
   if (colorMode == 'ss') {
      this.colorByStructure(all, 0xcc00cc, 0x00cccc);
   } else if (colorMode == 'chain') {
      this.colorByChain(all);
   } else if (colorMode == 'chainbow') {
      this.colorChainbow(all);
   } else if (colorMode == 'b') {
      this.colorByBFactor(all);
   } else if (colorMode == 'polarity') {
      this.colorByPolarity(all, 0xcc0000, 0xcccccc);
   }
console.log("color " + (+new Date() - time)); time = new Date();

   var asu = new THREE.Object3D();
   var mainchainMode = $(idHeader + 'mainchain').val();
   var doNotSmoothen = ($(idHeader + 'doNotSmoothen').attr('checked') == 'checked');

   if (mainchainMode != 'none') {
      if (mainchainMode == 'ribbon') {
         this.drawCartoon(asu, all, doNotSmoothen);
         this.drawCartoonNucleicAcid(asu, all);
      } else if (mainchainMode == 'thickRibbon') {
         this.drawCartoon(asu, all, doNotSmoothen, this.thickness);
         this.drawCartoonNucleicAcid(asu, all, null, this.thickness);
      } else if (mainchainMode == 'strand') {
         this.drawStrand(asu, all, null, null, null, null, null, doNotSmoothen);
         this.drawStrandNucleicAcid(asu, all);
      } else if (mainchainMode == 'chain') {
         this.drawMainchainCurve(asu, all, this.curveWidth, 'CA', 1);
         this.drawMainchainCurve(asu, all, this.curveWidth, 'O3\'', 1);
      } else if (mainchainMode == 'cylinderHelix') {
         this.drawHelixAsCylinder(asu, all, 1.6);
         this.drawCartoonNucleicAcid(asu, all);
      } else if (mainchainMode == 'tube') {
         this.drawMainchainTube(asu, all, 'CA');
         this.drawMainchainTube(asu, all, 'O3\''); // FIXME: 5' end problem!
      } else if (mainchainMode == 'bonds') {
         this.drawBondsAsLine(asu, all, this.lineWidth);
      }
   }


   //alert($(idHeader + 'line').attr('checked'));
   if ($(idHeader + 'line').attr('checked')) {
      this.drawBondsAsLine(this.modelGroup, this.getSidechains(all), this.lineWidth);
   }
console.log("mainchain " + (+new Date() - time)); time = new Date();

   var base = $(idHeader + 'base').val();
   if (base != 'none') {
      if (base == 'nuclStick') {
         this.drawNucleicAcidStick(this.modelGroup, all);
      } else if (base == 'nuclLine') {
         this.drawNucleicAcidLine(this.modelGroup, all);
      } else if (base == 'nuclPolygon') {
         this.drawNucleicAcidLadder(this.modelGroup, all);
     }
   }

   var nbMode = $(idHeader + 'nb').val();
   var target = $(idHeader + 'symopHetatms').attr('checked') ? asu : this.modelGroup;
   if (nbMode != 'none') {
      var nonBonded = this.getNonbonded(allHet);
      if (nbMode == 'nb_sphere') {
         this.drawAtomsAsIcosahedron(target, nonBonded, 0.3, true);
      } else if (nbMode == 'nb_cross') {
         this.drawAsCross(target, nonBonded, 0.3, true);

      }
   }

   var hetatmMode = $(idHeader + 'hetatm').val();
   //var hetatmModeVis = $(idHeader + 'psresults').val();
   if (hetatmMode != 'none') {
      if (hetatmMode == 'stick') {
         this.drawBondsAsStick(target, hetatm, this.cylinderRadius, this.cylinderRadius, true);
      } else if (hetatmMode == 'sphere') {
         this.drawAtomsAsSphere(target, hetatm, this.sphereRadius);
      } else if (hetatmMode == 'line') {
         this.drawBondsAsLine(target, hetatm, this.curveWidth);
      } else if (hetatmMode == 'icosahedron') {
         this.drawAtomsAsIcosahedron(target, hetatm, this.sphereRadius);
     } else if (hetatmMode == 'ballAndStick') {
         this.drawBondsAsStick(target, hetatm, this.cylinderRadius / 2.0, this.cylinderRadius, true, false, 0.3);
     } else if (hetatmMode == 'ballAndStick2') {
         this.drawBondsAsStick(target, hetatm, this.cylinderRadius / 2.0, this.cylinderRadius, true, true, 0.3);
     } 

   }



console.log("hetatms " + (+new Date() - time)); time = new Date();

   var projectionMode = $(idHeader + 'projection').val();
   if (projectionMode == 'perspective') this.camera = this.perspectiveCamera;
   else if (projectionMode == 'orthoscopic') this.camera = this.orthoscopicCamera;
  
   var tempColor = $(idHeader + 'bgcolor').val();
   this.setBackground(parseInt(tempColor));

   if ($(idHeader + 'cell').attr('checked')) {
      this.drawUnitcell(this.modelGroup);
   }

   if ($(idHeader + 'biomt').attr('checked')) {
      this.drawSymmetryMates2(this.modelGroup, asu, this.protein.biomtMatrices);
   }
   if ($(idHeader + 'packing').attr('checked')) {
      this.drawSymmetryMatesWithTranslation2(this.modelGroup, asu, this.protein.symMat);
   }
   this.modelGroup.add(asu);
};

/* Assign controller to the Object for visualization*/
if (!webgl_detect(this) && glmol01 != null){
	glmol01.defineRepresentation = defineRepFromController;
}
