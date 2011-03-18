<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$route['(:any)--phpgrid_method_(:any)_(:any)--(:any)--(:num)--(:any)--(:num)'] = '$1/index/method/$3/$2/$4/$5/$6/$7';
$route['(:any)--phpgrid_method_(:any)_(:any)--(:any)--(:num)'] = '$1/index/method/$3/$2/$4/$5';
$route['(:any)--phpgrid_method_(:any)_(:any)'] = '$1/index/method/$3/$2';
$route['(:any)--phpgrid_page_(:any)_(:num)'] = '$1/index/page/$2/$3';
$route['(:any)--phpgrid_order_(:any)--(:any)'] = '$1/index/order/$2/$3';
