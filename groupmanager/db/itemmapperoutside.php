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

class ItemMapperOutside{

   	private $tableName;
	private $tableMember; //TODO create it in the database
	private $tableAdmin;  //TODO create it in the database
	
	/**
	 * initialize the names of the database tables
	 * @param API $api: Instance of the API abstraction layer
	 */
	public function __construct(){
		$this->tableName = '*PREFIX*groupmanager_items';
		$this->tableMember = '*PREFIX*groupmanager_members';
		$this->tableAdmin = '*PREFIX*groupmanager_admins';
	}
	
	/**
    * Used to abstract the owncloud database access away
    * @param string $sql the sql query with ? placeholder for params
    * @param int $limit the maximum number of rows
    * @param int $offset from which row we want to start
    * @return \OCP\DB a query object
    */
    public function prepareQuery($sql, $limit=null, $offset=null){
        return \OCP\DB::prepare($sql, $limit, $offset);
    }
	
	/**
    * Runs an sql query
    * @param string $sql the prepare string
    * @param array $params the params which should replace the ? in the sql query
    * @param int $limit the maximum number of rows
    * @param int $offset from which row we want to start
    * @return \PDOStatement the database query result
    */
    protected function execute($sql, array $params=array(), $limit=null, $offset=null){
        $query = $this->prepareQuery($sql, $limit, $offset);
        return $query->execute($params);
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
	    //TODO OLD
        //TODO make a little bit more sql to find groups where the user is a member and admin of
        /*
		$sql = 'SELECT * FROM `' . $this->tableName . '`';// WHERE `groupadmin` = ?';
		$params = array($userId);

		$result = $this->execute($sql);
		
		$entityList = array();
		while($row = $result->fetchRow()){
			$entity = new Item($row);
			array_push($entityList, $entity);
		}
		*/
		return $entityList;
	}
	
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
	 * @throws DoesNotExistException: if the group does not exist
	 * @return the groupitem
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
		$entity->setGroupadmin($groupadmins);
		$entity->setGroupmember($groupmembers);

		return $entity;
	}

	/**
	 * Finds all Items
	 * @return array containing all items
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
