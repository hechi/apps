<?php

namespace OCA\Groupmanager\DependencyInjection;

use \OCA\AppFramework\DependencyInjection\DIContainer as BaseContainer;

use \OCA\Groupmanager\Controller\PageController;
use \OCA\Groupmanager\Controller\ItemController;

use \OCA\Groupmanager\DB\ItemMapper;

class DIContainer extends BaseContainer {

    public function __construct(){
        parent::__construct('groupmanager');

        // use this to specify the template directory
        $this['TwigTemplateDirectory'] = __DIR__ . '/../templates';

        $this['PageController'] = function($c){
            return new PageController($c['API'], $c['Request'],$c['ItemController']);
        };
        
        $this['ItemController'] = function($c){
            return new ItemController($c['API'], $c['Request'],$c['ItemMapper']);
        };
        
        /**
		 * MAPPERS
		 */
		$this['ItemMapper'] = $this->share(function($c){
			return new ItemMapper($c['API']);
		});
    }

}
