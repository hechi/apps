<?php

namespace OCA\Groupmanager;

use \OCA\AppFramework\App;
use \OCA\Groupmanager\DependencyInjection\DIContainer;

/*************************
 * Define your routes here
 ************************/

/**
 * Normal Routes
 */

// Route to the index Method from pagecontroller.php
$this->create('groupmanagerIndex', '/')->action(
    function($params){
        // call the index method on the class PageController
        App::main('PageController', 'index', $params, new DIContainer());
    }
);

// Route to the getRightContent Method from pagecontroller.php
$this->create('groupmanagerGetRightContent', '/getRightContent/{id}')->action(
	function($params){
		App::main('PageController', 'getRightContent', $params, new DIContainer());
	}
);

// Route to the createGroup Method from pagecontroller.php
$this->create('groupmanagerCreateGroup', '/createGroup/')->post()->action(
	function($params){
		App::main('PageController', 'createGroup', $params, new DIContainer());
	}
);

// Route to the modifyGroup Method from pagecontroller.php
$this->create('groupmanagerModifyGroup', '/modifyGroup/')->post()->action(
	function($params){
		App::main('PageController', 'modifyGroup', $params, new DIContainer());
	}
);

// Route to the modifyGroup Method from pagecontroller.php
$this->create('groupmanagerDeleteGroup', '/deleteGroup/')->post()->action(
	function($params){
		App::main('PageController', 'deleteGroup', $params, new DIContainer());
	}
);

// Route to the getGroups Method from pagecontroller.php
$this->create('groupmanagerGetGroups', '/getGroups/')->action(
	function($params){
		App::main('PageController', 'getGroups', $params, new DIContainer());
	}
);

// Route to the getGroup Method from pagecontroller.php
$this->create('groupmanagerGetGroup', '/getGroup/')->post()->action(
	function($params){
		App::main('PageController', 'getGroup', $params, new DIContainer());
	}
);
