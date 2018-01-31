const A = 1;
const T = 2;
const G = 3;
const C = 4;

var velocity = 0.01;
var chain = [];

class Nucleotide {
	constructor(type){
		this.type = type;
		this.model = BABYLON.Mesh.CreateSphere("nucleotide", 16, 0.75, scene);
		var mat = new BABYLON.StandardMaterial("mat", scene);
		switch(type){
			case A:
				mat.diffuseColor = new BABYLON.Color3(0, 1, 0);
				mat.emissiveColor = new BABYLON.Color3(0.2, 0.4, 0.2);
			break;
			case T:
				mat.diffuseColor = new BABYLON.Color3(1, 0, 0);
				mat.emissiveColor = new BABYLON.Color3(0.4, 0.2, 0.2);
			break;
			case G:
				mat.diffuseColor = new BABYLON.Color3(0, 0, 0);
				mat.emissiveColor = new BABYLON.Color3(0.2, 0.2, 0.2);
			break;
			case C:
				mat.diffuseColor = new BABYLON.Color3(0, 0, 1);
				mat.emissiveColor = new BABYLON.Color3(0.2, 0.2, 0.4);
			break;
		}
		this.model.material = mat;
	}
}
class Bound {
	constructor(type){
		this.type = type;
		this.model = BABYLON.Mesh.CreateCylinder("bound", 1.5, 0.25, 0.25, 8, 2, scene);
		this.nucleotide1 = new Nucleotide(type);
		this.nucleotide1.model.position.y += 1.5;
		this.nucleotide1.model.parent = this.model;
		var mat = new BABYLON.StandardMaterial("mat", scene);
		var x = 0.4;
		mat.diffuseColor = new BABYLON.Color3(x, x, x);
		mat.emissiveColor = new BABYLON.Color3(x, x, x);
		this.model.material = mat;
		switch(type){
			case A:
				this.nucleotide2 = new Nucleotide(T);
			break;
			case T: 
				this.nucleotide2 = new Nucleotide(A);
			break;
			case G: 
				this.nucleotide2 = new Nucleotide(C);
			break;
			case C: 
				this.nucleotide2 = new Nucleotide(G);
			break;
		}
		this.nucleotide2.model.position.y -= 1.5;
		this.nucleotide2.model.parent = this.model;
	}
	dispose() {
		this.nucleotide1.model.dispose();
		this.nucleotide2.model.dispose();
		this.model.dispose();
	}
}


if (BABYLON.Engine.isSupported()) {
	var canvas = document.getElementById("GameCanvas");
	var engine = new BABYLON.Engine(canvas, true);
	var scene = new BABYLON.Scene(engine);
	
	// This creates and positions a free camera (non-mesh)
    var camera = new BABYLON.FreeCamera("camera1", new BABYLON.Vector3(0, 0, -10), scene);

    // This targets the camera to scene origin
    camera.setTarget(BABYLON.Vector3.Zero());

    // This attaches the camera to the canvas
    camera.attachControl(canvas, true);

    // This creates a light, aiming 0,1,0 - to the sky (non-mesh)
    var light = new BABYLON.HemisphericLight("light1", new BABYLON.Vector3(0, 1, 0), scene);

    // Default intensity is 1. Let's dim the light a small amount
    light.intensity = 0.7;

	var plane = BABYLON.MeshBuilder.CreatePlane("plane", {width: 20, height: 15}, scene);
	plane.position.z = 5;
	plane.material = new BABYLON.StandardMaterial("mat", scene);
	plane.material.emissiveColor = new BABYLON.Color3(1, 1, 1);
	
	chain.push(new Bound(Math.floor((Math.random()*4)+1)));
	chain[0].model.position.x = 6;
	
	scene.registerBeforeRender(function () {
		//chain.push(new Bound(Math.floor((Math.random()*4)+1)));
		for (i = 0; i < chain.length; i++) {
			if(chain[chain.length-1].model.position.x <= 5){
				chain.push(new Bound(Math.floor((Math.random()*4)+1)));
				chain[chain.length-1].model.position.x = 6;
			}
			chain[i].model.position.x -= velocity;
			chain[i].model.rotation.x = -chain[i].model.position.x*0.5;
		}
		if(chain[0].model.position.x < -6) {
			chain[0].dispose();
			chain.splice(0,1);
		}
	});
	
	
	var renderloop = function() {
		scene.render();
	};
	engine.runRenderLoop(renderloop);
}
else {
	alert("WebGL not supported!");
}