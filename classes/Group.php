<?php
class Group
{
    private $sqlDataBase;
    private $groupId;
    private $groupName;
    private $description;
    private $departmentId;

	public function __construct(PDO $sqlDataBase)
	{
		$this->sqlDataBase = $sqlDataBase;
        $this->groupName="New Group";
	}
	
	public function __destruct()
	{
		
	}

    /**Add a group to the database and load it in the current object
     * @param $groupName
     * @param $description
     * @param $departmentId
     */
    public function AddGroup($groupName, $description, $departmentId)
	{
		$queryAddGroup = "INSERT INTO groups (group_name, description, department_id)VALUES(:group_name,:description,:department_id)";
        $addGroupPrep = $this->sqlDataBase->prepare($queryAddGroup);
        $addGroupPrep->execute(array(':group_name'=>$groupName,':description'=>$description,':department_id'=>$departmentId));
        $groupId = $this->sqlDataBase->lastInsertId();
        $this->groupName = $groupName;
        $this->description = $description;
        $this->departmentId = $departmentId;
        $this->groupId = $groupId;
	}

    /**Load a group into object from database given a group ID
     * @param $groupId
     */
    public function LoadGroup($groupId)
	{
		$queryGroupInfo = "SELECT * FROM groups WHERE id=:id";
        $groupInfo = $this->sqlDataBase->prepare($queryGroupInfo);
        $groupInfo->execute(array(':id'=>$groupId));
        $groupInfoArr = $groupInfo->fetch(PDO::FETCH_ASSOC);
        $this->groupName = $groupInfoArr['group_name'];
        $this->description = $groupInfoArr['description'];
        $this->departmentId = $groupInfoArr['department_id'];
        $this->groupId = $groupId;
	}

    /**
     * Update group parameters in database
     */
    public function UpdateGroup()
    {
        $queryUpdateGroup = "UPDATE groups SET
                                group_name=:group_name,
                                description=:description,
                                department_id=:department_id
                                WHERE id=:group_id";

        $updateGroup = $this->sqlDataBase->prepare($queryUpdateGroup);
        $updateGroup->execute(array(":group_name"=>$this->groupName,":description"=>$this->description,":department_id"=>$this->departmentId,":group_id"=>$this->groupId));
    }

    /**Get a list of all groups by id and group_name
     * @return array
     */
    public function GetGroupsList()
	{
		$queryGroupList = "SELECT id, group_name FROM groups ORDER BY group_name";
        $groupList = $this->sqlDataBase->query($queryGroupList);
        $groupListArr = $groupList->fetchAll(PDO::FETCH_ASSOC);

        return $groupListArr;
	}

    /**Check if a group exists by groupName
     * @param $groupName
     * @return bool
     */
    public function Exists($groupName)
    {
        $queryGroup = "SELECT COUNT(*) FROM groups WHERE group_name=:group_name";
        $group = $this->sqlDataBase->prepare($queryGroup);
        $group->execute(array(':group_name'=>$groupName));
        $groupCount = $group->fetchColumn();

        if($groupCount)
        {
            return true;
        }
        return false;
    }

    /**Get a list of all group members
     * @return array
     */
    public function GetMembers()
    {
        if($this->getGroupId())
        {
            $user = new User($this->sqlDataBase);
            $groupMembers = $user->GetGroupUsers($this->groupId);
            return $groupMembers;
        }
        return array();
    }


    //Getters and setters for this class

    /**
     * @param mixed $departmentId
     */
    public function setDepartmentId($departmentId)
    {
        $this->departmentId = $departmentId;
    }

    /**
     * @return mixed
     */
    public function getDepartmentId()
    {
        return $this->departmentId;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * @return mixed
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param mixed $groupName
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
    }

    /**
     * @return mixed
     */
    public function getGroupName()
    {
        return $this->groupName;
    }
}

?>