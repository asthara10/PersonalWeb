const A = 1;
const T = 2;
const C = 3;
const G = 4;

var keyAPressed = false;
var keyTPressed = false;
var keyCPressed = false;
var keyGPressed = false;
var keyEnterPressed = false;

var velocity = -0.03;

var gameStat = 0;

document.addEventListener("keydown", keyDownHandler, false);
document.addEventListener("keyup", keyUpHandler, false);

if (BABYLON.Engine.isSupported()) {
    var canvas = document.getElementById("GameCanvas");
	
    //WebGL motor
    var engine = new BABYLON.Engine(canvas, true);
	
    //container for all entities that form the 3D image
    //Lights, camera and materials
    var scene = new BABYLON.Scene(engine);
	
	class Nucleotide{
		constructor(name) {
			this.name = name;
			this.model = BABYLON.Mesh.CreateSphere("Sphere", 16, 2, scene);
			this.material = new BABYLON.StandardMaterial("default", scene);
			
			this.textPlane = BABYLON.Mesh.CreatePlane("outputplane", 25, scene, false);
			this.textPlane.material = new BABYLON.StandardMaterial("outputplane", scene);
			this.textPlane.parent = this.model;
			this.textPlane.position.y = -2.5;
			this.textPlane.position.z = -1;
			this.textPlane.scaling.y = 0.4;
			
			this.textTexture = new BABYLON.DynamicTexture("dynamic texture", 512, scene, true);
			this.textPlane.material.diffuseTexture = this.textTexture;
			this.textPlane.material.specularColor = new BABYLON.Color3(0, 0, 0);
			this.textPlane.material.emissiveColor = new BABYLON.Color3(1, 1, 1);
			this.textPlane.material.backFaceCulling = false;
			switch(name){
				case A:
					this.textTexture.drawText("A", null, 140, "bold 40px verdana", "white");
					this.material.diffuseColor = new BABYLON.Color3(0, 1, 0);
					this.material.emissiveColor = new BABYLON.Color3(0.2, 0.4, 0.2);
					this.material.specularColor = new BABYLON.Color3(1, 1, 1);
				break;
				case T:
					this.textTexture.drawText("T", null, 140, "bold 40px verdana", "white");
					this.material.diffuseColor = new BABYLON.Color3(1, 0, 0);
					this.material.emissiveColor = new BABYLON.Color3(0.4, 0.2, 0.2);
					this.material.specularColor = new BABYLON.Color3(1, 1, 1);
				break;
				case C:
					this.textTexture.drawText("C", null, 140, "bold 40px verdana", "white");
					this.material.diffuseColor = new BABYLON.Color3(0, 0, 1);
					this.material.emissiveColor = new BABYLON.Color3(0.2, 0.0, 0.4);
					this.material.specularColor = new BABYLON.Color3(1, 1, 1);
				break;
				case G:
					this.textTexture.drawText("G", null, 140, "bold 40px verdana", "white");
					this.material.diffuseColor = new BABYLON.Color3(0, 0, 0);
					this.material.emissiveColor = new BABYLON.Color3(0.2, 0.2, 0.2);
					this.material.specularColor = new BABYLON.Color3(1, 1, 1);
					
				break;
			}
			this.textTexture.hasAlpha = true;
			this.model.material = this.material;
		}
		
		getName(){
			return this.name;
		}
		
		getModel(){
			return this.model;
		}
		
		setVisibility(alpha){
			this.material.alpha = alpha;
			this.textPlane.material.alpha = alpha;
		}
		
		setVisibilityNucleotide(alpha){
			this.material.alpha = alpha;
		}
	}
	
	class BasePair{
		constructor(name) { //One of two nucleotides are passed
			this.mark = 0; //
			this.bond = BABYLON.Mesh.CreateCylinder("cylinder", 5, 1, 1, 6, 1, scene, false);
			
			var material = new BABYLON.StandardMaterial("default", scene);
			material.diffuseColor = new BABYLON.Color3(1, 1, 1);
			material.emissiveColor = new BABYLON.Color3(0.6, 0.6, 0.6);
			material.specularColor = new BABYLON.Color3(1, 1, 1);
			this.bond.material = material;
		
			this.nucleotide1 = new Nucleotide(name);
			switch(name){
				case A:
					this.nucleotide2 = new Nucleotide(T);
				break;
				case T:
					this.nucleotide2 = new Nucleotide(A);
				break;
				case C:
					this.nucleotide2 = new Nucleotide(G);
				break;
				case G:
					this.nucleotide2 = new Nucleotide(C);
				break;
			}

			this.nucleotide1.getModel().parent = this.bond;
			this.nucleotide2.getModel().parent = this.bond;
			this.nucleotide1.getModel().position.y = 4;
			this.nucleotide2.getModel().position.y = -4;
			this.nucleotide2.setVisibility(0.0);
		}

		showNucleotide(){
			alert(this.nucleotide1.getName()+" "+this.nucleotide2.getName());
		}
		
		setMark(val){
			this.mark = val; 
		}
		
		getMark(){
			return this.mark;
		}
		
		getBond(){
			return this.bond;
		}
		
		changeBondColor(color){
			this.bond.material.emissiveColor = color;
		}

		getNucleotide1(){
			return this.nucleotide1;
		}
		
		getNucleotide2(){
			return this.nucleotide2;
		}
		
		disposeModel1(){
			this.nucleotide1.getModel().dispose();
		}
		
		disposeModel2(){
			this.nucleotide2.getModel().dispose();
		}
		
		dispose() {
			this.disposeModel1();
			this.disposeModel2();
			this.bond.dispose();
		}
	
	}

	var dna = [];

	dna.push(new BasePair(Math.floor(Math.random() * 4) + 1));
	dna[0].getBond().position.x = 16;
	
	for(i = 1; i < 10; i++){
		dna.push(new BasePair(Math.floor(Math.random() * 4) + 1));
		dna[i].getBond().position.x = dna[i-1].getBond().position.x+2.25;
	}

    var camera = new BABYLON.FreeCamera("Camera", new BABYLON.Vector3(0, 0, -25), scene);
    var light0 = new BABYLON.PointLight("Omni0", new BABYLON.Vector3(0, 100, 0), scene);
	
	
	var background = new BABYLON.Mesh.CreatePlane("background", 100, scene, false);
	background.position.z = 40;
	var materialbg = new BABYLON.StandardMaterial("default", scene);
	materialbg.diffuseColor = new BABYLON.Color3(1, 1, 1);
	materialbg.emissiveColor = new BABYLON.Color3(0.5, 0.5, 0.5);
	materialbg.specularColor = new BABYLON.Color3(0, 0, 0);
	
	background.material = materialbg;
	
    scene.registerBeforeRender(function () {
		//if(keyAPressed)
			
		switch(gameStat){
			case 0: //Menu
			break;
			case 1: //Playing
			break;		
			case 2: //GameOver
			break;	
		}
		
		for(i = 0; i < dna.length;i++){
			if(dna[i].getBond().position.x > -1 && dna[i].getBond().position.x < 1) {
				dna[i].changeBondColor(new BABYLON.Color3(1, 1, 0.2));

				if(dna[i].getMark() == 0 && checkCorrectPress(dna[i].getNucleotide2().getName()) == 1){
					dna[i].setMark(1);
					dna[i].getNucleotide2().setVisibility(1);
					velocity -= 0.0005;
				}
				else if(dna[i].getMark() == 0 && checkCorrectPress(dna[i].getNucleotide2().getName()) == -1){
					dna[i].setMark(-1);
					dna[i].getNucleotide2().setVisibilityNucleotide(0.5); 
				}
			}
			else if(dna[i].getBond().position.x <= -1 && dna[i].getMark() == 0){
				dna[i].setMark(-1);
				dna[i].getNucleotide2().setVisibilityNucleotide(0.5);
				dna[i].changeBondColor(new BABYLON.Color3(0.6, 0.6, 0.6));
			}
			else {
				dna[i].changeBondColor(new BABYLON.Color3(0.6, 0.6, 0.6));
			}
			
			dna[i].getBond().position.x += velocity;
			dna[i].getBond().rotation.x = -dna[i].getBond().position.x*0.2;
		}
		
		if(dna[dna.length-1].getBond().position.x < 15) {
			dna.push(new BasePair(Math.floor(Math.random() * 4) + 1));
			dna[dna.length-1].getBond().position.x = dna[dna.length-2].getBond().position.x+2.25;
		}
		
		if(dna[0].getBond().position.x < -15) {
			dna[0].dispose();
			dna.splice(0,1);
		}
		
    });
	
    var renderloop = function () {
        scene.render();
    };
    //Define a renderizer cycle
    engine.runRenderLoop(renderloop);
}
else {
    alert("WebGL not supported");
}

function keyDownHandler(e) {
	if(e.keyCode == 65) { //a = 65
		keyAPressed = true;
	}
	else if(e.keyCode == 84) { //t = 84
		keyTPressed = true;
	}
	else if(e.keyCode == 67) { //c = 67
		keyCPressed = true;
	}
	else if(e.keyCode == 71) { //g = 71
		keyGPressed = true;
	}
	else if(e.keyCode == 13) { //Enter = 13
		keyEnterPressed == true;
	}
}
function keyUpHandler(e) {
	if(e.keyCode == 65) { //a = 65
		keyAPressed = false;
	}
	else if(e.keyCode == 84) { //t = 84
		keyTPressed = false;
	}
	else if(e.keyCode == 67) { //c = 67
		keyCPressed = false;
	}
	else if(e.keyCode == 71) { //g = 71
		keyGPressed = false;
	}
	else if(e.keyCode == 13) { //Enter = 13
		keyEnterPressed == false;
	}
}

function checkCorrectPress(val){
	switch(val){
		case A:
			if(keyTPressed) return -1;
			else if(keyCPressed) return -1;
			else if(keyGPressed) return -1;
			else if(keyAPressed) return 1;
			else return 0;
		break;
		case T:
			if(keyAPressed) return -1;
			else if(keyCPressed) return -1;
			else if(keyGPressed) return -1;
			else if(keyTPressed) return 1;
			else return 0;
		break;
		case C:
			if(keyAPressed) return -1;
			else if(keyTPressed) return -1;
			else if(keyGPressed) return -1;
			else if(keyCPressed) return 1;
			else return 0;
		break;
		case G:
			if(keyAPressed) return -1;
			else if(keyTPressed) return -1;
			else if(keyCPressed) return -1;
			else if(keyGPressed) return 1;
			else return 0;
		break;
	}
	return 0;
}