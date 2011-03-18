 
//鼠标移动高亮
var rowMarked = Array();
var rowOverColor;
var rowMarkedColor;

function bb2(obj1, obj2,relateDir) {
	relateDir=relateDir.replace("$$$","/");
	if (obj1.style.display == "none") {
		obj1.style.display = "block";
		obj2.src =relateDir+ "images/menu_open.gif";
	} else {
		obj1.style.display = "none";
		obj2.src = relateDir+ "images/menu_close.gif";
	}
}

var kk = 0;

function viewall(count,var_name,relateDir) {
	relateDir=relateDir.replace("$$$","/"); 
	var imgb = "imgb";
	var	imgb1 = "imgb1";
	if (kk == 0) {
		kk = 1;
		for (i = 1; i <= count; i++) {
			temptr = var_name+"_hide" + i;
			tempimg = var_name+"_img" + i;
			 
			document.getElementById( temptr).style.display = "block";
			document.getElementById( tempimg).src =relateDir+ "images/menu_open.gif";
		} 
		document.getElementById(imgb).src =relateDir+ "images/openb.gif";
		//document.getElementById( imgb1).src =relateDir+ "images/openb.gif";

	} else {
		kk = 0;
		for (i = 1; i <= count; i++) {
			temptr = var_name+"_hide" + i;
			tempimg = var_name+"_img" + i;
			document.getElementById( temptr).style.display = "none";
			document.getElementById( tempimg).src =relateDir+ "images/menu_close.gif";
		} 
		document.getElementById( imgb).src =relateDir+ "images/closeb.gif";
		// document.getElementById( imgb1).src = "images/closeb.gif";

	}

}  
