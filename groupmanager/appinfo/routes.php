<?php

/**
* ownCloud - App Template Example
*
* @author Bernhard Posselt
* @copyright 2012 Bernhard Posselt nukeawhale@gmail.com 
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either
* version 3 of the License, or any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*
* You should have received a copy of the GNU Affero General Public
* License along with this library.  If not, see <http://www.gnu.org/licenses/>.
*
*/

namespace OCA\Groupmanager;

use \OCA\AppFramework\App;

use \OCA\Groupmanager\DependencyInjection\DIContainer;


/*************************
 * Define your routes here
 ************************/

/**
 * Normal Routes
 */
 
// Route to the index Method from itemcontroller.php
$this->create('groupmanager_index', '/')->action(
	function($params){
		App::main('ItemController', 'index', $params, new DIContainer());
	}
);

// Route to the getRightContent Method from itemcontroller.php
$this->create('groupmanager_getRightContent', '/getRightContent/{id}')->action(
	function($params){
		App::main('ItemController', 'getRightContent', $params, new DIContainer());
	}
);

// Route to the createGroup Method from itemcontroller.php
$this->create('groupmanager_createGroup', '/createGroup/')->post()->action(
	function($params){
		App::main('ItemController', 'createGroup', $params, new DIContainer());
	}
);

// Route to the getGroups Method from itemcontroller.php
$this->create('groupmanager_getGroups', '/getGroups/')->action(
	function($params){
		App::main('ItemController', 'getGroups', $params, new DIContainer());
	}
);

// Route to the getGroup Method from itemcontroller.php
$this->create('groupmanager_getGroup', '/getGroup/')->post()->action(
	function($params){
		App::main('ItemController', 'getGroup', $params, new DIContainer());
	}
);

/*
$this->create('groupmanager_index_param', '/test/{test}')->action(
	function($params){
		App::main('ItemController', 'index', $params, new DIContainer());
	}
);

$this->create('groupmanager_index_redirect', '/redirect')->action(
	function($params){
		App::main('ItemController', 'redirectToIndex', $params, new DIContainer());
	}
);
*/

/**
 * Ajax Routes
 */
/*
$this->create('groupmanager_ajax_setsystemvalue', '/setsystemvalue')->post()->action(
	function($params){
		App::main('ItemController', 'setSystemValue', $params, new DIContainer());
	}
);

$this->create('groupmanager_ajax_getsystemvalue', '/getsystemvalue')->post()->action(
	function($params){
		App::main('ItemController', 'getSystemValue', $params, new DIContainer());
	}
);
*/
