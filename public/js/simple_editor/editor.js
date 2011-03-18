var gSetColorType = ""; 
var gIsIE = document.all; 
var gIEVer = fGetIEVer();
var gLoaded = false;
var ev = null;
var gSigns = "";
var gSignHtml = "";
/**
 * 获取event对象
 */
function fGetEv(e){
	ev = e;
}
/**
 * 获取IE版本
*/
function fGetIEVer(){
	var iVerNo = 0;
	var sVer = navigator.userAgent;
	if(sVer.indexOf("MSIE")>-1){
		var sVerNo = sVer.split(";")[1];
		sVerNo = sVerNo.replace("MSIE","");
		iVerNo = parseFloat(sVerNo);
	}
	return iVerNo;
}
/**
 * 设置可编辑
 */
function fSetEditable(){
	var f = $("HtmlEditor").contentWindow;
	f.document.designMode="on";
	if(!gIsIE)
		f.document.execCommand("useCSS",false, true);
}
/**
 * 设置编辑区域的事件
*/
function fSetFrmClick(){
	var f = $("HtmlEditor").contentWindow.document;
	f.onclick = function(){
		fHideMenu();
	}
	f.onkeydown = function(){
		parent.gIsEdited = true;
	}
}
/**
 * 设置onload事件
*/
var oURL = location.href;
var offset = oURL.lastIndexOf("id=");
if (offset == -1)
{
	alert("请传入调用参数ID，即隐藏的内容表单项ID！");
} else {
	offset = offset + 3
}
var sLinkFieldName = oURL.substring(offset)
var oLinkField = parent.document.getElementById(sLinkFieldName);
window.onload = function(){
config();
}
function config(){
	try{
		HtmlEditor=$("HtmlEditor").contentWindow;
		HtmlEditor.document.body.contentEditable="true";
		HtmlEditor.focus();

		HtmlEditor.document.body.innerHTML = oLinkField.value;
		$("Editcc").value=oLinkField.value;
		$("HtmlEditor").contentWindow.document.onkeyup=function()
		{
			oLinkField.value=HtmlEditor.document.body.innerHTML;
		}
		$("HtmlEditor").contentWindow.document.onkeypress=function(){
			if(HtmlEditor.event.keyCode==13)
			{
				var txtobj=HtmlEditor.document.selection.createRange()
				txtobj.text==""?txtobj.text="\n":(HtmlEditor.document.selection.clear())&(txtobj.text="\n")
				HtmlEditor.document.selection.createRange().select()
				return false
			}
			oLinkField.value=HtmlEditor.document.body.innerHTML;
		}
	}
	catch(e)
	{
		alert(e.message);
	}
}
function fSetHtmlContent(html){
	if (html)
	{
		var f = $("HtmlEditor").contentWindow.document;
		f.open();
		f.write(oLinkField.value);
		f.close();
	}
}
/**
 * 设置文字颜色
 */
function fSetColor(){
	var dvForeColor =$("dvForeColor");
	if(dvForeColor.getElementsByTagName("TABLE").length == 1){
		dvForeColor.innerHTML = drawCube() + dvForeColor.innerHTML;
	}
}
/**
 * 设置mousemove事件
*/
document.onmousemove = function(e){
	if(gIsIE) var el = event.srcElement;
	else var el = e.target;
	var tdView = $("tdView");
	var tdColorCode = $("tdColorCode");

	if(el.tagName == "IMG"){
		try{
			if(fInObj(el, "dvForeColor")){
				tdView.bgColor = el.parentNode.bgColor;
				tdColorCode.innerHTML = el.parentNode.bgColor
			}
		}catch(e){}
	}
}
/**
 * 判断el对象是否在另一个节点里
*/
function fInObj(el, id){
	if(el){
		if(el.id == id){
			return true;
		}else{
			if(el.parentNode){
				return fInObj(el.parentNode, id);
			}else{
				return false;
			}
		}
	}
}
/**
 * 显示对象
 */
function fDisplayObj(id){
	var o = $(id);
	if(o) o.style.display = "";
}
/**
 * 设置onclick事件
 */
document.onclick = function(e){
	if(gIsIE) var el = event.srcElement;
	else var el = e.target;
	var dvForeColor =$("dvForeColor");
	var dvPortrait =$("dvPortrait");

	if(el.tagName == "IMG"){
		try{
			if(fInObj(el, "dvForeColor")){
				format(gSetColorType, el.parentNode.bgColor);
				dvForeColor.style.display = "none";
				return;
			}
		}catch(e){}
		try{
			if(fInObj(el, "dvPortrait")){
				format("InsertImage", el.src);
				dvPortrait.style.display = "none";
				return;
			}
		}catch(e){}
	}
	fHideMenu();
	var hideId = "";
	if(el.id == "aGetSysSignList"){
		hideId = "dvSignList";
		parent.$(hideId).style.display = "";
	}else if(el.id == "imgStationery"){
		try{
			parent.LetterPaper.control(window, "show");
		}catch(exp){}
	}else if(arrMatch[el.id]){
		hideId = arrMatch[el.id];
		fDisplayObj(hideId);
	}
}
var arrMatch = {
	imgFontface:"fontface",
	imgFontsize:"fontsize",
	imgFontColor:"dvForeColor",
	imgBackColor:"dvForeColor",
	imgFace:"dvPortrait",
	imgAlign:"divAlign",
	imgList:"divList",
	imgSign:"dvSign",
	aGetSysSignList:"dvSignList",
	imgInOut:"divInOut"
}
/**
 * 执行格式化显示
 */
function format(type, para){
	var f = $("HtmlEditor").contentWindow;
	var sAlert = "";
	if(!gIsIE){
		switch(type){
			case "Cut":
				sAlert = "您的浏览器安全设置不允许编辑器自动执行剪切操作,请使用键盘快捷键(Ctrl+X)来完成";
				break;
			case "Copy":
				sAlert = "您的浏览器安全设置不允许编辑器自动执行拷贝操作,请使用键盘快捷键(Ctrl+C)来完成";
				break;
			case "Paste":
				sAlert = "您的浏览器安全设置不允许编辑器自动执行粘贴操作,请使用键盘快捷键(Ctrl+V)来完成";
				break;
		}
	}
	if(sAlert != ""){
		alert(sAlert);
		return;
	}
	f.focus();
	if(!para){
		if(gIsIE){
			f.document.execCommand(type);
		}else{
			f.document.execCommand(type,false,false);
		}
	}else{
		f.document.execCommand(type,false,para);
	}
	f.focus();
	oLinkField.value=HtmlEditor.document.body.innerHTML;
}
/*-----设置字体名称和大小------*/
function fontname(obj){
	format('fontname',obj.innerHTML);
	obj.parentNode.style.display='none';
}
function fontsize(size,obj){
	format('fontsize',size);
	obj.parentNode.style.display='none';
}
/**
 * 设置字体颜色
 */
function foreColor(e) {
	fDisplayColorBoard(e);
	gSetColorType = "foreColor";
}
/**
 * 设置背景色
 */
function backColor(e){
	var sColor = fDisplayColorBoard(e);
	if(gIsIE)
		gSetColorType = "backcolor";
	else
		gSetColorType = "backcolor";
}
/**
 * 显示颜色拾取器
 */
function fDisplayColorBoard(e){

	if(gIsIE){
		var e = window.event;
	}
	if(gIEVer<=5.01 && gIsIE){
		var arr = showModalDialog("help.htm", "", "font-family:Verdana; font-size:12; status:no; dialogWidth:21em; dialogHeight:21em");
		if (arr != null) return arr;
		return;
	}
	var dvForeColor =$("dvForeColor");
	// fSetColor();
	var iX = e.clientX;
	var iY = e.clientY;
	dvForeColor.style.display = "";
	dvForeColor.style.left = (iX-30) + "px";
	dvForeColor.style.top = 33 + "px";
	// EV.stopEvent();
	return true;
}
/**
 * 创建链接
 */
function createLink() {
	var sURL=window.prompt("请输入链接 (如:http://www.chif.net.cn/):", "http://www.chif.net.cn/");
	if ((sURL!=null) && (sURL!="http://")){
		format("CreateLink", sURL);
	}
}
/**
 * 创建图片
 */
function createImg()	{
	var sPhoto=prompt("请输入图片位置:", "http://a/download/b.JPG");
	if ((sPhoto!=null) && (sPhoto!="http://")){
		format("InsertImage", sPhoto);
	}
}

/**
 * 删除字符串两边空格
 */
String.prototype.trim = function(){
	return this.replace(/(^\s*)|(\s*$)/g, "");
}
/**
 * 鼠标移上图标
 */
function fSetBorderMouseOver(obj) {
	obj.style.borderRight="1px solid #aaa";
	obj.style.borderBottom="1px solid #aaa";
	obj.style.borderTop="1px solid #fff";
	obj.style.borderLeft="1px solid #fff";
} 
/**
 * 鼠标移出图标
 */
function fSetBorderMouseOut(obj) {
	obj.style.border="none";
}
/**
 * 鼠标按下图标
 */
function fSetBorderMouseDown(obj) {
	obj.style.borderRight="1px #F3F8FC solid";
	obj.style.borderBottom="1px #F3F8FC solid";
	obj.style.borderTop="1px #cccccc solid";
	obj.style.borderLeft="1px #cccccc solid";
}
/**
 * 显示下拉菜单
 */
function fDisplayElement(element,displayValue) {
	fHideMenu();
	if(element == "sign"){
		var dvSign = $("dvSign");
		if(dvSign){
			dvSign.style.display = "none";
			dvSign.parentNode.removeChild(dvSign);
		}
		var div = document.createElement("DIV");
		div.id = "dvSign";
		div.style.position = "absolute";
		div.innerHTML = '<div style="padding:1px;background:#FFFFFF; border:1px solid #838383; width:100px;"><span></span><div style="height:1px; font-size:1px; border-bottom:1px dotted #999; margin-bottom:1px"></div><span></span></div>';
		var signList = parent.GE.signs;
		var html = "";
		for(var i=0;i<signList.length;i++){
			var a = document.createElement("A");
			a.className = "n";
			a.innerHTML = signList[i].name;
			a.style.cursor = document.all?"hand":"pointer";
			eval('a.onclick= function(){fHideMenu();signList['+ i +'].callback("'+ signList[i].value +'");}');
			if(i == signList.length-1 || i == signList.length-2){
				div.firstChild.lastChild.appendChild(a);
				if(i == signList.length-2){
					a.onclick = fGetSignList;
					a.id = "aGetSysSignList";
				}
			}else{
				div.firstChild.firstChild.appendChild(a);
			}
		}
		document.body.appendChild(div);
		element = "dvSign";
	}
	if ( typeof element == "string" )
		element = $(element);
	if (element == null) return;
	element.style.display = displayValue;
	if(gIsIE){
		var e = event;
		var target = e.srcElement;
	}else{
		var e = ev;
		var target = e.target;
	}
	var iX = f_GetX(target);
	element.style.display = "";
	element.style.left = (iX) + "px";
	element.style.top = 33 + "px";
	// EV.stopEvent();
	return true;
}
/**
 * 获取对象的x坐标
 */
function f_GetX(e)
{
	var l=e.offsetLeft;
	while(e=e.offsetParent){				
		l+=e.offsetLeft;
	}
	return l;
}
/**
 * 获取对象的y坐标
 */
function f_GetY(e)
{
	var t=e.offsetTop;
	while(e=e.offsetParent){
		t+=e.offsetTop;
	}
	return t;
}
/**
 * 隐藏下拉菜单
 */
function fHideMenu(){
	try{
		var arr = ["fontface", "fontsize", "dvForeColor", "dvPortrait", "divAlign", "divList", "dvSign" ,"divInOut" ];
		for(var i=0;i<arr.length;i++){
			var obj = $(arr[i]);
			if(obj){
				obj.style.display = "none";
			}
		}
		try{
			parent.LetterPaper.control(window, "hide");
		}catch(exp){}
		var dvSignList = parent.$("dvSignList");
		if(dvSignList) dvSignList.style.display = "none";
	}catch(exp){}
}
/**
 * 获取对象
 */
function $(id){
	return document.getElementById(id);
}
/**
 * 隐藏对象
 */
function fHide(obj){
	obj.style.display="none";
}
gIsHtml = true;
/**
 * 删除标签符号
 */
String.prototype.stripTags = function(){
    return this.replace(/<\/?[^>]+>/gi, '');
};