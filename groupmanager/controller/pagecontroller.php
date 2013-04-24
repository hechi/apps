<?php

/**
* @author Andreas Hechenberger
* @copyright 2012 Andreas Hechenberger oc@hechenberger.me
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
* 
* The pagecontroller is the connection between the javascript (app.js) and the
* database (itemmapper).
*/

namespace OCA\Groupmanager\Controller;

// import the AppFramwork classes
use \OCA\AppFramework\Controller\Controller;
use \OCA\AppFramework\Db\DoesNotExistException;
use \OCA\AppFramework\Core\API;
use \OCA\AppFramework\Http\Request;

// import the ItemController to get the Itemmapper
use \OCA\Groupmanager\Controller\ItemController;

// import the Item, that represents the Entry in the database
use OCA\Groupmanager\Db\Item;

class PageController extends Controller {

    /* Attribute */
    private $itemController;
    private $itemMapper;

    /**
     * Constructor of the PageController
     * initialize the attribute
     */
    public function __construct($api, $request, $itemController){
        parent::__construct($api, $request);
        $this->itemController=$itemController;
        $this->itemMapper=$this->itemController->getItemMapper();
        
        $this->initAdminSettings();
    }
    
    /**
     * @CSRFExemption
     * @IsAdminExemption
     * @IsSubAdminExemption
     *
     * Redirects to the index page
     */
    public function redirectToIndex(){
            $url = $this->api->linkToRoute('groupmanagerIndex');
            return new RedirectResponse($url);
    }


    /**
     * Prints the index page of Groupmanager
     *
     * @CSRFExemption
     * @IsAdminExemption
     * @IsSubAdminExemption
     */
    public function index(){
        // loads the stylesheets from css directory
		$this->api->addStyle('style'); //style = /css/style.css
		$this->api->addStyle('animation'); //animation = /css/animation.css

        // loads the script from the js directory
		$this->api->addScript('app'); //app = /js/app.js
        
        //templateName is the name of the Template in /templates
		$templateName = 'main';
		// create a array with parameters if need
		$params = array();		
		// paint/render the the template with parameters on the website
		return $this->render($templateName, $params);
    }
    
	/**
	 * Prints the right content of the index page
	 * If 
	 *   one clicks the new button it prints the new.php page and shows
	 *   a table of a new group form
	 * else
	 *   one clicks an existing group entry from the left side, it prints
	 *   the edit.php form with the entries from the database
	 * 
	 *
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
			//check if the user is a admin, write true in permission if 
			//he is an admin, else write false
			$permission=($item->isAdmin($this->api->getUserId()))?'true':'false';
			
			$params = array('groupname'=>$item->getGroupname(),
			                'members'=>$item->getMemberStr(),
			                'groupadmin'=>$item->getGroupadminStr(),
			                'description'=>$item->getDescription(),
			                'permission'=>$permission,
			                'groupcreator'=>$item->getGroupcreator(),
			                'memberJSON'=>json_encode($item->getMemberArray()),
			                'adminJSON'=>json_encode($item->getAdminArray()),
			                );
			
		}
		// paint/render the the template with parameters on the website
		return $this->render($templateName, $params,'blank');
	}
	
	/**
	 * Saves the entries from the new.php form into the database
	 * 
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
	        
	        // get the parameter from the website
	        // will be sended by the js/app.js
			$memStr = $this->params('memberList');
			$admStr = $this->params('adminList');

			// the first parameter is the memberList as string
			// the second parameter says that the result will be an 
			// associative array
			$memberList = json_decode($memStr,TRUE);
            $adminList = json_decode($admStr,TRUE);
            
			//echo "inhalt:".$memStr."\ndump:".var_dump($memberList)."\n";
			//echo "inhalt:".$admStr."\ndump:".var_dump($adminList);
			
            $row['groupname'] = $this->params('groupname');
            //$row['members'] = $this->params('members');
            //$row['groupadmin'] = $this->params('groupadmin');
            $row['description'] = $this->params('description');
            $row['groupcreator'] = $this->api->getUserId();
            
            //create a new Item with all information in the $row array
            $item = new Item($row);
            
            //add all members and admins to the item
            foreach($memberList as $mem){
			    $item->addMember($mem);
			}
			
			foreach($adminList as $adm){
			    $item->addAdmin($adm);
			}
            
            //call the function from the itemMapper who save it into to
            //database
            $this->itemMapper->save($item);    
            
            //send a notification back
            $params = array(
                        'notification' => 'saved',
                        );
                        
            //TODO print an sucessfull page
            //print a blank page
            return $this->render('new', array(),'blank');
	}
	
	/**
	 * Modifie the entry with the passed id from the edit.php into the database
	 * 
	 * @CSRFExemption
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 *
	 * 
	 * @return an instance of a Response implementation
	 */
	public function modifyGroup(){
	    $templateName = 'edit';

        //create an array with all parameters from the website
        //createGroup is called from Save Button in the /js/app.js
        $row = array();
        $row['groupid'] = $this->params('id');
        $row['groupname'] = $this->params('groupname');
        //$row['members'] = $this->params('members');
        //$row['groupadmin'] = $this->params('groupadmin');
        $row['description'] = $this->params('description');
        $row['groupcreator'] = $this->params('creator');
        
        // get the parameter from the website
        // will be sended by the js/app.js
        $memStr = $this->params('memberList');
		$admStr = $this->params('adminList');

		// the first parameter is the memberList as string
		// the second parameter says that the result will be an 
		// associative array
		$memberList = json_decode($memStr,TRUE);
        $adminList = json_decode($admStr,TRUE);
	    
	    $params = $row;
	    $params['notification'] = 'modified';
	    
	    //create a new Item with all information in the $row array
        $item = new Item($row);
        
        //add all members and admins to the item
        foreach($memberList as $mem){
		    $item->addMember($mem);
		    echo "memberadd: ".$mem;
		}
		
		foreach($adminList as $adm){
		    $item->addAdmin($adm);
		    echo "adminadd: ".$adm;
		}
        
        //call the function from the itemMapper who save it into to
        //database
        $this->itemMapper->update($item);
        
	    return $this->render($templateName,$params,'blank');
	
	}
	
	/**
	 * Delete the entry with the passed parameter from the app.js 
	 * from the database
	 * 
	 * @CSRFExemption
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 *
	 * 
	 * @return an instance of a Response implementation
	 */
	public function deleteGroup(){
	    $templateName = 'new';

	    $this->itemMapper->deleteByGroupId($this->params('id'));
	    
	    $params = array();
	    
	    return $this->render($templateName,$params,'blank');
	}

	/**
	 * Get all groups where the user is a member or admin
	 * 
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
		// get all informations from the entries
		foreach($entries as $entry){
		        $array[]=$entry->getProperties();
		}
		// give back a jason object that the /js/app.js use to create
		// listItems in the leftcontent
		return $this->renderJSON($array);	
	}
	
	/**
	 * Get a single entry with the passed id from the app.js
	 * 
	 * @CSRFExemption
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 *
	 * 
	 * @return a single groupitem
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
	
	/************************************************************************** 
	                ADMIN SETTINGS                 
	 **************************************************************************/
	  
    /**
     * If there es no entry in the /config/config.php for the groupmanager
     * admin setting. Then create some.
     */
    private function initAdminSettings(){
        $unique = $this->getUniqueGroupIdSetting();
        $autocomp = $this->getAutocompletionSetting();
        // check if the attributes are in the /config/config.php file
        // if not set it to the default values
        if($unique===''){
            $this->setUniqueGroupIdSetting(false);
        }
        if($autocomp===''){
            $this->setAutocompletionSetting(true);
        }
    }
	
    /**
     * Get the value of the uniqueGroupId from the /config/config.php
     * @return bool: Returns True if The Value is Yes, otherwise False
     */
    private function getUniqueGroupIdSetting(){
        $value = $this->getSettingByName('groupmanagerUniqueGroupId');
        return $value;
    }
    
    /**
     * Get the value of the autocompletionBox from the /config/config.php
     * @return bool: Returns True if The Value is Yes, otherwise False
     */
    private function getAutocompletionSetting(){
        $value = $this->getSettingByName('groupmanagerAutocompletionBox');
        return $value;
    }
    
    /**
     * Get a value of the settingAttribute from the /config/config.php
     * @param $key: settingAttribute in the /config/config.php
     * @return string: Returns the string of the /config/config.php
     */
    private function getSettingByName($key){
        return $this->api->getSystemValue($key);
    }
    
    /**
     * Set the settingAttribute of the uniqueGroupId into the /config/config.php
     * file
     * @param $value bool: set the attribute
     */    
    private function setUniqueGroupIdSetting($value){
        $this->setSettingByName('groupmanagerUniqueGroupId',$value);
    }
    
    /**
     * Set the settingAttribute of the autocompletionBox into the /config/config.php
     * file
     * @param $value bool: set the attribute
     */
    private function setAutocompletionSetting($value){
        $this->setSettingByName('groupmanagerAutocompletionBox',$value);
    }
    
    /**
     * Set value of the given settingAttribute into the /config/config.php
     * file
     * @param $key string: name of the attribute
     * @param $value : value to set
     */
    private function setSettingByName($key,$value){
         $this->api->setSystemValue($key,$value);
    }
    
    /**
     * Creat an entry in the admin panel
     *
     * @CSRFExemption
     */
    public function adminSettings(){
        // loads the stylesheets from css directory
		$this->api->addStyle('style'); //style = /css/style.css
		$this->api->addStyle('animation'); //animation = /css/animation.css

        //templateName is the name of the Template in /templates
		$templateName = 'part.settings';
		
		// get system value from the /config/config.php file
		$unique = $this->getUniqueGroupIdSetting();
        $autocomp = $this->getAutocompletionSetting();
        
        // check if there is a Yes then but a checked in the variable
        // otherwise do nothing in it
        if($unique){
            $unique = 'checked';
        }else{
            $unique = '';
        }        
        if($autocomp){
            $autocomp = 'checked';
        }else{
            $autocomp = '';
        }
		
		// create a array with parameters if need
		$params = array(
		        'uniqueGroupIdCheck' => $unique,
		        'autocompCheck' => $autocomp);
	
		// paint/render the the template with parameters on the website
		return $this->render($templateName, $params,'admin');
    }
    
	/**
     * saves the settings to the configuration file /config/config.php
     *
     * @CSRFExemption
     */
    public function saveSettings(){
		
		$unique = '';
		$autocomp = '';
		
		if($this->params('groupIdBox')==='on'){
		    $this->setUniqueGroupIdSetting(true);
		    $unique = 'checked';
		}else{
		    $this->setUniqueGroupIdSetting(false);
		}
		
		if($this->params('autocompletionBox')==='on'){
		    $this->setAutocompletionSetting(true);
		    $autocomp = 'checked';
		}else{
		    $this->setAutocompletionSetting(false);
		}
		
		// create a array with parameters if need
		$params = array(
		            'uniqueGroupIdCheck' => $unique,
		            'autocompCheck' => $autocomp,
		            'notification'=>'saved');		
		// give back all information to the website as an JSON Object
		return $this->renderJSON($params);
    }
    
    
    /**
     * returns the settings from the configuration file /config/config.php
     *
     * @CSRFExemption
     */
    public function getSettings(){
        
        // get system value from the /config/config.php file
		$unique = $this->getUniqueGroupIdSetting();
        $autocomp = $this->getAutocompletionSetting();
        
        // create a array with parameters if need
		$params = array(
		        'uniqueGroupIdCheck' => $unique,
		        'autocompCheck' => $autocomp);
		        
        // give back all information to the website as an JSON Object
		return $this->renderJSON($params);
    }
    
    /**
     * returns a number of users
     *
     * @CSRFExemption
 	 * @IsAdminExemption
	 * @IsSubAdminExemption
     */
    public function getUsers(){
        // get the given searchString from the url
        // send by js/app.js
        $searchString = $this->params('searchString');
        
        //\OCP\User::getUsers($search = '', $limit = null, $offset = null);
        $users = \OCP\User::getUsers($searchString, $limit=4, 4);
        
        // create a array with parameters if need
		$params = array();
		// check for usernames with the searchstring
        foreach($users as $user){
            // check if the username contains the searchString
            if(stripos($user,$searchString)!==false){
                // push the user in the return array
                array_push($params,$user);
            }
        }
		        
        // give back all information to the website as an JSON Object
		return $this->renderJSON($params);
    }
	
}
