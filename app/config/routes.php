<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'index';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


/*
前台路由重定向
 */
//$route['index'] = 'main/index';
$route['list/(:num)'] = 'column/index/$1';
$route['arc/(:num)'] = 'archive/index/$1';

