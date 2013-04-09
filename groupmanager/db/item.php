<?php
/**
* ownCloud - App Template Example
*
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
*/

namespace OCA\Groupmanager\Db;

class Item {

    //Attribute
	private $groupid;
	private $groupname;
	private $member;
	private $groupadmin;
	private $description;

    // contructor that initialized the attributes if there is no parameter
	public function __construct($fromRow=null){
		$this->groupid = 0;
		$this->groupname = "empty";
		$this->member = array();
		$this->groupadmin = array();
		$this->description = "non description";

		if($fromRow){
			$this->fromRow($fromRow);
		}
	}

    // extract all information from the parameter and transfer it to the 
    // attributes
	public function fromRow($row){
        //if there is an parameter with the name groupid
        //it happens if we want to create a new group
        if(isset($row['groupid'])){
	        $this->groupid = $row['groupid'];
        }
		$this->groupname = $row['groupname'];
		//TODO check why, i forget the reason
		if(isset($row['members'])){
	        $list = split(',',$row['members']);
		    foreach( $list as $entry ){
		        //echo "eintries ".$entry."\n";
		        //check if the entry is an empty string
		        if(!(trim($entry)==='')){
		            $this->addGroupmember($entry);
		        }
		    }
        }
		if(isset($row['groupadmin'])){
		    $list = split(',',$row['groupadmin']);
		    foreach( $list as $entry ){
		        //echo "eintries ".$entry."\n";
		        //check if the entry is an empty string
		        if(!(trim($entry)==='')){
		            $this->addGroupadmin($entry);
	            }
		    }
	    }
		$this->description = $row['description'];
	}

// GETTER
	public function getGroupid(){
		return intval($this->groupid);
	}

	public function getGroupname(){
		return $this->groupname;
	}

	public function getMember(){
		return $this->member;
	}
	
	/**
	 * 
	 */	
	public function getMemberStr(){
	    $memberStr = '';
        foreach($this->member as $mem){
            $memberStr = $memberStr.$mem.',';
        }
        return $memberStr;
	}

	public function getGroupadmin(){
		return $this->groupadmin;
	}
	
	/**
	 *
	 */
	public function getGroupadminStr(){
	    $admStr = '';
        foreach($this->groupadmin as $adm){
            $admStr = $admStr.$adm.',';
        }
        return $admStr;
	}
	
	public function getDescription(){
		return $this->description;
	}

    /**
     * put all information in an array and give it back
     * @return array with groupid groupname member groupadmin description
     */        
	public function getProperties(){
	        return array('groupid' => $this->groupid, 
	                     'groupname' => $this->groupname,
	                     'members' => $this->getMemberStr(),
	                     'groupadmin' => $this->getGroupadminStr(),
	                     'description'=> $this->description);	
	}

// SETTER
	public function setGroupid($id){
		$this->groupid = $id;
	}

	public function setGroupname($name){
		$this->groupname = $name;
	}

    //TODO not good to set member better to add and remove members
	public function setGroupmember($memberList){
		$this->member = $memberList;
	}
	
	public function isInGroup($user){
	    return $this->isAdmin($user) || $this->isMember($user);
	}
	
	public function isAdmin($user){
	    return $this->isInArray($this->groupadmin,$user);
	}
	
	public function isMember($user){
	    return $this->isInArray($this->member,$user);
	}
	
	private function isInArray(array $array,$search){
	    $ret = false;
	    foreach($array as $element){
	        if($element === $search){
	            $ret=true;
	        }
	    }
	    return $ret;
	}
	
	/**
	 * Add a userid as a member to the group
	 * @param $user userid which want to be a member of the group
	 */
	public function addGroupmember($user){
	        array_push($this->member,$user);
	}
	
	/**
	 * Remove a userid from the members array
	 * @param $user userid which should be remove from the list
	 */	
	public function removeGroupmember($user){
	       //TODO remove from members array
	       //cautious there should be always the groupadmin in the members
	       //array
	}
	
    //TODO not goot to set groupadmin better to add and remove groupadmins
	public function setGroupadmin($groupAdminList){
		$this->groupadmin = $groupAdminList;
	}

	/**
	 * Add a userid as a admin to the group
	 * @param $user userid which should be a member of the group
	 */	
	public function addGroupadmin($user){
	        array_push($this->groupadmin,$user);
	}
	
	/**
	 * Remove a userid from the groupadmin array
	 * @param $user userid which should be remove from the list
	 */
	public function removeGroupdamin($user){
	        //TODO remove $groupadmin from groupadmin array
	        //cautious there schould be ever at least one groupadmin
	}

	public function setDescription($descr){
		$this->description = $descr;
	}
}
