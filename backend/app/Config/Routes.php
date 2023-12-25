<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

$routes->group('mlrs/api/v1', function($routes){
	$routes->group('auth', function($routes){
		$routes->post('login', 'Auth::login');
		$routes->post('getVersion', 'Auth::validateAppVersion');
		$routes->post('firstLoginChange', 'Auth::firstLoginChangePassword');
	}); 


	// All Users mmodule
	$routes->group('users', function($routes){
		$routes->post('create', 'Users::registerUser');
		$routes->get('getUsersList', 'Users::getAllUserList');
		$routes->post('changePassword', 'Users::ChangePassword');
		$routes->post('getUserById', 'Users::getUserDetails');

		// For the Mobile App API's
		$routes->get('getAgents', 'Users::getAgentUsers');
	});


	// Announcement mmodule
	$routes->group('announcement', function($routes){
		$routes->post('create', 'Announcement::saveAnnouncement');
		$routes->post('update', 'Users::registerUser');
		$routes->post('delete', 'Users::registerUser');
		$routes->get('getList', 'Announcement::getAnnouncementList');
		$routes->get('public/getList', 'Announcement::getAnnouncementListPublic');
	});



});


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

