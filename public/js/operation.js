var oFrame = window.top;
var weburl="index.php?app=webgame&controller=";
function mouseover(item)
{
	item.style.borderColor = "#003399";
	item.style.background = "#CCCCFF";
}
function mouseout(item)
{
	item.style.borderColor = "#CCCCCC";
	item.style.background = "#CCCCCC";
}
/* 从主内容窗口显示 */
function goWork(url)
{
	oFrame.panelWork.location = url;
}
/* 从菜单树窗口显示 */
function goMenu(url)
{
	oFrame.panelMenu.location = url;
}
function goWorkPublish(url)
{
	oFrame.panelWork.mainFrame.location = url;
}
/* 生成 iframe */
function createIframe(id,url,width,height)
{
	if(width==null) width = 100;
	if(height==null) height = 100;
	return "<iframe id=\""+id+"\" name=\""+id+"\" src=\"" + url + "\" scrolling=\"no\" frameBorder=\"0\" width=\"" + width + "\" height=\"" + height + "\"></iframe>";
}
function move_next()
{
	history.forward(1);
}
function move_previous()
{
	history.back(1);
}
function freshmain()
{
	oFrame.panelWork.mainFrame.location = oFrame.panelWork.mainFrame.location;
}

function CheckAllObj(obj) 
{
	var chk = obj.checked;
	var CheckObject = parent.mainFrame.document.getElementsByName('dataId[]');
	for (i=0;i<CheckObject.length;i++) {
		if(CheckObject[i].checked != chk){
			CheckObject[i].click();
		}
	}
}

//提交表单
function dataFormSubmit(doType)
{
	var cIdObject = parent.mainFrame.document.getElementsByName('dataId[]');
	var isSelect;
	for(var i = 0;i < cIdObject.length ;i++){
		if(isSelect = cIdObject[i].checked){
			break;
		}
	}
	if(!isSelect){
		alert('请选择要进行操作的内容！');
		return false;
	}
	parent.mainFrame.$('do').value = doType;
	switch(doType){
		case 'edit':
			for(var i = 0;i < cIdObject.length ;i++){
				if(cIdObject[i].checked){
					doing('building&action=edit&id='+cIdObject[i].value,'edit');
				}
			}
		break;
		case 'del':
			if(confirm("确定 永久删除 此内容吗？\n请注意内容进行 永久删除 后，将不能恢复！"))
			{
				parent.mainFrame.$('dataForm').request({onSuccess:function(http){http.responseText ? alert(http.responseText) : parent.mainFrame.location = parent.mainFrame.location;$('checkAll').checked = false;}});
			}
		break;	
		default:
		 break;
	}
}
function doing(operation,type)
{
	 switch(type){
	 	case 'del':
	 	  if(confirm("确定删除吗？")){
			new Ajax.Request(weburl+operation, {method: 'post', onSuccess:freshmain});
		  }
	 	 break;
	    default:
	      goWork(weburl+ operation);
	     break; 	
	 }
	 
}
