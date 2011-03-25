<?php

class Splitpage {

  private $records_per_page;  // 顯示筆數
  private $page;              // 目前所在頁數 
  private $total_records;     // 每幾筆分一頁
  private $total_pages;       // 總分頁數
  private $started_record;
  
  // 每次顯示的分頁數量
  // 如果是10的話，範例就是1~10或10~20
  private $listPage;  

  private $startPage;
  private $endPage;
  public $viewlist;
  
  // 自己加上的功能
  public $basic_element; // [array] 基本元素(first,prev,next,last,now(現在頁面的編號))
  public $page_element;  // [array] 頁面編號及連結對應表

  //public function __construct() {}

  public function init($page = 0, $total_records = 0, $records_per_page = 0, $listPage = 0) {
    $this->records_per_page = $records_per_page;
    $this->page          = $page;
    $this->total_records = $total_records;
    $this->listPage      = $listPage;
    $this->setALL();
  }

  public function setALL() {  //設定類別參數
    $this->total_pages      = ceil($this->total_records / $this->records_per_page);
    $this->started_record = $this->records_per_page * ($this->page-1);
    if($this->listPage < $this->total_pages) {
      if($this->page % $this->listPage == 0)
        $this->startPage = $this->page - $this->listPage + 1;
      else
        $this->startPage = $this->page - $this->page % $this->listPage + 1;

      if(($this->startPage + $this->listPage) > $this->total_pages)
        $this->endPage = $this->total_pages;
      else
        $this->endPage = ($this->startPage + $this->listPage - 1);
    }
    else {
      $this->startPage = 1;/*預設頁面編號是1*/
      $this->endPage = $this->total_pages;
    }
  }

  /*
   * 產生2種導覽列，一種是基本版型的導覽列，另一種是可套其他版型的導覽列
   *
   * 引數說明
   *   url 網址(aaa.php)
   *   url_arg GET引數(abc=123)
   *   records_enable 是否要加上顯示每頁筆數的功能
   */
  //function setViewList($url, $url_arg, $records_enable = false) {
  public function setViewList($url, $url_arg = '') {

	if($url_arg != ''){
		$url = $url.'/'.$url_arg.'/';
	}
  
    // 初始化
    $this->viewlist = '';
    $this->basic_element = array();
    $this->page_element = array();
    
    // 預設值
    $this->basic_element['now'] = $this->page; /*指定現在的頁面編號*/
    $this->basic_element['total'] = $this->total_pages;
    $this->basic_element['first'] = $url.'1';
    
    // 如果總分頁數的設定超過1，才會跑這裡，大部份的情況都會超過0
    if($this->total_pages > 1) {
      if(($this->page - 1) != 0) {
        $this->basic_element['prev'] = $url.($this->page - 1);
        
        // 處理上十頁區段的地方
        if($this->total_pages > $this->listPage){
          if( (($this->startPage - $this->listPage) >= 1) and ($this->page > $this->listPage)) {
            //$this->basic_element['prevten'] = ($this->startPage - $this->listPage);
            $this->basic_element['prevten'] = $url.($this->startPage - 1);
          }
        }
      } /* if page-1 */

      for($pagenumber = $this->startPage; $pagenumber <= $this->endPage; $pagenumber++){
        if($pagenumber != $this->page){
          $this->page_element[] = array('name' => $pagenumber,
                                        'link' => $url.$pagenumber
                                       );
        // 現行的page編號要如何處理，就是這裡
        } else {
          $this->page_element[] = array('name' => $pagenumber,
                                        'link' => $url.$pagenumber
                                       );
        }
      } /* for */
      
      
      if(($this->page + 1) <= $this->total_pages) {
        // 處理下十頁區段的地方
        if(($this->total_pages > $this->listPage) and ($this->endPage != $this->total_pages)){
          $this->basic_element['nextten'] = $url.($this->endPage + 1);
        }
        $this->basic_element['next'] = $url.($this->page + 1);
      }
    }
    
    $this->basic_element['last'] = $url.$this->total_pages;
    
  } /* end function setViewList */
    
  /*
   * 產生導覽的基本元素與連結的對應陣列
   *
   * 陣列元素:
   *  first 第1頁
   *  prevten 上十頁(暫不寫)
   *  prev 上一頁(暫不寫)
   *
   *  nextten 下十頁(暫不寫)
   *  next 下一頁(暫不寫)
   *  last 最後一頁
   *
   * 陣列值:
   *  都是連結
   */
  public function setBasicElement($url) {
        
    $this->basic_element['first'] = $url.'page=1';
    
    // 如果目前所在的頁面和最後一頁是同一頁的情況
    if( $this->page == $this->endPage ){
      $this->basic_element['last'] = '';
    } else {
      $this->basic_element['last'] = $url.'page='.$this->endPage;
    }
    
  } /* end function setBasicElement */
  
  /*
   * 產生導覽的頁面編號及連結的對應陣列
   *
   * 陣列元素:
   *  name  頁面的編號
   *  link  頁面的連結
   */
  //function setPageElement($url, $target = false) {

  /*
   * 給Rewrite專用的導覽列
   * 
   * 引數說明:
   *   url 網址
   *   records_enable 是否要加上顯示每頁筆數的功能
   */
  public function setViewList_for_rewrite($url, $records_enable = false) {
  
    // 定義副檔名名稱
    $extensionname = '.html';
    
    // 分隔的字元
    $intervalchar = '_';
  
    // 初始化
    $url = $url.$intervalchar;
    $this->basic_element = array();
    $this->page_element = array();
  
    if($records_enable){
      // 本來是 => .html
      // 如果有啟用顯示每頁筆數的功能
      // 就會變成這樣子 => _20.html
      $extensionname = $intervalchar.$this->records_per_page.$extensionname;
    }
    
    // 預設值
    $this->basic_element['now'] = $this->page; /*指定現在的頁面編號*/
    $this->basic_element['total'] = $this->total_pages;
    $this->basic_element['first'] = $url.'1'.$extensionname;
    
    if($this->total_pages > 1) {
      if(($this->page - 1) != 0) {
        $this->basic_element['prev'] = $url.($this->page - 1).$extensionname;
        
        // 處理上十頁區段的地方
        if($this->total_pages > $this->listPage){
          if(($this->startPage - $this->listPage) >= 1 and $this->page > $this->listPage) {
            $this->basic_element['prevten'] = $url.($this->startPage - $this->listPage).$extensionname;
          }
        }
      }

      for($pagenumber = $this->startPage; $pagenumber <= $this->endPage; $pagenumber++){
        if($pagenumber != $this->page){
          $this->page_element[] = array('name' => $pagenumber,
                                        'link' => $url.$pagenumber.$extensionname
                                       );
        } else {
          $this->page_element[] = array('name' => $pagenumber,
                                        'link' => $url.$pagenumber.$extensionname
                                       );
        }
      } /*for*/
      
      if(($this->page + 1) <= $this->total_pages) {
        // 處理下十頁區段的地方
        if(($this->total_pages > $this->listPage) and ($this->endPage != $this->total_pages)){
          $this->basic_element['nextten'] = $url.($this->endPage + 1);
        }
        $this->basic_element['next'] = $url.($this->page + 1).$extensionname;
      }
    }
    $this->basic_element['last'] = $url.$this->endPage.$extensionname;
    
  } /* setViewList_for_rewrite */

} /* end class SplitPage */
