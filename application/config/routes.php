<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'auth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['users/(:num)'] = 'users/update/$1';
$route['articles/(:num)'] = 'articles/update/$1';

$route['api/test'] = 'api/test';

$route['login'] = 'auth/login';

$route['api/login'] = 'api/auth/login';
$route['api/logout'] = 'api/auth/logout';

$route['api/users']['GET'] = 'api/users/index';
$route['api/users']['POST'] = 'api/users/store';
$route['api/users/(:num)']['GET'] = 'api/users/show/$1';
$route['api/users/(:num)']['PUT'] = 'api/users/update/$1';
$route['api/users/(:num)']['DELETE'] = 'api/users/delete/$1';

$route['api/datas']['GET'] = 'api/articles/index';
$route['api/datas']['POST'] = 'api/articles/store';
$route['api/datas/(:num)']['GET'] = 'api/articles/show/$1';
$route['api/datas/(:num)']['POST'] = 'api/articles/update/$1';
// $route['api/datas/(:num)']['PUT'] = 'api/articles/update/$1';
$route['api/datas/(:num)']['DELETE'] = 'api/articles/delete/$1';
