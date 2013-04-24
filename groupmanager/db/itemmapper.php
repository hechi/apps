<?php
/**
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
* The ItemMapper maps the Item class into the database.
* It make all the SQL Magic to communicate with the database.
*/

namespace OCA\Groupmanager\Db;

// import important AppFramework classes
use \OCA\AppFramework\Core\API;
use \OCA\AppFramework\Db\Mapper;
use \OCA\AppFramework\Db\DoesNotExistException;


class ItemMapper extends Mapper {

    /* Attribute */
	private $tableName;
	private $tableMember; 
	private $tableAdmin; 
	
	/**
	 * initialize the names of the database tables
	 * @param API $api: Instance of the API abstraction layer
	 */
	public function __construct($api=null){
	    parent::__construct($api,'groupmanager_items');
	    // the *PREFIX* stands for the dabasename that every admin take
		$this->tableName = '*PREFIX*groupmanager_items';
		$this->tableMember = '*PREFIX*groupmanager_members';
		$this->tableAdmin = '*PREFIX*groupmanager_admins';
	}

	/**
	 * Finds all groups where the user is a member or admin of by user id
	 * @param string $userId: the id of the user 
	 * @throws DoesNotExistException: if the group does not exist
	 * @return list of groups
	 */
	public function findByUserId($userId){
	    //create sql querys to select all groupid's in admin and member table
	    $sqlGroupadmin = 'SELECT groupid FROM `'.$this->tableAdmin.'` 
	                        WHERE `admin` = ?';
	    $sqlGroupmember = 'SELECT groupid FROM `'.$this->tableMember.'` 
	                        WHERE `member` = ?';
	    
	    $userToSearch = array($userId);
	    
	    //fire sql query on database
	    $resultGroupadmin = $this->execute($sqlGroupadmin,$userToSearch);
	    $resultGroupmember = $this->execute($sqlGroupmember,$userToSearch);
	    
        //create an array with groupids
        //check for double values, because we dont want dublicated entries
	    $selectedGroupids=array();
	    
	    while($row = $resultGroupadmin->fetchRow()){
	        if(!in_array($row['groupid'],$selectedGroupids)){
	            array_push($selectedGroupids,$row['groupid']);
	        }
	    }
	    while($row = $resultGroupmember->fetchRow()){
	        if(!in_array($row['groupid'],$selectedGroupids)){
	            array_push($selectedGroupids,$row['groupid']);
	        }
	    }
	    
	    //create sql querys and fire in loop to database, to get all
	    //groups where the user is a member or an admin of
	    $sql = 'SELECT * FROM `'.$this->tableName.'` WHERE `groupid` = ?';
	    //an entityList for the groups
	    $entityList = array();
	    foreach($selectedGroupids as $groupid){
	        $params = array($groupid);
	        $result = $this->execute($sql,$params);
	        $entity = new Item($result->fetchRow());
	        array_push($entityList,$entity);
        }	
		return $entityList;
	}
	
	/**
	 * Check if a passed groupname is already taken
	 * @param string $groupname: check for groupname already exists
	 * @return bool if the groupname is already taken it returns true, 
	 *              else false
	 */
	public function groupnameExists($groupname){
	    $sql = 'SELECT * FROM `' . $this->tableName . '` WHERE `groupname` = ?';
	    $params = array($groupname);
	    $result = $this->execute($sql, $params);
	    $row = $result->fetchRow();
	    if($row==null){
	        return false;
	    }else{
	        return true;
	    }
	}
	
	/**
	 * Search for a group identify by the groupname
	 * @param string $groupname: groupname that identify the group
	 * @return Item returns the group
	 */
	public function findGroupByName($groupname){
	    $sql = 'SELECT * FROM `' . $this->tableName . '` WHERE `groupname` = ?';
	    $params = array($groupname);
	    $result = $this->execute($sql, $params);
	    $row = $result->fetchRow();
	    $groupid = $row['groupid'];
	    
	    return $this->findByGroupId($groupid);
	}

	/**
	 * Finds a group by group id
	 * @param string $groupid: the id of the group that we want to find
	 * @return Item returns the group
	 */
	public function findByGroupId($groupId){
		$sql = 'SELECT * FROM `' . $this->tableName . '` WHERE `groupid` = ?';
		$sqlGroupadmin = 'SELECT admin FROM `'. $this->tableAdmin. '` WHERE `groupid` = ?';
		$sqlGroupmember = 'SELECT member FROM `'. $this->tableMember. '` WHERE `groupid` = ?';
		
		$params = array($groupId);

		$result = $this->execute($sql, $params);
		$resultGroupadmin = $this->execute($sqlGroupadmin,$params);
		$resultGroumember = $this->execute($sqlGroupmember,$params);
		
		$group = $result->fetchRow();
		
		$groupadmins = array();
		while($row = $resultGroupadmin->fetchRow()){
		    //echo $row['admin'];
			array_push($groupadmins, $row['admin']);
		}
		
		$groupmembers = array();
		while($row = $resultGroumember->fetchRow()){
		    //echo $row['member'];
			array_push($groupmembers, $row['member']);
		}
		$entity = new Item($group);
		$entity->setAdmin($groupadmins);
		$entity->setMember($groupmembers);

		return $entity;
	}

	/**
	 * Finds all Items
	 * @return array of Item containing all items
	 */
	public function findAll(){
		$sql = 'SELECT groupid FROM `' . $this->tableName . '`';
		$sqlGroupadmin = 'SELECT admin FROM `'. $this->tableAdmin. '` WHERE `groupid` = ?';
		$sqlGroupmember = 'SELECT member FROM `'. $this->tableMember. '` WHERE `groupid` = ?';
		
		$params = array($groupId);

		$result = $this->execute($sql, $params);
		
		//$groups = array();
		$groupList = array();
		
		while($row = $result->fetchRow()){
		    //array_push($groups,$row['groupid']);		
		    $item = $this->findByGroupId($row['groupid']);
		    array_push($groupList,$item);
		}
		return $groupList;
	}


	/**
	 * Saves an groupitem into the database
	 * @param Item $group: the groupitem to be saved
	 * @return the item with the filled in id
	 */
	public function save($item){
        //TODO more sql magic to but it in the right tables
		$sqlGroup = 'INSERT INTO `'. $this->tableName . '`(`groupname`, `description`,`groupcreator`)'.
				' VALUES(?, ?, ?)';
		$sqlGroupadmins = 'INSERT INTO `'. $this->tableAdmin . '`(`groupid`, `admin`)'.
		        ' VALUES(?, ?)';
		$sqlGroupmembers = 'INSERT INTO `'. $this->tableMember . '`(`groupid`, `member`)'.
		        ' VALUES(?, ?)';       
		        
		$paramsGroup = array(
			$item->getGroupname(),
			$item->getDescription(),
			$item->getGroupcreator()
		);

		$this->execute($sqlGroup, $paramsGroup);
		$item->setGroupid($this->api->getInsertId($this->tableName));

		foreach( $item->getAdminArray() as $admin){
	        //create parameter list
	        $paramsGroupadmin = array(
	            $item->getGroupid(),
	            $admin
	        );
	        // fire sql query to the database
	        $this->execute($sqlGroupadmins,$paramsGroupadmin);
	    }
	    foreach( $item->getMemberArray() as $member){
	        //create parameter list
	        //echo ' members ->'.$member;
	        $paramsGroupmembers = array(
	            $item->getGroupid(),
	            $member
	        );
	        // fire sql query to the database
	        $this->execute($sqlGroupmembers,$paramsGroupmembers);
	    }
	}


	/**
	 * Modify the database with the given parameter
	 * Method reads all informations from the parameter and 
	 * Modifies the database
	 * @param Item $item: the item to be updated
	 * 
	 * TODO: write these in BA
	 * CAUTION: the creator can not be modified!
	 */
	public function update($item){
		$sqlGroup = 'UPDATE `'. $this->tableName . '` SET
				`groupname` = ?,
				`description` = ?
				WHERE `groupid` = ?';
				
		//TODO is this to dirty ?
		// 1.) delete all admins and members connected to the groupid
		// 2.) insert all admins and members
		$sqlGroupadmin = 'DELETE FROM `'.$this->tableAdmin.'` WHERE `groupid` = ?';
		$sqlGroupmember = 'DELETE FROM `'.$this->tableMember.'` WHERE `groupid` = ?';

		$paramsGroup = array(
			$item->getGroupname(),
			$item->getDescription(),
			$item->getGroupid()
		);
		
		$params = array($item->getGroupid());
        
        //update Groupinformation
		$this->execute($sqlGroup, $paramsGroup);
		// 1.) delete all admins and members
		$this->execute($sqlGroupadmin,$params);
		$this->execute($sqlGroupmember,$params);
		
		// 2.) insert all admins and members
		$sqlGroupadmins = 'INSERT INTO `'. $this->tableAdmin . '`(`groupid`, `admin`)'.
		        ' VALUES(?, ?)';
		$sqlGroupmembers = 'INSERT INTO `'. $this->tableMember . '`(`groupid`, `member`)'.
		        ' VALUES(?, ?)';
	    
	    foreach( $item->getAdminArray() as $admin){
	        //create parameter list
	        $paramsGroupadmin = array(
	            $item->getGroupid(),
	            $admin
	        );
	        // fire sql query to the database
	        $this->execute($sqlGroupadmins,$paramsGroupadmin);
	    }
	    foreach( $item->getMemberArray() as $member){
	        //create parameter list
	        //echo ' members ->'.$member;
	        $paramsGroupmembers = array(
	            $item->getGroupid(),
	            $member
	        );
	        // fire sql query to the database
	        $this->execute($sqlGroupmembers,$paramsGroupmembers);
	    }	
	}


	/**
	 * Deletes a group on tha passed id
	 * @param int $id: the id of the item
	 */
	public function deleteByGroupId($id){
		$sqlGroup = 'DELETE FROM `'.$this->tableName. '` WHERE `groupid` = ? ';
		$sqlGroupadmin = 'DELETE FROM `'.$this->tableAdmin. '` WHERE `groupid` = ? ';
		$sqlGroupmember = 'DELETE FROM `'.$this->tableMember. '` WHERE `groupid` = ? ';
	
	    $params = array($id);
	
	    $this->execute($sqlGroup,$params);
	    $this->execute($sqlGroupadmin,$params);
	    $this->execute($sqlGroupmember,$params);
	}
}
