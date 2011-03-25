<?php

/**
 *该类只生成表格列表
 *
 *
 *
 <table summary=" table of Datagrid">
 <caption>列表</caption>
 <thead>
 <tr>
 <td scope="col"><a href="?datagrid_order=ge_userid">用户ID</a></td>
 <td scope="col"><a href="?datagrid_order=ge_cityid">城市ID</a></td>
 <td scope="col"><a href="?datagrid_order=ge_name">名称</a></td>
 <td scope="col"><a href="?datagrid_order=ge_equipment">信息</a></td>
 <tr>
 </thead>
 <tbody>
 <tr  >
 <td>2</td>
 <td>2222222222222</td>
 <td>Name</td>
 <td></td>
 </tr>
 </tbody>
 </table>
 *
 *@example
 $test=new Dataview();
 $test->sql="SELECT  *  FROM  tb_user ";
 $test->title="用户列表";
 $test->is_use_cache=true;
 echo $html=  $test->display();

 *
 *
 *
 */

//session_start();
//include_once('config.inc.php');
// require_once("Mysql.php");

//load language file 
//require_once("lang/".LANGUAGE_FILE);


//$this->tableBodyHtml.='<a title="body_control" href="?datagrid_action=view&var_name='.$var_name.'&'.$linkUrl.'" >'.$GLOBALS['language']['view'].'</a> ';
/*
 * GET url define
 *
 * CRUD都會有的引數
 *
 *	   @datagrid_action 可能會有view, add, edit, delete, copy
 *
 *	   http://URL/MMM?datagrid_action=delete&var_name=CI&roles_id=1&
 *
 *	   打算做成這個樣子
 *
 *	   <http://URL/MMM/phpgrid_method_ACTION_VARNAME/PRIMARYNAME/VALUE>
 *
 *	   以原本的網址，會變成以下這個樣子
 *
 *	   <http://URL/MMM/phpgrid_method_delete_CI/roles_id/1>
 *
 * 分頁，會有首頁、第一頁、下一頁、最後一頁，還有GOTO某一頁，都是以下的網址
 *
 *	   http://URL/MMM?var_name=CI&CI_page=2
 *
 *	   分頁，打算做成這個樣子
 *
 *	   <http://URL/MMM/phpgrid_page_VARNAME_NNN>
 *
 *	   更改過後的網址如下
 *
 *	   <http://URL/MMM/phpgrid_page_CI_2>
 *
 * 欄位排序
 *
 *	   http://URL/MMM?CI_order=resources_name&var_name=CI
 *
 *	   欄位的部份，打算做成這個樣子
 *
 *	   <http://URL/MMM/phpgrid_order_VARNAME_FIELDNAME>
 *
 *	   更改過後的網址如下
 *
 *	   <http://URL/MMM/phpgrid_order_CI/resources_name>
 *
 * 所以route需要做以下的設定
 * 有以下固定的設計方式:
 *
 * - 剛開始一定是先讓我知道這一批的GET變數群是什麼
 * - 第二個引數一定是var_name(phpgrid所定義的東西)
 *
 * 另外，為什麼有些是底線，有些是斜線呢？
 *
 * - 因為有些引數是可能會帶底線的，所以才會使用斜線來分隔
 *
 * $route['MMM/phpgrid_method_(:any)_(:any)/(:any)/(:num)'] = 'MMM/index/method/$2/$1/$3/$4';
 * $route['MMM/phpgrid_page_(:any)_(:num)'] = 'MMM/index/page/$1/$2';
 * $route['MMM/phpgrid_order_(:any)/(:any)'] = 'MMM/index/order/$1/$2';
 */

/**
 * 该类只生成表格列表
 * @author cdwei
 *
 */
class Dataview {
	//数据库连接对象
	protected  $db=null;

	public $is_use_cache=true;
	public  $role="v,u,a,d";

	public  $this_var_name="";
	private $create_time="";
	public  $relativeDir="";
	public  $absoluteDir	="";
	
	public $headerJS		="";
	public $title			="";
	public $tableTitleHtml	="";
	public $tableHeaderHtml	="";
	public $tableBodyHtml	="";
	public $tableFooterHtml	="";
	public $tableHtml		="";


	//显示设置   
	public $displayTableTitle	=true;	//是否显示表标题的HTML
	public $displayTableHeader	=true;	//是否显示表头的HTML 
	public $displayTableBody	=true;	//是否显示表内容的HTML  
	public $displayOrder		=true;	//是否可以排序
	public $displayHideField	=false;	//显示隐藏列
	public $displayControl 		=false;	//显示操作列 

	//数据设置
	public $rows=array();
	public $sql="";
	public $tables="";					//SQL查询中表名称，可以是单表，多表，或者连接表
	public $where="";
	public $fields				=array();
	public $fieldDisplays		=array();
	public $fieldStyles			=array();
	public $hideField			="";

	public $formPrimkey			="";

	//私有方法，外部不能直接访问和设置这些属性
	private $fieldInfo			=array();
	private $tableNames			=array();
	private $fieldKeys			=array();
	private $fieldComments		=array();
	private $fieldTypes			=array();
	private $noSetDisplayFlag	=false;

	protected $session;
	protected $lang;

	// 用來取代原有的$_GET變數使用
	protected $get;

	protected $isloadjs = '';

	protected $ci;

	/**
	 *构造函数,初始化连接数据库
	 */
	function __construct($sourcedata = array()){
		$this->get = $this->handle_array($sourcedata);

		//var_dump($this->get);
		//die;

		$CI = &get_instance();
		$this->db = $CI->db;
		$this->session = $CI->session;
		$this->lang = $CI->lang;

		$this->ci = $CI;

		//header("Content-Type: text/html; charset=utf-8");
		$this->create_time = time();
		/*
		if(!empty($sql)) $this->sql=$sql;
		if(isset($this->GLOBALS['pool_grid_db'.$database])){
			$this->db=$this->GLOBALS['pool_grid_db'.$database];
			return true;
		}
		if(empty($host)) $host=$this->GLOBALS['datagrid']['db']['host'];
		if(empty($user)) $user=$this->GLOBALS['datagrid']['db']['user'];
		if(empty($password)) $password=$this->GLOBALS['datagrid']['db']['passwd'];
		if(empty($database)) $database=$this->GLOBALS['datagrid']['db']['dbname'];
		if(empty($charset)) $charset=$this->GLOBALS['datagrid']['db']['charset'];
		if(!class_exists("PDO")){
			$this->db=new MysqlDB('mysql', $host, $user, $password, $database,$charset);
		}else{
			$this->db=new MysqlPDO('mysql', $host, $user, $password, $database,$charset);
		}
		$this->GLOBALS['pool_grid_db'.$database]=$this->db;
		 */
		$this->absoluteDir=dirname(__FILE__)."/";
		$this->relativeDir=$this->getRelativeDir();  

		$this->lang->load('phpgrid', 'english');
	}

	/*
	 * 將CI傳來的GET分散的變數，改成phpgrid看得懂的東西
	 * 目前所知，最多有5個變數
	 */
	protected function handle_array($arr = array()){
		$result = array();

		if(count($arr) == 0) return $result;

		// 在Zend來說，它叫做controller，我在CI，我稱它做router_class
		$result['router_class'] = $arr[0];

		// 因為引數2是每一個動作都會有的
		$var_name = $arr[2];
		//echo $arr[1];
		//var_dump($arr);
		//die;

		if($arr[1] == 'method'){
			$result['var_name'] = $var_name;
			$result['datagrid_action'] = $arr[3];
			if(!empty($arr[4])) $result[$arr[4]] = $arr[5];
			if(!empty($arr[6])) $result[$arr[6]] = $arr[7];
		} elseif($arr[1] == 'page'){
			$result['var_name'] = $var_name;
			$result[$var_name.'_page'] = $arr[3];
		} elseif($arr[1] == 'order'){
			$result['var_name'] = $var_name;
			$result[$var_name.'_order'] = $arr[3];
		}

		return $result;
	}

	/**
	 *将一个PHP变量写入到php文件 中，从而避免从数据库中读取资源,只写在一个PHP文件中写入一个变量 
	 *@param $script php文件名
	 *@param $cachenames要缓存的名称
	 *@param $cachedata 要缓存的变量
	 *@param $exprie_time 过期时间 
	 */
	private function writeVarToCache($script, $cachenames, $cachedata = '',$exprie_time=3600) {
		if(!$this->is_use_cache)  return false;
		$dir =$this->relativeDir.'cache/';
		if(!is_dir($dir)) {
			@mkdir($dir, 0777);
		}

		$cachedata_str=$cachenames."=".var_export($cachedata,true);
		$code="<?php\n//Cache file, DO NOT modify me!".
				"\n//Created: ".date("M j, Y, G:i").
				"\n\$exprie_time=".strval(time()+$exprie_time).";\n
				".$cachedata_str.";\n
				?>";
			
		if($fp = fopen($dir.$script, 'wb')) {
			fwrite($fp, $code);
			fclose($fp);
		} else {
			show_error('Can not write to cache files, please check directory and file   '.$dir.$script);
		}
	}

	/**
	 *将多个PHP变量写入到一个PHP文件中
	 *特别注意$cacheNamesArr的值得作为$cacheDataArr对应顺序的值，不要给这两个变量设置索引(下标),下面的参数正确的：
	 *$cacheNamesArr=array('$test','$test2');$cacheDataArr=array($cache_test,$cache_test2);
	 *@param $cacheNamesArr
	 *@param $cacheDataArr
	 */
	private function writeManyVarToCache($script, $cacheNamesArr = array(), $cacheDataArr = array(),$exprie_time=3600) {
		if(!$this->is_use_cache)  return false;
		if(empty($cacheNamesArr) || empty($cacheDataArr)) return false;

		$dir =$this->relativeDir.'cache/';
		if(!is_dir($dir)) {
			@mkdir($dir, 0777);
		}
		$cachedata_str="\n";
		foreach ($cacheNamesArr as $key =>  $name) {
			if(!is_string($name)) continue;
			if(!isset($cacheDataArr[$key])) continue;
			if(!strstr($name,'$')) $name='$'.$name;
			$cachedata_str.=$name."=".var_export($cacheDataArr[$key],true).";\n";
		}

		$code="<?php\n//Cache file, DO NOT modify me!".
				"\n//Created: ".date("M j, Y, G:i").
				"\n\$exprie_time=".strval(time()+$exprie_time).";\n
				".$cachedata_str.";\n
				?>";
			
		if($fp = fopen($dir.$script, 'wb')) {
			fwrite($fp, $code);
			fclose($fp);
		} else {
			exit('Can not write to cache files, please check directory and file   '.$dir.$script);
		}
	}

	/**
	 * 创建文件夹
	 * @param $folder
	 * @return none
	 */
	static function createfolder( $folder )
	{
		$folder = explode( "/" , $folder );
		$mkfolder = '';
		for(  $i=0 ; isset( $folder[$i] ) ; $i++ )
		{
			$mkfolder .= $folder[$i] . '/';
			if( !is_dir( $mkfolder ) ){
				mkdir( "$mkfolder" ,  0777);
			}
		}
	}

	/**
	 *取得相对于phpgrid的相对路径
	 */
	public function getRelativeDir() {
			
		$phpgridDir	= $this->absoluteDir;
		$phpgridDir	= str_replace("\\","/",$phpgridDir);
		$dir2		= str_replace("\\","//",$_SERVER['SCRIPT_FILENAME']);
		$dir2Info	= pathinfo($dir2);
		$dir2		= str_replace($dir2Info['basename'],"",$dir2);
		if(strlen($phpgridDir)<strlen($dir2)){
			$last_str=str_replace($phpgridDir,"",$dir2);
			$count	= count(explode("/",$last_str))-1;
			$str	= "";
			for($i=0;$i<$count;$i++){
				$str.="../";
			}
			return $str;
		}else{
			$last_str=str_replace($dir2,"",$phpgridDir);
			return $last_str;
		}
		return "";

	}


	/**
	 * 构造SQL语句
	 * @return string
	 */
	public  function makeSql() {
		$var_name=$this->this_var_name;
		$sql=strtolower($this->sql);
		if(empty($sql)){
			//die('您既没有设定SQL语句也没有指定字段列表和表名')
			if(empty($this->fields) || empty($this->tables)) show_error('sorry, system have no.7776 error');
			$sql='Select '.implode(',',$this->fields).",".$this->hideField." ";
			$sql=strtolower($sql);
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

		//排序计算
		$order="desc";
		if(isset($this->get[$var_name.'_order'])){
			$order_field = $this->get[$var_name.'_order'];
			/*
			if(isset($_SESSION[$var_name.'_datagrid_order'])){
				if($_SESSION[$var_name.'_datagrid_order']=="desc") {
					$order="asc";
					$_SESSION[$var_name.'_datagrid_order']="asc";
				} else{
					$order="desc";
					$_SESSION[$var_name.'_datagrid_order']="desc";
				}
			}else{
				$_SESSION[$var_name.'_datagrid_order']="desc";
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
				if(preg_match("/limit(.*?)$/im",$sql)){ ;

				list($sql_1,$sql_2) = preg_split('/limit\s/im', $sql);
				$sql=$sql_1." order by  ".$order_field." ".$order." limit ".$sql_2;
				}else{
					$sql.=' order by `'.$order_field.'` '.$order.' ';
				}
			}
		}
		//echo $sql;
		//file_put_contents("sql.txt",$sql.'');
		return $sql;
	}


	/**
	 * 当没有指定SQL中字段时取得字段列表
	 * @return bool
	 */
	public function makeFields() {
		if(empty($this->sql)) return false;
		$var_name=$this->this_var_name;

		$tempSql=strtolower($this->sql);
		$fields="";
		if (preg_match('/select(.*?)from/', $tempSql, $regs)){
			$fields = trim($regs[1]);
		}else{
			show_error('sorry, system have no.7777 error');
			//die($this->sql."该SQL语句无效，因为它没有包含select 和from语句!")
		}
		$tableNames=array();
		//解析出表名和字段列表	
		list($temp_sql)=explode('where',$tempSql);
		$sql="EXPLAIN ".$temp_sql;
		$cache_file='cache_' . $var_name.'_'.md5($sql).'.php';
		//缓存中取数据
		if($this->is_use_cache){
			if (file_exists('cache/'.$cache_file)) {
				include_once('cache/'.$cache_file);
				if($exprie_time > time()) {
					$explain_row = $explain_row_cache;
					$tableNames = $tableNames_cache;
					$fieldInfo = $fieldInfo_cache;
				}
			}
		}
		//如果没有从缓存中取数据或者获取失败则数据库查询
		$cacheNameArr=array();
		$cacheDataArr=array();
		if(empty($explain_row)){
			//$explain_row=$this->db->getAll($sql);
			$query = $this->db->query($sql);
			$explain_row = $query->result_array();
			$cacheNameArr[]='$explain_row_cache';
			$cacheDataArr[]=$explain_row;
		}
		if(empty($tableNames) || empty($fieldInfo)){
			foreach ($explain_row as $k => $row) {
				$tablName=$row['table'];
				$tableNames[]=$tablName;
				$sql_temp="SHOW FULL COLUMNS FROM ".$tablName;
				$query = $this->db->query($sql_temp);
				//$fieldInfo[$tablName]=$this->db->getAll($sql_temp);
				$fieldInfo[$tablName] = $query->result_array();
			}
			$cacheNameArr[]='$tableNames_cache';
			$cacheDataArr[]=$tableNames;
			$cacheNameArr[]='$fieldInfo_cache';
			$cacheDataArr[]=$fieldInfo;
		}

		//写入缓存
		if($this->is_use_cache){
			$this->writeManyVarToCache($cache_file,$cacheNameArr,$cacheDataArr);
		}
		$this->fieldInfo=$fieldInfo;
		$this->tableNames=$tableNames;
		$fields_arr=array();
		$fieldKeys =array();
		$fieldComments=array();
		$fieldTypes=array();
		foreach ($fieldInfo as $key => $tableFields) {
			foreach ($tableFields as $key => $fieldRow) {
				$field_					= trim($fieldRow['Field']);
				$fields_arr[]			= $field_;
				$fieldComments[$field_] = $fieldRow['Comment'];
				$fieldTypes[$field_]	= $fieldRow['Type'];
				if($fieldRow['Key']=='PRI') $fieldKeys[$key]=$fieldRow['Field'];
			}
		}
		$this->fieldKeys=$fieldKeys;
		$this->fieldComments=$fieldComments;
		$this->fieldTypes=$fieldTypes;
		if($fields=="*"){
			if(empty($this->fields)) $this->fields=$fields_arr;
			if(empty($this->fieldDisplays))  $this->fieldDisplays=array_combine ($fields_arr,$fields_arr);
		}else{
			$fields=str_replace('`','',$fields);
			$fieldTempArr=explode(",",$fields);
			$fields=array();
			$displayFields=array();
			foreach ($fieldTempArr as $ff) {
				if(!strstr($ff,' as ')){
					$fields[]=trim($ff);
				}else{
					list(,$fields[])=explode("as",trim($ff));
				}
			}
			if(empty($this->fields)) $this->fields=$fields;
			if(empty($this->fieldDisplays)) $this->fieldDisplays=array_combine ($fields,$fields);
		}
		return true;
	}



	/**
	 *通过传入字段的类型，计算出该字段占据列的长度百分比
	 */
	public function getFieldLenght($type) {
		$len=5;
		if(strstr($type,'varchar') or strstr($type,'char')){
			$num=$this->getInLenght($type);
			$len=15;
			if($num<20) $len=10;
			if($num>=100) $len=20;
		}
		if(strstr($type,'text') or strstr($type,'blob') ){
			$len=20;//
		}
		return $len;
	}



	/**
	 *取得括号内的数值,不使用正则表达式
	 */
	static public function getInLenght($type){
		$num=0;
		list(,$temp)=explode('(',$type);
		list($num)=explode(')',$temp);
		if(empty($num)) return 0;
		return $num;
	}


	/**
	 * 根据SQL语句生成数据
	 * @return string of html
	 */
	public function makeDataset() {
		$this->sql=$this->makeSql();
		//$rows=$this->db->getAll($this->sql);
		$query = $this->db->query($this->sql);
		$rows = $query->result_array();
		//$this->db->query("INSERT INTO  `tb_logs` (`lg_sql`, `lg_time`) VALUES ('".mysql_escape_string($this->sql)."', UNIX_TIMESTAMP());");
		//print_r($rows);
		if(empty($this->fieldDisplays)) $this->noSetDisplayFlag=true;
		$this->makeFields();//如果没有设置字段列表则生成所有的
		$this->rows=$rows;

	}
	
	/**
	 *生成头部的JS
	 */
	public function setHeaderJS() {
		
		if($this->isloadjs == ''){//避免重复加载
			$this->headerJS=' <!-- 后台JS代码及CSS -->
			<script type="text/javascript" src="'.$this->relativeDir.'js/common.js"></script> '; 
			$this->isloadjs = 'load';
		}
	}


	/**
	 *生成表格标题的HTML
	 */
	public function setTableTitle() {
		if($this->displayTableTitle)  {
			$addCos=0;
			if($this->displayControl) $addCos=1;
			//$this->tableTitleHtml.='<tr><td colspan="'.strval(count($this->fields)+2+$addCos).'" class="td_3_style"><span class="table_header">'.$this->title.'</span></td></tr>';
			$this->tableTitleHtml.='
			<caption>'.$this->title.'</caption>';
		}
	}

	/**
	 *生成表格头部的HTML
	 */
	function setTableHeaderHtml() {
		if($this->displayTableHeader){
			$var_name=$this->this_var_name;

			$this->tableHeaderHtml.='
			<thead id="'.$var_name.'_thead">
				<tr id="'.$var_name.'_thead_tr">';
			$fieldTypes=$this->fieldTypes;
			if($this->displayHideField){
				$this->tableHeaderHtml.='<th width="2%" scope="col"  ><img id="imgb" src='.$this->relativeDir.'images/closeb.gif  style="CURSOR: hand" onClick="viewall('.count($this->rows).',\''.$var_name.'\',\''.str_replace("/","$$$",$this->relativeDir).'\')"/></th>';
			}
			foreach ($this->fieldDisplays as $key =>$displaycol){
				if($key==$this->hideField) continue;
				$lenght=5;
				$rowStyle=' ';
				if(isset($fieldTypes[$key])){
					$lenght=$this->getFieldLenght($fieldTypes[$key]);
				}
				if($this->noSetDisplayFlag==true and isset($this->fieldComments[$key])){
					if(!empty($this->fieldComments[$key])) $displaycol.='('.$this->fieldComments[$key].')';
				}
				if(isset($this->fieldStyles[$key])) $rowStyle=$this->fieldStyles[$key];
				$cols=$displaycol;
				if($this->displayOrder){
					//$cols='<a href="?'.$var_name.'_order='.$key.'&var_name='.$var_name.'">'.$displaycol.'</a>';
					$cols='<a href="'.base_url().'/'.$this->get['router_class'].'--phpgrid_order_'.$var_name.'--'.$key.'">'.$displaycol.'</a>';
				}
				$this->tableHeaderHtml.='<th '.$rowStyle.'  scope="col">'.$cols.'</th>';
			}
			if($this->displayControl){
				$this->tableHeaderHtml.='<th  width="10%" align="center"  scope="col" id="'.$var_name.'_td_control"><a href=# >'.$this->lang->line('action').'</a></th>';
			}

			$this->tableHeaderHtml.='
				
			<tr>
			</thead> 
			';
		}
	}

	/**
	 *生成表格正文的HTML
	 */
	function setTableBodyHtml() {
		if(!$this->displayTableBody) return false;
		$var_name=$this->this_var_name;
		$addCos=0;
		if($this->displayControl) $addCos=1;
		$rows=$this->rows;
		//print_r($this->fieldDisplays);die;
		$jj=0;
		$j=0;
		$this->tableBodyHtml='<tbody id="'.$var_name.'_tbody">';
		foreach ($rows as $key =>$row){
			$jj++;
			$trClass="";
			if(fmod ($jj,2)==0){
				$trClass=' class="odd"';
			}
			$this->tableBodyHtml.='<tr  id="'.$var_name.'_tbody_tr_'.$jj.'"  '.$trClass.'   >'."\n";
			//隐藏列的+-
			if(!empty($this->displayHideField)){
				$this->tableBodyHtml.="\t".'<td  width="2%" ><IMG  src="'.$this->relativeDir.'images/menu_close.gif"  id="'.$var_name.'_img'.$jj.'" style="CURSOR: hand"    onclick="bb2('.$var_name.'_hide'.$jj.','.$var_name.'_img'.$jj.',\''.$this->relativeDir.'\')">
		 			</td>'."\n";
			}
			//主体列
			foreach ($this->fieldDisplays as $field =>$display){
				if($field!=$this->hideField){
					if(strstr($field," as ")){
						list(,$field)=explode(" as ",$field);// get ' COLUMNS as Name' of the Name
					}

					$td_html=$this->repalceValue($row,$field,$row[$field]);
					$this->tableBodyHtml.="\t".'<td  >'.$td_html.'</td>'."\n";
				}
			}
			//是否显示操作列 
			if($this->displayControl){
				$this->tableBodyHtml.="\t".'<td align="center" id="'.$var_name.'_tbody_td_control_'.$jj.'">';
				$linkUrl="#";
				if(!empty($this->fieldKeys)){
					$linkUrl="";
					$in_flag=false;
					// 這裡目前只有修改它，到支援二組的LinkURL
					foreach ($this->fieldKeys as $field_key  ) {
						if(isset($row[$field_key])){
							$in_flag=true;
							//$linkUrl.=''.$field_key.'='.$row[$field_key].="&";
							$linkUrl .= '--'.$field_key.'--'.$row[$field_key];
						}
					}
					if($in_flag==false){
						$formPrimkey=$this->formPrimkey;
						if(isset($row[$formPrimkey])){
							//$linkUrl.=''.$formPrimkey.'='.$row[$formPrimkey].="&";
							$linkUrl .= '--'.$formPrimkey.'--'.$row[$formPrimkey];
						}
					}
				}
				$this->role=strtolower($this->role);
				$roles=explode(",",$this->role);
				
				if(in_array("v",$roles)) {
					//$this->tableBodyHtml.='<a title="body_control" href="?datagrid_action=view&var_name='.$var_name.'&'.$linkUrl.'" >'.$GLOBALS['language']['view'].'</a> ';
																	// $route['(:any)_phpgrid_method_(:any)_(:any)_(:any)_(:num)'] = '$1/index/method/$3/$2/$4/$5';
					$this->tableBodyHtml.='<a title="body_control" href="'.base_url().'/'.$this->get['router_class'].'--phpgrid_method_view_'.$var_name.$linkUrl.'" >'.$this->lang->line('view').'</a> ';
				}
				
				if(in_array("u",$roles)) {
					//$this->tableBodyHtml.='<a title="body_control" href="?datagrid_action=edit&var_name='.$var_name.'&'.$linkUrl.'" >'.$GLOBALS['language']['edit'].'</a>';
					//$this->tableBodyHtml.='<a title="body_control" href="?datagrid_action=copy&var_name='.$var_name.'&'.$linkUrl.'" >'.$GLOBALS['language']['copy'].'</a>';
					$this->tableBodyHtml.='<a title="body_control" href="'.base_url().'/'.$this->get['router_class'].'--phpgrid_method_edit_'.$var_name.$linkUrl.'" >'.$this->lang->line('edit').'</a>';
					$this->tableBodyHtml.='<a title="body_control" href="'.base_url().'/'.$this->get['router_class'].'--phpgrid_method_copy_'.$var_name.$linkUrl.'" >'.$this->lang->line('copy').'</a>';
				}
				if(in_array("d",$roles)) {
					//$this->tableBodyHtml.='<a href="#" title="body_control"   onClick="if(confirm(\''.$GLOBALS['language']['confirm'].'\')) window.location.href=\'?datagrid_action=delete&var_name='.$var_name.'&'.$linkUrl.'\'" >'.$GLOBALS['language']['delete'].'</a>';
					$this->tableBodyHtml.='<a href="#" title="body_control" onClick="if(confirm(\''.$this->lang->line('confirm').'\')) window.location.href=\''.base_url().'/'.$this->get['router_class'].'--phpgrid_method_delete_'.$var_name.$linkUrl.'\'" >'.$this->lang->line('delete').'</a>';
				} 
					
				$this->tableBodyHtml.='</td>';
			}
			$this->tableBodyHtml.="\n</tr>\n";
			//隐藏列
			if($this->displayHideField){
				$hideHtml="";
				if(isset($row[$this->hideField])){
					$hideHtml=$this->repalceValue($row,$this->hideField,$row[$this->hideField]);
				}
				$this->tableBodyHtml.='<tr style="display:none"  bgcolor="#CCCCCC"  id="'.$var_name.'_hide'.$jj.'"><td colspan="'.strval(count($this->fields)+2+$addCos).'"  >'.$hideHtml.'</td></tr>';
			}
			$j++;
		}
		$this->tableBodyHtml.='</tbody>'."\n";
	}


	/**
	 *重写此方法可以重新设定表格中每一列的HTML
	 */
	function repalceValue($row,$field,$value) {
		return $value;
	}

	/**
	 *通过SQL语句 SHOW FULL COLUMNS FROM 获得一个表的详细信息
	 */
	public function getTableInfo($tableName) {

		$cache_file="cache_".$tableName."_info.php";
		$tableNameInfo=array();
		//缓存中取数据
		if($this->is_use_cache){
			if (file_exists('cache/'.$cache_file)) {
				include_once('cache/'.$cache_file);
				if($exprie_time>time()) {
					$tableNameInfo=$tableNameInfo_cache;
					return $tableNameInfo;
				}
			}
		}
		//如果没有从缓存中取数据或者获取失败则数据库查询
		if(empty($tableNameInfo)){
			$sql="SHOW FULL COLUMNS FROM $tableName";
			//$temp_fields=$this->db->getAll($sql);
			$query = $this->db->query($sql);
			$temp_fields = $query->result_array();

			$fields=array();
			$primkey="";
			$fieldDefaults=array();
			$fieldTypes=array();
			foreach ($temp_fields as $number => $row) {
				$fieldName="";
				$field=$row['Field'];
				$fieldName=$field;
				if(!empty($row['Comment'])) $fieldName.='('.$row['Comment'].')';
				$fields[$field]=$fieldName;
				if($row['Key']=="PRI") $primkey=$field;
				if($row['Default']=="NULL" && empty($row['Default'])) {
					$fieldDefaults[$field]="";
				}else{
					$fieldDefaults[$field]=$row['Default'];
				}
				$fieldTypes[$field]=$row['Type'];
			}
		}
		$tableNameInfo=array($fields,$fieldTypes,$primkey,$fieldDefaults);
		if($this->is_use_cache){
			$this->writeVarToCache($cache_file,'$tableNameInfo_cache',$tableNameInfo);
		}
		return $tableNameInfo;
	}


	/**
	 * 连接HTML
	 * @return string of HTML
	 */
	public function makeHtml() {
		$html="";
		$html.=$this->headerJS;
		$html.='<table class="'.$this->_table_classname.'" summary=" table of Dataview"  >';
		$html.=$this->tableTitleHtml;
		$html.=$this->tableHeaderHtml;
		$html.=$this->tableBodyHtml;
		$html.='
		</table>';
		return $html;
	}

	/**
	 * 返回HTML
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
		if(empty($this->rows)) {
			$this->makeDataset();
		}
		$this->setHeaderJS();
		$this->setTableTitle();
		$this->setTableHeaderHtml();
		$this->setTableBodyHtml();
		return $this->makeHtml();
	}
}
