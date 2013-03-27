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
		parent::__construct($api);
		$this->tableName = '*PREFIX*groupmanager_items';
		//TODO
		//$this->tableMember='*PREFIX*groupmanager_member';
		//$this->tableAdmin='*PREFIX*groupmanager_admin';
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
		$sql = 'SELECT * FROM `' . $this->tableName . '` WHERE `groupadmin` = ?';
		$params = array($userId);

		$result = $this->execute($sql, $params);
		
		$entityList = array();
		while($row = $result->fetchRow()){
			$entity = new Item($row);
			array_push($entityList, $entity);
		}
		return $entityList;
	}

	/**
	 * Finds an group by group id
	 * @param string $groupid: the id of the group that we want to find
	 * @throws DoesNotExistException: if the group does not exist
	 * @return the groupitem
	 */
	public function findByGroupId($groupId){
		$sql = 'SELECT * FROM `' . $this->tableName . '` WHERE `groupid` = ?';
		$params = array($groupId);

		$result = $this->execute($sql, $params);
		
		$row = $result->fetchRow();
		
		$entity = new Item($row);

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
		$sql = 'INSERT INTO `'. $this->tableName . '`(`groupadmin`, `groupname`, `description`)'.
				' VALUES(?, ?, ?)';

		$params = array(
			$item->getGroupadmin(),
			$item->getGroupname(),
			$item->getDescription()
		);

		$this->execute($sql, $params);

		$item->setGroupid($this->api->getInsertId($this->tableName));
	}


	/**
	 * TODO if we modify an group
	 * Updates an item
	 * @param Item $item: the item to be updated
	 */
	public function update($item){
		$sql = 'UPDATE `'. $this->tableName . '` SET
				`groupid` = ?,
				`groupadmin` = ?,
				`groupname` = ?,
				`description` = ?
				WHERE `groupid` = ?';

		$params = array(
			$item->getGroupid(),
			$item->getGroupadmin(),
			$item->getGroupname(),
			$item->getDescription()
		);

		$this->execute($sql, $params);
	}


	/**
	 * TODO if we want to delete a group
	 * Deletes an item
	 * @param int $id: the id of the item
	 */
	public function delete($id){
		$this->deleteQuery($this->tableName, $id);
	}


}
