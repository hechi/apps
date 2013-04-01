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

namespace OCA\Groupmanager\Db;

use \OCA\AppFramework\Core\API;
use \OCA\AppFramework\Db\Mapper;
use \OCA\AppFramework\Db\DoesNotExistException;


class ItemMapper extends Mapper {


	private $tableName;
	private $tableMember; //TODO create it in the database
	private $tableAdmin;  //TODO create it in the database

	/**
	 * initialize the names of the database tables
	 * @param API $api: Instance of the API abstraction layer
	 */
	public function __construct($api){
		parent::__construct($api,'groupmanager_items');
		$this->tableName = '*PREFIX*groupmanager_items';
		$this->tableMember = '*PREFIX*groupmanager_members';
		$this->tableAdmin = '*PREFIX*groupmanager_admins';
	}

        
        //TODO did i need this?
	/**
	 * Finds an item by id
	 * @throws DoesNotExistException: if the item does not exist
	 * @return the item
	 */
//	public function find($id){
//		$row = $this->findQuery($this->tableName, $id);
//		return new Item($row);
//	}


	/**
	 * Finds all groups where the user is a member or admin of by user id
	 * @param string $userId: the id of the user 
	 * @throws DoesNotExistException: if the group does not exist
	 * @return list of groups
	 */
	public function findByUserId($userId){
        //TODO make a little bit more sql to find groups where the user is a member and admin of
		$sql = 'SELECT * FROM `' . $this->tableName . '`';// WHERE `groupadmin` = ?';
		$params = array($userId);

		$result = $this->execute($sql);
		
		$entityList = array();
		while($row = $result->fetchRow()){
			$entity = new Item($row);
			array_push($entityList, $entity);
		}
		return $entityList;
	}

	/**
	 * Finds a group by group id
	 * @param string $groupid: the id of the group that we want to find
	 * @throws DoesNotExistException: if the group does not exist
	 * @return the groupitem
	 */
	public function findByGroupId($groupId){
	    //TODO make a little bit more sql to get all entries
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
		$entity->setGroupadmin($groupadmins);
		$entity->setGroupmember($groupmembers);

		return $entity;
	}

        //TODO did i need this?
	/**
	 * Finds all Items
	 * @return array containing all items
	 */
//	public function findAll(){
//		$result = $this->findAllQuery($this->tableName);
//
//		$entityList = array();
//		while($row = $result->fetchRow()){
//			$entity = new Item($row);
//			array_push($entityList, $entity);
//		}
//
//		return $entityList;
//	}


	/**
	 * Saves an groupitem into the database
	 * @param Item $group: the groupitem to be saved
	 * @return the item with the filled in id
	 */
	public function save($item){
        //TODO more sql magic to but it in the right tables
		$sqlGroup = 'INSERT INTO `'. $this->tableName . '`(`groupname`, `description`)'.
				' VALUES(?, ?)';
		$sqlGroupadmins = 'INSERT INTO `'. $this->tableAdmin . '`(`groupid`, `admin`)'.
		        ' VALUES(?, ?)';
		$sqlGroupmembers = 'INSERT INTO `'. $this->tableMember . '`(`groupid`, `member`)'.
		        ' VALUES(?, ?)';       
		        
		$paramsGroup = array(
			$item->getGroupname(),
			$item->getDescription()
		);

		$this->execute($sqlGroup, $paramsGroup);
		$item->setGroupid($this->api->getInsertId($this->tableName));

		foreach( $item->getGroupadmin() as $admin){
	        //create parameter list
	        $paramsGroupadmin = array(
	            $item->getGroupid(),
	            $admin
	        );
	        // fire sql query to the database
	        $this->execute($sqlGroupadmins,$paramsGroupadmin);
	    }
	    foreach( $item->getMember() as $member){
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
	 * TODO if we modify a group
	 * Updates an item
	 * @param Item $item: the item to be updated
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
	    
	    foreach( $item->getGroupadmin() as $admin){
	        //create parameter list
	        $paramsGroupadmin = array(
	            $item->getGroupid(),
	            $admin
	        );
	        // fire sql query to the database
	        $this->execute($sqlGroupadmins,$paramsGroupadmin);
	    }
	    foreach( $item->getMember() as $member){
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
	 * Deletes a group
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
