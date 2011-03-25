<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('splitpage')){

	/*
	 *
	 * @total	integer	總筆數
	 * @control	array	上一頁、下一頁等的控制參數
	 * @number	array	每次顯示的頁數，與該頁數所顯示的url
	 * @url		string	serverUrl
	 */
	function splitpage($total, $control = array(), $number ,$url){

		//$this->view->headLink()->appendStylesheet('/css/admin/splitpage.css');

		/*
          <div class="pageNav">
            <a href="#" class="previous">上一頁</a>
            <a href="#">1</a>
            <span class="selected">2</span>
            <a href="#">3</a>
            <a href="#">4</a>
            <a href="#">5</a>
            <a href="#" class="next">下一頁</a>
          </div>
		 */

		$result = "<div class=''>";

		if($total > 1){
			//if($control['now'] == '1'){
			//	$result .= '<li class="previous-off">&laquo; 第一頁</li>';
			//} else {
			//	$result .= '<li class="previous"><a href="'.$url.$control['first'].'">&laquo; 第一頁</a></li>';
			//}
			
			//if(!isset($control['prevten'])){
			//	$result .= '<li class="previous-off">&laquo; 上十頁</li>';
			//} else {
			//	$result .= '<li class="previous"><a href="'.$url.$control['prevten'].'">&laquo; 上十頁</a></li>';
			//}
			
			if(!isset($control['prev'])){
				//$result .= '<a href="#" class="previous" title="上一頁">上一頁</a>';
			} else {
				$result .= '<a href="'.$url.$control['prev'].'" class="previous">上一頁</a>';
			}
			
			foreach($number as $key => $pagedata){
				if($pagedata['name'] == $control['now']){
					$result .= '<span class="selected">'.$pagedata['name'].'</span>';
				} else {
					$result .= '<a href="'.$url.$pagedata['link'].'">'.$pagedata['name'].'</a>';
				}
			}
			
			if(!isset($control['next'])){
				//$result .= '<a href="#" class="next">下一頁</a>';
			} else {
				$result .= '<a href="'.$url.$control['next'].'" class="next" title="下一頁">下一頁</a>';
			}
			
			//if(!isset($control['nextten'])){
			//	$result .= '<li class="previous-off">下十頁&raquo;</li>';
			//} else {
			//	$result .= '<li class="previous"><a href="'.$url.$control['nextten'].'">下十頁 &raquo;</a></li>';
			//}
			
			//if($control['now'] == $control['total']){
			//	$result .= '<li class="previous-off">最末頁&raquo;</li>';
			//} else {
			//	$result .= '<li class="next"><a href="'.$url.$control['last'].'">最末頁 &raquo;</a></li>';
			//}

			//$result .= '<li class="previous-off">總共'.$total.'筆</li>';

		} // total

		$result .= '</div>';

		//$result = "<ul id='pagination-clean'>";

		//if($total > 1){
		//	if($control['now'] == '1'){
		//		$result .= '<li class="previous-off">&laquo; 第一頁</li>';
		//	} else {
		//		$result .= '<li class="previous"><a href="'.$url.$control['first'].'">&laquo; 第一頁</a></li>';
		//	}
		//	
		//	if(!isset($control['prevten'])){
		//		$result .= '<li class="previous-off">&laquo; 上十頁</li>';
		//	} else {
		//		$result .= '<li class="previous"><a href="'.$url.$control['prevten'].'">&laquo; 上十頁</a></li>';
		//	}
		//	
		//	if(!isset($control['prev'])){
		//		$result .= '<li class="previous-off">&laquo; 上一頁</li>';
		//	} else {
		//		$result .= '<li class="previous"><a href="'.$url.$control['prev'].'">&laquo; 上一頁</a></li>';
		//	}
		//	
		//	foreach($number as $key => $pagedata){
		//		if($pagedata['name'] == $control['now']){
		//			$result .= '<li class="active">'.$pagedata['name'].'</li>';
		//		} else {
		//			$result .= '<li><a href="'.$url.$pagedata['link'].'">'.$pagedata['name'].'</a></li>';
		//		}
		//	}
		//	
		//	if(!isset($control['next'])){
		//		$result .= '<li class="previous-off">下一頁 &raquo;</li>';
		//	} else {
		//		$result .= '<li class="previous"><a href="'.$url.$control['next'].'">下一頁 &raquo;</a></li>';
		//	}
		//	
		//	if(!isset($control['nextten'])){
		//		$result .= '<li class="previous-off">下十頁&raquo;</li>';
		//	} else {
		//		$result .= '<li class="previous"><a href="'.$url.$control['nextten'].'">下十頁 &raquo;</a></li>';
		//	}
		//	
		//	if($control['now'] == $control['total']){
		//		$result .= '<li class="previous-off">最末頁&raquo;</li>';
		//	} else {
		//		$result .= '<li class="next"><a href="'.$url.$control['last'].'">最末頁 &raquo;</a></li>';
		//	}

		//	$result .= '<li class="previous-off">總共'.$total.'筆</li>';

		//} // total

		//$result .= '</ul>';

		return $result;

	} // splitpage

}
