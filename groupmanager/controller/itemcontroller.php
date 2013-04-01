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

namespace OCA\Groupmanager\Controller;

use OCA\AppFramework\Controller\Controller;
use OCA\AppFramework\Db\DoesNotExistException;
use OCA\AppFramework\Http\RedirectResponse;

use OCA\Groupmanager\Db\Item;


class ItemController extends Controller {
	

	/**
	 * @param Request $request: an instance of the request
	 * @param API $api: an api wrapper instance
	 * @param ItemMapper $itemMapper: an itemwrapper instance
	 */
	public function __construct($api, $request, $itemMapper){
		parent::__construct($api, $request);
		$this->itemMapper = $itemMapper;
	}


	/**
	 * @CSRFExemption
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 *
	 * Redirects to the index page
	 */
	public function redirectToIndex(){
		$url = $this->api->linkToRoute('groupmanager_index');
		return new RedirectResponse($url);
	}

	/**
	 * @CSRFExemption
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 *
	 * @brief renders the index page
	 * @return an instance of a Response implementation
	 */
	public function getRightContent(){
	        // get the transfered parameters
	        // you must now the name of the parameter
		$id = $this->params('id');
		
		//create an empty array
		$params = array();
		
		if($id=='new'){
        		//if the id is new, than we want to get the template new
			$templateName = 'new';
		}else{
		        //if the id is not new (better a number) than we
		        //want the edit template withe the group who is selected
			$templateName = 'edit';

                        //greate a new item and get the Group from the database   
                        //getGroups is a Method from itemcontroller.php                     
			$item = $this->getGroup($id);
			//create an array with the all information of the group
			$params = array('groupname'=>$item->getGroupname(),
			                'members'=>$item->getMember(),
			                'groupadmin'=>$item->getGroupadmin(),
			                'description'=>$item->getDescription()
			                );
			
		}
		// paint/render the the template with parameters on the website
		return $this->render($templateName, $params,'blank');
	}
	
	/**
	 * @CSRFExemption
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 *
	 * 
	 * @return an instance of a Response implementation
	 */
	public function createGroup(){
	        //create an array with all parameters from the website
	        //createGroup is called from Save Button in the /js/app.js
	        $row = array();
                $row['groupname'] = $this->params('groupname');
                $row['member'] = $this->params('member');
                $row['groupadmin'] = $this->params('groupadmin');
                $row['description'] = $this->params('description');
                
                //create a new Item with all information in the $row array
                $item = new Item($row);
                
                //call the function from the itemMapper who save it into to
                //database
                $this->itemMapper->save($item);                
                
                //TODO print an sucessfull page
                //print a blank page
                return $this->render('new', array(),'blank');
	}

	/**
	 * @CSRFExemption
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 *
	 * 
	 * @return an instance of a Response implementation
	 */
	public function getGroups(){
	        try {
                        //get all Group from the database where the user is
                        //a member       
			$entries = $this->itemMapper->findByUserId($this->api->getUserId());
		} catch (DoesNotExistException $e) {
                        //if there is no group where the user is a member
                        //TODO create nothing or create a usergroup
                        /*
			$item = new Item();
			$item->setGroupname($this->api->getUserId());
			$item->addMember('admin');
			$item->setGroupadmin('john');
			$item->setDescription('empty');
			$this->itemMapper->save($item);
			*/
		}
		$array = array();
		foreach($entries as $entry){
		        $array[]=$entry->getProperties();		
		}
		// give back a jason object that the /js/app.js use to create
		// listItems in the leftcontent
		return $this->renderJSON($array);	
	}
	
	/**
	 * @CSRFExemption
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 *
	 * 
	 * @return an instance of a Response implementation
	 */
	public function getGroup(){
        // get the transfered parameters
        // you must now the name of the parameter
        $id = $this->params('id');
        // get group information from database, depending on the id
		$item = $this->itemMapper->findByGroupId($id);
		// give the item back to the /js/app.js 
		// the script fills the edit.php form from the /templates/edit.php
		return $item;	
	}
	
	
	public function getItemMapper(){
	    return $this->itemMapper;
	}
}
