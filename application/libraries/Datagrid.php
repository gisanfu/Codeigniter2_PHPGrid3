<?php

/**
 *
 * 该类能够生成具有表格列表,搜索，分页，排序，编辑，删除，新增的功能
 *
 *  
 *
 *@example
 *$test=new Datagrid();
 *$test->sql="SELECT  *  FROM  tb_user ";
 *$test->title="用户列表";
 * echo $html=  $test->display();
 *
 *
 *
 */

require_once("Dataview.php");

/**
 * 通过SQL生成带查询，编辑，删除功能的 表格 
 * 为了能够实现以上功能需要扩展Dataview类,在Dataview的基础上重写和扩充了部分方法
 * @author cdwei
 *
 */
class Datagrid extends Dataview {
    
    /**
	 * 数据库连接对象
	*/
	//public  $db = null;
	/**
     *为Datagrid的实例创建一个唯一的名称，在显示多个Datagrid实例时有用
	 */
	public $this_var_name = "";
	/**
     *每个实例的创建时间
	 */
	private $create_time = 0;
	/**
     *该Datagrid实例拥有的权限，v,u,a,d分表表示具有查看,更新,添加,删除的权限
	 */
	public $role = "v,u,a,d";

	/** 
	 * 最终生成的HTML由下面的$title,$searchHtml,$tableTitleHtml,
	 * $tableHeaderHtml,$tableBodyHtml,$tableFooterHtml,$footerHtml属性组成
	 * 
	 */
	
	/**
	 * 最后生成Datagrid HTML中的标题
	 */
	public $title = "";
	
	/*
	 * 最后生成Datagrid HTML中的表格搜索部分
	 */
	public $searchHtml		= "";
	
	/*
	 * 最后生成Datagrid HTML中的表格标题
	 */
	public $tableTitleHtml	= "";
	
	/*
	 * 最后生成Datagrid HTML中的表格头部,如： 
	 */
	public $tableHeaderHtml	= "";
	
	/*
	 * 最后生成Datagrid HTML中的表格主题部分
	 */
	public $tableBodyHtml	= "";
	
	/*
	 * 最后生成Datagrid HTML中的表格底部部分
	 */
	public $tableFooterHtml	= "";
	
	/*
	 * 最后生成Datagrid HTML中底部部分
	 */
	public $footerHtml		= "";
	public $tableHtml		= "";
	
	//分页属性 
	
	/*
	 * 每页显示几行
	 */
	public $pageRow		= 10;
	
	/*
	 * 当前分页的第几页
	 */
	public $page		= 1;
	
	/*
	 * 总共分页数
	 */	
	public $pageCount	= 1;
	
	/*
	 * 总共有几行
	 */	
	public $rowCount	= 0;


	//显示设置  
	/*
	 * 是否显示表标题的HTML
	 */
	public $displaySearch		=true;	
	
	/*
	 * 是否显示表标题的HTML
	 */
	public $displayTableTitle	=true;	
	
	/*
	 * 是否显示表头的HTML
	 */
	public $displayTableHeader	=true;	
	
	/*
	 * 是否显示表内容的HTML
	 */
	public $displayTableBody	=true;	
	
	/*
	 * 是否显示表底部的HTML
	 */
	public $displayTableFooter	=true;	
	
	/*
	 * 是否显示底部的HTML
	 */
	public $displayFooter		=true;	
	
	/*
	 * 是否显示操作
	 */
	public $displayControl		=true;	
	
	/*
	 * 是否可以排序
	 */
	public $displayOrder		=true;	
	
	/*
	 * 显示隐藏列
	 */
	public $displayHideField	=false;	

	//数据设置
	/*
	 * Datagrid从数据库中得到数据,这里可以不设置
	 */
	public $rows=array();
	
	/*
	 * 通过设置该值，取得特定数据
	 */
	public $sql="";
	
	/*
	 * SQL中主键字段,如果为空Datagrid自动取得主键值	
	 */
	public $key="";	 
	
	/*
	 * SQL查询中表名称，可以是单表，多表，或者连接表，如果已经设置前面sql，这里可以不需要设置
	 */	
	public $tables=""; 
	
	/*
	 * 设置查询的条件，可以动态的构建条件，用于取得你所需要数据
	 */
	public $where="";
	
	/*
	 * 要取得$tables字段格式类似如下：array(’id’,’name’,’pic as picture’),可以使用as 别名
	 */
	public $fields				=array();
	
	/*
	 * 指定$fields中要显示的字段,格式如下：array(’name’=>’名称’,’ picture’=>’照片’)，
	 * 数组键值为字段名，而数组值为显示的字符串
	 */
	public $fieldDisplays		=array(); 
	
	public $fieldStyles			=array();
	
	/*
	 * 设置隐藏列值的字段，如果显示设置允许显示，该项才会生效
	 */
	public $hideField			="";

	/*
	 * 设置查询的字段，格式类似如：array(’name’=>’名称’,’ picture’=>’照片’),
	 * 与$fieldDisplays 相似
	 */
	public $searchFields		=array();

	//表单设定
	/*
	 * 设置表单对应的表名,只可以设置一个表，如果你要使用表单，必须给该项设置值
	 */
	public $formTable			="";
	
	/*
	 * 设置表单的项，这些表单项为$formTable中的字段，格式如下：array(’name’=>’名称’,’ picture’=>’照片’),
	 * 与$fieldDisplays 相似
	 */
	public $formFields			=array();

	/*
	 * 为表单项指定类型，改项目是可选的，可以指定：label,input,date,text,textarea,select,list,hidden,simple_editor,fckeditor.格式如下：
	 * array(’name’=>’text’,
	 * 	’pic’=>’file’,
	 * 	’memo’=>’simple_editor’,
	 * 	’sex’=>array(’select’,array(1,2)
	 * 	); 
	 */
	public $formTypes			=array();
	
	/*
	 * 指定$formTable中的主键，如果你要使用表单，必须给该项设置值
	 */
	public $formPrimkey			="";
	
	/*
	 * 指定表单项中对应的默认值，格式如下array(’name’=>’请输入姓名’);
	 */
	public $formDefaults		=array();
	
	/*
	 * 设置表单项中的js验证，使用了livevalidation1.3 javascript库，
	 * 格式如：array("name"=>"Presence","user_email"=>"Email","cus_phone"=>"Numericality, {minimum: 11}"), 
	 * 关于livevalidation1的详细使用可以访问官方网站文档：http://livevalidation.com/
	 */
	public $formFieldsValidates	=array(); 
	
	/*
	 * 表单将会显示几列
	 */
	public $formColsNum			=2;
	
	/*
	 * 自定义表单项中的HTML属性,格式举例：
	 * array("name"=>'style="background:#FFFF00" ',"user_email"=>'readonly="true" style="background:#CCCCCC"'),
	 * 数组的键值为表单项的名称，数组元素值为其属性
	 */
	public $formAttributes		=array();
	
	//文件上传设定
	/*
	 * 文件上传的默认存放路径，你可以更改此路径
	 */
	public $formUploadDir		="uploadfiles/";
	
	/*
	 * 允许上传文件的格式
	 */
	public $formUploadFiloeType = array("jpg","gif","png","pdf","rar","zip","doc","xls");
	
	/*
	 * 文件上传的大小限制
	 */
	public $formUploadMaxSize	= 1048576;

	private $newFormHtml		= "";


	//私有方法，外部不能直接访问和设置这些属性
	private $fieldInfo			=array();
	private $tableNames			=array();
	private $fieldKeys			=array();
	private $fieldComments		=array();
	private $fieldTypes			=array();
	private $noSetDisplayFlag	=false;

	// gisanfu define
	private $_table_classname = 'tb3';
 
	/**
	 *生成查询的SQL语句
	 */
	public function makeSearchSql() {
		$sql="";
		//查询计算
		if(!isset($_POST['datagrid_action'])) return '';
		$action=$_POST['datagrid_action'];
		if($action!='search') return '';
		$exp_arr=array('LIKE','LIKE %...%','NOT LIKE','=','!=','IS NULL','IS NOT NULL');
		if(empty($this->searchFields)) return '';
		foreach ($this->searchFields as $searchField => $searchName) {
			if(!isset($_POST[$searchField.'_exp']) || !isset($_POST[$searchField.'_value'])) continue;
			$exp=$_POST[$searchField.'_exp'];
			$value=strval($_POST[$searchField.'_value']);
			if(in_array($exp,$exp_arr) && !empty($value)){
				if($exp=="=" || $exp=="!=" || $exp=="LIKE" || $exp==">=" || $exp=="<=") $sql.=" `$searchField` ".$exp."  '".$value."' and";
				if($exp=="LIKE %...%") 		$sql.=" `$searchField` LIKE  '%".$value."%' and";
				if($exp=="IS NULL")   		$sql.=" `$searchField` IS NULL and";
				if($exp=="IS NOT NULL")  	$sql.=" `$searchField` IS NOT NULL and";
			}
		}
		$sql=substr($sql,0,-3);//去掉最后的and

		return $sql;
	}

	/**
	 * 构造SQL语句
	 * @return string
	 */
	public function makeSql($makeCountSql = false) {
		$var_name=$this->this_var_name;
		$sql=strtolower($this->sql);

		if(empty($sql)){
			if(empty($this->fields) || empty($this->tables)) show_error($this->lang->line('error_empty_table_fields'));
			$sql='Select '.implode(',',$this->fields);
			if(!empty($this->hideField)){
				$sql=$sql.",".$this->hideField." ";
			}
			$sql=strtolower($sql);
			if(!empty($this->formPrimkey)){
				if(!in_array($this->formPrimkey,$this->fields)){
					$sql=$sql.",".$this->formPrimkey." ";
				}
			}
				
			if(stristr($this->tables,'from')){
				$sql.=' '.$this->tables;
			}else{
				$sql.=' From '.$this->tables;
			}
			if(stristr($this->where,'where')){
				$sql.=' '.$this->where;
			}else{
				if(!empty($this->where)) $sql.=' Where '.$this->where;
			}
		}

		//取得查询后的SQL
		$searchSql=$this->makeSearchSql();
		if($searchSql!='') {
			if (strstr($sql,'where')) {
				list($temp1,$temp2)=explode("where",$sql);
				$sql=$temp1.' where  '.$searchSql.' and '.$temp2;
			}else{
				$sql.=' where '.$searchSql.' ';
			}
		}
		if($makeCountSql==true){
			list(,$temp3)=explode('from',$sql);
			$sql='Select count(*) as row_count from '.$temp3;
			$sql2=preg_replace("/limit(.*?)$/i","",$sql);
			return $sql2;
		}

		//排序计算
		$order="desc";
		if(isset($this->get[$var_name.'_order'])){
			$order_field = $this->get[$var_name.'_order'];
			/*
			if(isset($_SESSION[$var_name.'_order'])){
				if($_SESSION[$var_name.'_order']=="desc") {
					$order="asc";
					$_SESSION[$var_name.'_order']="asc";
				} else{
					$order="desc";
					$_SESSION[$var_name.'_order']="desc";
				}
			}else{
				$_SESSION[$var_name.'_order']="desc";
			}
			*/
			//if(isset($this->session->userdata($var_name.'_datagrid_order')){
				if($this->session->userdata($var_name.'_datagrid_order') == 'desc'){
					$order = 'asc';
					$this->session->set_userdata($var_name.'_datagrid_order', 'asc');
				} else {
					$order = 'desc';
					$this->session->set_userdata($var_name.'_datagrid_order', 'desc');
				}
			//} else {
			//	$this->session->set_userdata($var_name.'_datagrid_order', 'desc');
			//}

			if (preg_match('/order\s*by(.*?)desc|asc/i', $sql)) {
				$result = preg_replace('/order\s*by(.*?)desc|asc/i', 'order by `'.$order_field.'` '.$order.' ', $sql);
				$sql=$result[0];
			} else {
				if(preg_match("/limit(.*?)$/i",$sql)){
					$test=explode("limit ",$sql);
					list($cut1,$cut2)=$test;
					$sql=$cut1.' order by `'.$order_field.'` '.$order.' limit '.$cut2;
				}else{
					$sql.=' order by `'.$order_field.'` '.$order.' ';
				}
			}

		}
		if($this->pageRow==0){
			return $sql;
		}
		if(preg_match("/limit(.*?)$/i",$sql)) return $sql;
		
		//分页计算
		if(isset($this->get[$var_name.'_page'])){
			$page = intval($this->get[$var_name.'_page']);
			if($page<=0) $page=1;
			$this->page=$page;
			$pageRow=$this->pageRow;
			$sql.=' limit '.strval(($page-1)*$pageRow).','.strval($pageRow);
		}else{
			if(!preg_match("/limit(.*?)$/i",$sql)){ ;
			$pageRow=$this->pageRow;
			$sql.=' limit 0,'.strval($pageRow);
			}
		}
		//echo $sql;
		//file_put_contents("sql.txt",$sql.'');

		return $sql;
	}




	/**
	 *生成搜索的HTML
	 */
	public function setSearchHtml() {
		if(empty($this->searchFields)) return false;
		$count=count($this->searchFields);
		$numCos=2;
		if($count>9) $numCos=3;
		$numTR=round($count/$numCos);
		$searchFields=array_keys($this->searchFields);
		$searchValues=array_values($this->searchFields);
		$var_name=$this->this_var_name;
		$this->searchHtml='
		 
			<form action="" method="post" name="'.$var_name.'_search_form" id="'.$var_name.'_search_form">
			<input type="hidden" name="datagrid_action" value="search" />
			<input type="hidden" name="datagrid_page" value="'.$this->page.'" />
			<input type="hidden" name="var_name" value="'.$var_name.'" /> 
			
			<table class="'.$this->_table_classname.'" id="'.$var_name.'_form_table">
		           <tr>
			        <td  colspan="'.strval($numCos*3).'"  ><span  >'.$this->lang->line('search_title').'</span></td>
			      </tr> 
			      ';
		$num=0;
		for($i=0;$i<$numTR;$i++){
			$this->searchHtml.=' <tr >';
			for($j=0;$j<$numCos;$j++){
				$temp_display="";
				$temp_field="";
				$opt='';
				$input='';
				if(isset($searchValues[$num])){
					$temp_display=$searchValues[$num];
				// 底下兩行是修它的Bug
				} else {
					continue;
				}
				if(isset($searchFields[$num])){
					$temp_field=$searchFields[$num];
					$input='<input type="text" name="'.$temp_field.'_value" size="25" class="textfield" id="'.$searchFields[$num].'_value" />';
					$opt='<select name="'.$searchFields[$num].'_exp">
			                <option value="=">=</option>
			                <option value="!=">!=</option>
			                <option value="LIKE">LIKE</option>
			                <option value="LIKE %...%">LIKE %...%</option>
			                <option value="NOT LIKE">NOT LIKE</option> 
			                <option value="IS NULL">IS NULL</option>
			                <option value="IS NOT NULL">IS NOT NULL</option>
			              </select>';
				}
				$this->searchHtml.='
			            <td  >'.$searchValues[$num].'</td>
			            <td >'.$opt.'</td>
			            <td  >'.$input.'</td>
						';
				$num++;
			}
			$this->searchHtml.='</tr> ';
		}
		$this->searchHtml.='
				 
		   <tr >
			        <td colspan="'.strval($numCos*3).'">
			          <div align="center">
  					<input type="submit" name="Submit" value="'.$this->lang->line('form_submit').'" />
 					 &nbsp;&nbsp;<input type="reset" name="Submit2" value="'.$this->lang->line('form_reset').'" />
			          </div></td>
		          </tr> </table>	
	  <br>
   </form> 
		';
	}



	/**
	 * 生成表格的底部HTML，包含分页
	 * @return none
	 */
	function setTableFooterHtml() {
		if(!$this->displayTableFooter) return false;
		$var_name=$this->this_var_name;
		$this->tableFooterHtml.='
		<tfoot id="'.$var_name.'_tfoot">
			<tr id="'.$var_name.'_tfoot_tr">
				<td id="'.$var_name.'_tfoot_td"   colspan="'.strval(count($this->fields)+2).'"   >'.$this->lang->line('total').'：'.$this->rowCount.' &nbsp;'.$this->getPagesInfo().'</td>
		 	 </tr>
	 	</tfoot>'."\n";
	}






	/**
	 *通过传入字段的类型，计算出该字段占据列的长度百分比
	 */
	private function getFormFieldLenght($type) {
		$len=20;
		if(strstr($type,'varchar') or strstr($type,'char')){
			$num=$this->getInLenght($type);
			if($num<20) $len=10;
			if($num>=100) $len=100;
		}
		if(strstr($type,'text') or strstr($type,'blob') ){
			$len=100;//
		}
		return $len;
	}

	/**
	 * 根据字段类型判断表单字段的类型
	 */
	private function getFormFieldType($type) {
		$re="text";
		if(strstr($type,'varchar') or strstr($type,'char')){
			$num=$this->getInLenght($type);
			if($num>=100) $re=array('text',"");//
		}
		if(strstr($type,'text') or strstr($type,'blob') ){
			$re=array('text',"");//
		}
		if(strstr($type,'set(') ){
			$type=str_replace(array("set(","'",")"),array("","",""),$type);
			$result=explode(',',$type);
			$re=array('list',array_combine($result,$result));//
		}
		if(strstr($type,'enum(') ){
			$type=str_replace(array("enum(","'",")"),array("","",""),$type);
			$result=explode(',',$type);
			$re=array('select',array_combine($result,$result));//
		}
		return $re;
	}





	/**
	 *新增加一条记录，如果成功则返回插入的ID，否则返回FALSE
	 */
	public function addRow() {
		if(!empty($_POST['var_name'])){
			if($_POST['var_name']!=$this->this_var_name) return false;//表单中隐藏域var_name的值和实例化的对象名不一致
		}
		if(empty($this->formTable)) return $this->lang->line('error_noset_formTable'); 
		$table=$this->formTable;
		list($formFields,$formTypes,$formPrimkey,$formDefaults)=$this->getTableInfo($table);
		if(empty($this->formFields)) $this->formFields=$formFields;
		if(empty($this->formTypes)) $this->formTypes=$formTypes;
		$formTypes=$this->formTypes;
		
		$this->formTable=str_replace("`","",$this->formTable);
		$sql="INSERT INTO `".$this->formTable."` SET ";
		$post_value="";

		foreach ($this->formFields as $field => $variable) {
			
		//如果是文件上传
			if(isset($_FILES[$field]) ){
				if($formTypes[$field]=="file" && !empty($_FILES[$field]['size']))  {
					list($flag,$msg)=$this->uploaderFILES($_FILES[$field]);
					if($flag==true){
						$sql.=" `$field`='".$msg."',";
						continue;
					}else{
						show_error($msg);
					}
				}
			}
			
			if(isset($_POST[$field]) ){
				//如果是多选列表,转换为字符串
				if(isset($formTypes[$field][0])){
					if($formTypes[$field][0]=="list"){
						if(is_array($_POST[$field])){
							$_POST[$field]=implode(',',$_POST[$field]);
						}
					}
				} 
				$post_value=htmlspecialchars_decode($_POST[$field]);
				$post_value=mysql_escape_string(stripslashes($post_value));
				$sql.=" $field='".$post_value."',";
			}
		}
		$sql=substr($sql,0,-1);

		//$flag=$this->db->query($sql);
		$this->db->query($sql);
		//if($flag>0) return $this->db->insertId();
		if($this->db->affected_rows() > 0){
			return $this->db->insert_id();
		}
		return false;


	}

	/**
	 *更新一条记录，如果成功则返回插入的ID，否则返回FALSE
	 */
	public function updateRow() {
		if(!empty($_POST['var_name'])){
			if($_POST['var_name']!=$this->this_var_name) return false;//表单中隐藏域var_name的值和实例化的对象名不一致
		}
		if(empty($this->formTable)) return $this->lang->line('error_noset_formTable');
		//if(empty($this->formFields)) return $GLOBALS['language']['error_noset_formTable']值!';
		$table=$this->formTable;
		list($formFields,$formTypes,$formPrimkey,$formDefaults)=$this->getTableInfo($table);
			
		if(empty($_POST[$formPrimkey])){
			show_error( $this->lang->line('error_noset_formPrimkey') );//"提交的主键字段{$formPrimkey}的值为空,操作不能完成!"
		}

		if(empty($this->formFields)) $this->formFields=$formFields;
		if(empty($this->formTypes)) $this->formTypes=$formTypes;
		$formTypes=$this->formTypes;

		$this->formTable=str_replace("`","",$this->formTable);
		$sql="UPDATE `".$this->formTable."` SET ";
		foreach ($this->formFields as $field => $variable) {
			$post_value="";
			//如果是文件上传
			if(isset($_FILES[$field]) ){
				if($formTypes[$field]=="file" && !empty($_FILES[$field]['size']))  {
					list($flag,$msg)=$this->uploaderFILES($_FILES[$field]);
					if($flag==true){
						$sql.=" `$field`='".$msg."',";
						continue;
					}else{
						show_error($msg);
					}
				}
			}
				
			//普通的HTTP_POST提交
			if(isset($_POST[$field]) ){
				//如果是多选列表,转换为字符串
				if(isset($formTypes[$field][0])){
					if($formTypes[$field][0]=="list"){
						if(is_array($_POST[$field])){
							$_POST[$field]=implode(',',$_POST[$field]);
						}
					}
				} 
				$post_value=htmlspecialchars_decode($_POST[$field]);
				$post_value=mysql_escape_string(stripslashes($post_value));
				$field=str_replace('`','',$field);
				$sql.=" `$field`='".$post_value."',";
			}
		}
		$sql=substr($sql,0,-1);
		$sql.=" WHERE $formPrimkey='".$_POST[$formPrimkey]."' limit 1";
		//echo $sql;
		//$flag=$this->db->query($sql);
		$this->db->query($sql);
		//if($flag>0) return true;
		if($this->db->affected_rows() > 0){
			return true;
		}
		return false;

	}

	/**
	 * 删除一条记录
	 */
	public function deleteRow() {
		if(empty($this->formTable)) return $this->lang->line('error_noset_formTable'); 
		$table=$this->formTable;
		list($formFields,$formTypes,$formPrimkey,$formDefaults)=$this->getTableInfo($table);
		if(empty($this->formFields)) $this->formFields=$formFields;
		$sql="DELETE FROM   $table ";
		$sql.=" WHERE $formPrimkey='".$this->get[$formPrimkey]."' limit 1";
		//$flag=$this->db->query($sql);
		$this->db->query($sql);
		//if($flag>0) return true;
		if($this->db->affected_rows() > 0){
			return true;
		}
		return false;
	} 
	
	/**
	 * 重写了 父类的方法，目的是要加载表单验证的JS
	 *生成头部的JS
	 */
	public function setHeaderJS() {
		
		//避免重复加载
		if($this->isloadjs == ''){
			$this->headerJS=' <!-- 后台JS代码及CSS --> 
			<script type="text/javascript" src="'.$this->relativeDir.'js/common.js"></script>   '."\n".'
			<script type="text/javascript" src="'.$this->relativeDir.'js/validation/livevalidation_standalone.js"></script>   '."\n".'
 			<link rel="stylesheet" type="text/css" href="'.$this->relativeDir.'js/validation/validation.css" /> '."\n".'
			'; 
			$this->isloadjs = 'load';
		}
	}
	
	/**
	 *生成新增表单
	 */
	public function setNewFormHtml() {
		return $this->makeFormHtml('new');
	}	

	/**
	 *生成编辑表单
	 */
	public function setEditFormHtml() {
		return $this->makeFormHtml('edit');
	}
	/**
	 *生成复制表单
	 */
	public function setCopyFormHtml() {
		return $this->makeFormHtml('copy');
	}
	
	
	
	/**
	 *生成查看表单
	 */
	public function setViewFormHtml() {
		return $this->makeFormHtml('view');
	}
	

	/**
	 *生成表单
	 */
	public  function makeFormHtml($flag="new") {
		if(empty($this->formTable)) return $this->lang->line('error_noset_formTable');
		//if(empty($this->formFields)) return '没有指定表单的字段,请为了Datagrid设置formFields值!';
		$table=$this->formTable;
		$var_name=$this->this_var_name;
		list($formFields,$formTypes,$formPrimkey,$formDefaults)=$this->getTableInfo($table);
		if(empty($this->formPrimkey)) $this->formPrimkey=$formPrimkey;
		//表单字段类型，如果不设置则依据数据库的字段类型来判断
		if(empty($this->formTypes)){
			$lens=array();
			$types=array();
			foreach ($formTypes as $key => $value) {
				$lens[$key]=$this->getFormFieldLenght($value);
				$types[$key]=$this->getFormFieldType($value);
			}
			$this->formTypes=$types;
		}



		//默认值
		if(empty($this->formDefaults)){
			$this->formDefaults=$formDefaults;
		}
		if(empty($this->formFields)) $this->formFields=$formFields;
		//var_export($this->formTypes);
		$html='';
		$formFields=$this->formFields;
		if($flag=="edit" || $flag=="view" || $flag=="copy" ){
			$formPrimkey=$this->formPrimkey;
			if(!isset($this->get[$formPrimkey])){
				show_error( $this->lang->line('error_noset_primkey') );
			}
			if($this->get[$formPrimkey]=="") show_error("{$formPrimkey},".$this->lang->line('error_empty_GET_primkey'));
			$formPrimkeyValue=mysql_escape_string($this->get[$formPrimkey]);
			//$this->formDefaults=$this->db->getRow(" select * from ".$table=$this->formTable." where $formPrimkey='$formPrimkeyValue' limit 1");
			$query = $this->db->query(' SELECT * FROM '.$table=$this->formTable." WHERE $formPrimkey='$formPrimkeyValue' LIMIT 1");
			$rrrooo = $query->result_array();
			$this->formDefaults = $rrrooo[0];
			unset($formFields[$formPrimkey]);
			unset($this->formFields[$formPrimkey]);
		}
		//if(empty($this->formFields)) return '没有指定表单的字段,请为了Datagrid设置formFields值!';
		$count=count($formFields);
		$numCos=$this->formColsNum;
		$numTR=round($count/$numCos);

		$formFields=array_keys($formFields);
		$formValues=array_values($this->formFields);
		$formTypes=$this->formTypes;
		//print_r($formValues);
		$html='
		 
			<form action="" method="post" enctype="multipart/form-data"  name="'.$var_name.'_form" id="'.$var_name.'_form">
			<input type="hidden" name="datagrid_action" value="'.$flag.'" /> 
			<input type="hidden" name="var_name" value="'.$var_name.'" /> 
			';
		$title=$this->lang->line('form_title_add');
		if($flag=="edit"){
			$title=$this->lang->line('form_title_edit');
			$html.='<input type="hidden" name="'.$formPrimkey.'" value="'.$formPrimkeyValue.'" />';
		}
		if($flag=="view") $title = $this->lang->line('form_title_view');
		if($flag=="copy") $title = $this->lang->line('form_title_copy');
		$html.='
			
			<table class="'.$this->_table_classname.'" id="formHtml" >
		           <tr>
			        <td colspan="'.strval($numCos*2).'"  ><span >'.$title.'</span></td>
			      </tr> 
			      ';
		$num=0;
		for($i=0;$i<$numTR;$i++){
			//$this->newFormHtml.=' <tr >';
			//$html.=' <tr >';
			for($j=0;$j<$numCos;$j++){
				$value="";
				$fieldHtml="";
				$display="";
				if(isset($formFields[$num])){
					$name=$formFields[$num];
					$type=$formTypes[$name];

					$display=$formValues[$num];
					if(isset($this->formDefaults[$name])) $value=$this->formDefaults[$name];
					$attribute="";
					if(isset($this->formAttributes[$name])) $attribute=$this->formAttributes[$name];
					
					if (!get_magic_quotes_gpc()) $value=addslashes($value);
					$value=htmlspecialchars($value);
					if($flag != "view"){
						$fieldHtml=$this->getFormField($name,$value,$attribute);
						$fieldHtml=$this->extendField($name,$fieldHtml);
					}else{
						$fieldHtml=htmlspecialchars_decode($value);
					}
				}
				$html.='<tr>
			           <td  >'.$display.'</td>
			            <td  >'.$fieldHtml.' </td></tr>
			            ';
				$num++;
			}
			//$html.='</tr>';
		}
		if($flag!="view"){
			$html.='<tr >
			        <td colspan="'.strval($numCos*2).'">
			          <div align="center">
  					<input type="submit" name="Submit" value="'.$this->lang->line('form_submit').'" />
 					 &nbsp;&nbsp;<input type="reset" name="Submit2" value="'.$this->lang->line('form_reset').'" />
			          </div></td>
		         </tr> ';
		}
		$html.='    </table>  <br>';
		if($flag!="view"){
			$html.=$this->setValidateJS();
		}
		$html.='</form>';
		return $html;

	}

	/**
	 *重写该方法，可以自定义表单的字段
	 */
	public function extendField($name,$fieldHtml) {
		return $fieldHtml;
	}
 

	/**
	 *生成可自动进行表单验证的js代码
	 *这些代码是livevalidation库的代码，你可以查看http://livevalidation.com/
	 *
	 *@see http://livevalidation.com/
	 *
	 */
	public function setValidateJS() {
		$validates=$this->formFieldsValidates;
		$formFields=array_keys($this->formFields);
		$js="\n<script type='text/javascript'>\n";
		foreach ($validates as $key => $value) {
			$temp="";
			if(!in_array($key,$formFields)) continue;
			if(!strstr($value,"Validate.")) $value="Validate.".$value;
			$js.='var '.$key.' = new LiveValidation( "'.$key.'", { validMessage: "OK", wait: 500 } );'."\n";
			if(is_array($value)){
				foreach ($value as  $vv) {
					$js.=$key.".add( ".$vv." );\n";
				}
			}else{
				$js.=$key.".add( ".$value." );\n";
			}
			$js.="\n";
		}
		$js.="</script>\n";
		return $js;
	}


	/**
	 *生成表单字段的HTML
	 *@param $name 表单名称 
	 *@param $value 字段值 
	 *@param $add_attribute 附加属性
	 */
	private function getFormField($name,$value,$add_attribute)
	{
			
		$typeValue=array();
		if(!is_array($this->formTypes[$name])){
			$formFieldType=$this->formTypes[$name];
		}else{
			$formFieldType=$this->formTypes[$name][0];
			if(isset($this->formTypes[$name][1])) $typeValue=$this->formTypes[$name][1];
		}
			
		
		 
		//label,input,date,text,textarea,select,list,hidden,simple_editor,fckeditor
		//表单是何种类型?
		switch($formFieldType) {
			case "label":
				return $value;
				break;
			case "textarea":
			case "memo":
				$html='<textarea id="'.$name.'" name="'.$name.'"  '.$add_attribute.'  cols="40" rows="3"   >'.$value.'</textarea>
                        ';
				return $html;
				break;
					
			case "select":
				$x='<select name="'.$name.'"   id="'.$name.'">';
				foreach ($typeValue as $key => $row) {
					if($key==$value){
						$x .= "<option value='".$key."'  selected >".$row."</option>"  ;
					}else{
						$x .= "<option value='".$key."'>".$row."</option>"  ;
					}
				}
				$x.='</select>';
				return $x ;
					
				break;

			case "list":
				$value_arr=explode(",",$value);
				$count=count($typeValue);
				$x='<select name="'.$name.'[]"   id="'.$name.'" size="'.$count.'" multiple="multiple">';
				foreach ($typeValue as $key => $row) {
					$flag=0;
					foreach ($value_arr as $v_key => $v) {
						if($key==$v){
							$flag=1;
							$x .= "<option value='".$key."'  selected >".$row."</option>"  ;
							unset($value_arr[$v_key]);
							break;
						}
					}
					if($flag==0) $x .= "<option value='".$key."'>".$row."</option>"  ;

				}
				$x.='</select>';
				return $x ;
					
				break;

			case "hidden":
				$time="";
				$html=' <input type="hidden" id="'.$name.'" name="'.$name.'"    value="'.$value.'"   /> ';
				return $html ;
				break;

			case "fckeditor": 
				$reDir=$this->relativeDir;
				include_once($reDir."js/fckeditor/fckeditor.php") ;
				$oFCKeditor = new FCKeditor($name) ;
				$oFCKeditor->BasePath = $reDir.'js/fckeditor/' ;
				$oFCKeditor->Value=htmlspecialchars_decode($value);
				return $oFCKeditor->Create();
				break;
			case "simple_editor":
				$reDir=$this->relativeDir;
				$html='<textarea id="'.$name.'" name="'.$name.'"   style="display:none;"   '.$add_attribute.'   >'.htmlspecialchars_decode($value).'</textarea>
								<iframe id="yy" src="'.$reDir.'js/simple_editor/editor.html?id='.$name.'" frameborder="0" scrolling="no" style="width:500px;height:300px;"></iframe>
                              ';
				return $html ;
				break;
				
			case "file":
				$html='文件路径：<a href="'.$this->relativeDir.$value.'"  target="_blank">'.$value.'</a><br>
						 <input name="'.$name.'" id="'.$name.'" type="file"   /> ';
				return $html ;
				break;
						
			case "input":
			case "date":
			case "text":
			default :
				$time="";
				$html='<input type="text" id="'.$name.'" name="'.$name.'"   value="'.$value.'"  '.$add_attribute.' />';
				return $html ;
				break;
		}

		return  $html;
	}


	/**
	 * 根据SQL语句生成数据
	 * @return string of html
	 */
	public function makeDataset() {
		//构造SQL语句
		$this->sql = $this->makeSql();
		//$rows=$this->db->getAll($this->sql);
		//echo $this->sql;
		$query = $this->db->query($this->sql);
		//`echo "$this->sql" /home/gisanfu/123.txt`;
		$rows = $query->result_array();
		//var_dump($rows);
		//$this->db->query("INSERT INTO  `tb_logs` (`lg_sql`, `lg_time`) VALUES ('".mysql_escape_string($this->sql)."', UNIX_TIMESTAMP());");
		//获得总数的SQL
		$count_sql=$this->makeSql(true);
		//echo $count_sql;
		//$this->rowCount=$this->db->getOne($count_sql);
		$query = $this->db->query($count_sql);
		$rowcount = $query->result();
		$this->rowCount = $rowcount[0]->row_count;
		//$this->db->query("INSERT INTO  `tb_logs` (`lg_sql`, `lg_time`) VALUES ('".mysql_escape_string($count_sql)."', UNIX_TIMESTAMP());");
		if(empty($this->fieldDisplays)) $this->noSetDisplayFlag=true;
		$this->makeFields();//如果没有设置字段列表则生成所有的
		$this->rows = $rows;
	}



	/**
	 * 返回最终的HTML
	 * 
	 * @return string of HTML
	 */
	function display() {

		//取得对象名，如$example=new Datagrid();中的example,目的是显示多个表格时进行区分
		foreach ($GLOBALS as $key => $value) {
			if ($value instanceof Datagrid) {
				if($value->create_time==$this->create_time){
					break;
				}
			}
		}
		$this->this_var_name=$key;
		$this->setHeaderJS();//首先生成头部的JS
		$this->role=strtolower($this->role);
		$roles=explode(",",$this->role);
			
		//处理新增、编辑
		if(isset($_POST['datagrid_action'])){
			$action=$_POST['datagrid_action'];
			if($action=="new" || $action=="copy")  {
				if(!in_array("a",$roles)) show_error($this->lang->line('no_permission'));
				$insertid=$this->addRow();
			}
			if($action=="edit") {
				if(!in_array("u",$roles)) show_error($this->lang->line('no_permission'));
				$this->updateRow();
			}
		}else{
			//显示新增编辑
			if(isset($this->get['datagrid_action'])){
				$action = $this->get['datagrid_action'];
				if($action=="new")  {
					if(!in_array("a",$roles)) show_error($this->lang->line('no_permission'));
					return  $this->headerJS.$this->setNewFormHtml();
				}
				if($action=="edit") {
					if(!in_array("u",$roles)) show_error($this->lang->line('no_permission'));
					return  $this->headerJS.$this->setEditFormHtml();
				}
				if($action=="copy") {
					if(!in_array("u",$roles)) show_error($this->lang->line('no_permission'));
					return  $this->headerJS.$this->setCopyFormHtml();
				}
				if($action=="view") {
					if(!in_array("v",$roles)) show_error($this->lang->line('no_permission'));
					return  $this->headerJS.$this->setViewFormHtml();
				}
				if($action=="delete")      {
					if(!in_array("d",$roles)) show_error($this->lang->line('no_permission'));
					$this->deleteRow();
				}
			}
		}
		//如果没数据集	
		if(empty($this->rows)) {
			$this->makeDataset();
		}else{
			$this->rowCount=count($this->rows);
		}
		
		$this->setSearchHtml();
		$this->setTableTitle();
		$this->setTableHeaderHtml();
		$this->setTableBodyHtml();
		$this->setTableFooterHtml();
		return $this->makeHtml();

	}

	/**
	 * 连接HTML
	 * @return string of HTML
	 */
	public function makeHtml() {
		$html=$this->headerJS;
		$html.=$this->searchHtml;
		$html.='<table class="'.$this->_table_classname.'" summary=" table of Datagrid"  >';
		$html.=$this->tableTitleHtml;
		$html.=$this->tableHeaderHtml;
		$html.=$this->tableBodyHtml;
		$html.= $this->tableFooterHtml;
		$html.='
		</table>';
		return $html;
	}



	/**
	 * 获得分页偏移量
	 * @return none
	 */
	function getPagesInfo($finally=true){
		$var_name=$this->this_var_name;
		$cur_page=$this->page;
		$pagesize=$this->pageRow;
		$numrows =$this->rowCount;
		$pages = intval($numrows / $pagesize);
		if ($numrows % $pagesize) $pages++;
		if (isset($this->get[$var_name.'_page'])) {		//设置 数
			$cur_page 			= intval($this->get[$var_name.'_page']);
			if( $cur_page<1) $cur_page = 1;
			if($cur_page>$pages) $cur_page=$pages;
		} else {
			$cur_page = 1;
		}

		$offset  = $pagesize * ($cur_page-1);//计算记录偏移量
		if($offset<0)  $offset=0;
		if($finally==true){
			return $this->getPageStr($pages,$cur_page,$var_name.'_page');
		}else{
			return array($offset,$pages,$cur_page);
		}
		//return array($offset,$pages,$page);

	}

	/**
	 * 分页公共函数
	 *
	 * @param int $pages 			总页数
	 * @param int $page				当前页数
	 * @param int $page_url_name	URL的参数名称
	 * @return    					返回分页的HTML		
	 */
	public function getPageStr($pages,$page,$page_url_name="datagrid_page"){

		$new_url=$_SERVER['REQUEST_URI'];
		$var_name=$this->this_var_name;
		if(preg_match('/'.$page_url_name.'=(.\d*)/i', $new_url)){
			$new_url = preg_replace('/&'.$page_url_name.'=(.\d*)/i', '', $new_url);
		}
		if(!strstr($new_url,'?')){
			$new_url.='?var_name='.$var_name;
		}else{
			$new_url.='&var_name='.$var_name;
		}
		$page_str="";
		if($pages>1){
			$next=$page+1;
			if($page==$pages) $next=$pages;
			$pre=$page-1;
			if($page==1) $pre=1;
			$page_str= ' ' ;
			$page_str.= ' <a  href="'.$new_url."&".$page_url_name.'='.intval(1).'" >'.$this->lang->line('page_first').'</a>&nbsp;<a  href="'.$new_url."&".$page_url_name.'='.intval($pre).'"   >'.$this->lang->line('page_pre').'</a>&nbsp;
			 '; 
			if($pages>9){
				if($page<=3){
					for($i=1;$i<$page;$i++){
						$page_str.=' <a href="'.$page_url_name.intval($i).'" >'.$i.'</a>';
					}
				}else{
					//$page_str.=' <a href="'.$new_url."&".$page_url_name.'='.intval($page-3).'" >'.intval($page-3).'</a>';
					$page_str.='<a href="'.$new_url."&".$page_url_name.'='.intval($page-2).'" >'.intval($page-2).'</a>&nbsp;';
					$page_str.='<a href="'.$new_url."&".$page_url_name.'='.intval($page-1).'" >'.intval($page-1).'</a>&nbsp;';

				}
				$page_str.='<a href="#"   >'.$page.'</a>';
				if(($pages-$page)>3){
					$page_str.='<a href="'.$new_url."&".$page_url_name.'='.intval($page+1).'" >'.intval($page+1).'</a>&nbsp;';
					$page_str.='<a href="'.$new_url."&".$page_url_name.'='.intval($page+2).'" >'.intval($page+2).'</a>&nbsp;';
					//$page_str.='<div class="page_num"><a href="'.$new_url."&".$page_url_name.'='.intval($page+3).'" >'.intval($page+3).'</a></div> ';
				}else{
					for($i=$page+1;$i<=($pages);$i++){
						$page_str.='<a href="'.$new_url."&".$page_url_name.'='.intval($i).'" >'.$i.'</a> ';
					}
				}
			}else{
				for($i=1;$i<=$pages;$i++){
					$page_str.='<a href="'.$new_url."&".$page_url_name.'='.intval($i).'" >'.$i.'</a>&nbsp;';

				}
			}

			$page_str.= '<a href="'.$new_url."&".$page_url_name.'='.intval($next).'"    >'.$this->lang->line('page_next').'</a>
			&nbsp;<a href="'.$new_url."&".$page_url_name.'='.intval($pages).'"   >'.$this->lang->line('page_end').'</a>&nbsp;&nbsp;';
			$page_str.='<a href="#"  >GOTO</a>&nbsp;<input  name="page_'.$page.'"  id="page_"   size="2"  type="text" />&nbsp;<a href="#"  onclick="window.location.href=\''.$new_url."&".$page_url_name.'='.'\'+document.getElementById(\'page_\').value;">GO！</a>
           ';
		}
		return $page_str;

	}


	/**
	 * 处理文件上传 
	 * @param array  $file
	 * @return array
	 */
	public function uploaderFILES($file){
		$file_types_array=$this->formUploadFiloeType;
		$max_file_size =$this->formUploadMaxSize;
		$upload_dir		=$this->formUploadDir;
		
		if($file["error"]!=UPLOAD_ERR_OK) 				return array(false, $this->lang->line('upload_error'));
		if($file["name"]=="")							return array(false, $this->lang->line('upload_empty'));
		if($file["size"]>$max_file_size)			 	return array(false, $this->lang->line('upload_maxsize'));
		$oldFilename = $file["name"];
		$filename = explode(".",$oldFilename);
		$filenameext = $filename[count($filename)-1];

		if(!in_array($filenameext,$file_types_array)) 	return array(false, $this->lang->line('upload_valid'));
		$newFilename=md5($oldFilename)."_".time()."_".rand(10000,20000).".".$filenameext;
		if(!is_dir($upload_dir)) $this->createfolder($upload_dir);
		if(!is_dir($upload_dir)) 						return array(false,"".$upload_dir.$this->lang->line('upload_dir_no_exist'));
		if(move_uploaded_file($file["tmp_name"], $this->relativeDir.$upload_dir.'/'.$newFilename)){
			return array(true, $upload_dir.$newFilename);
		}
		return array(false,"$oldFilename ".$this->lang->line('upload_no_succ'));

	} // funtion



	
}


?>
