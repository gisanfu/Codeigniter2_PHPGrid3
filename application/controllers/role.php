<?php
if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );

class Role extends CI_Controller {
	function __construct() {
		parent::__construct ();

		$this->load->library ('Datagrid');
	}
	
	public function index($a1 = '', $a2 = '', $a3 = '', $a4 = '', $a5 = '', $a6 = '', $a7 = '')
	{
		$get = array($this->data['router_class'], $a1, $a2, $a3, $a4, $a5, $a6, $a7);
		$expamle = new Datagrid($get);
		//$expamle->role = 'v';
		$expamle->is_use_cache = false;
		$expamle->sql = 'SELECT * FROM `acl_sample_user_roles`';
		//$expamle->tables = '`acl_sample_user_roles`';
		//$expamle->fields = array('roles_name', 'roles_description', 'roles_parent_id');
		$expamle->fieldDisplays = array(
			'roles_name' => '角色名稱',
			'roles_description' => '描述',
			'roles_parent_id' => '繼承目標'
		);
		//$expamle->formFields = array('roles_name' => '角色名稱', 'roles_description' => '描述', 'roles_parent_id' => '繼承目標');
		$expamle->formTable = '`acl_sample_user_roles`';
		//$expamle->formTypes=array('roles_name' => 'text');
		//$expamle->formPrimkey = 'roles_id';

		$expamle->formFields = $expamle->fieldDisplays;

		$expamle->searchFields=array(
			'roles_name' => '角色名稱'
		);

		//$expamle->fields=array('id', 'login_user', 'login_pass');
		//$expamle->fieldDisplays = array("login_user"=>"User");
		//$expamle->formFieldsValidates = array("login_user"=>"Presence");

		$expamle->title = '管理員設定';
		//$html = $expamle->display();
		//echo $html;
		//die;
		//$this->data['
		$this->data['title'] = '管理員設定 :: 角色';
		$this->data['main_content'] = 'acl';
		$this->data['phpgrid'] = $expamle->display();
		$this->data['head_css'] = '<link href="/admin/css/phpgrid/oranges-in-the-sky-gisanfu.css" rel="stylesheet" type="text/css">';
		$this->smarty_parser->parse('ci:index.htm', $this->data);
	}
}
