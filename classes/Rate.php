<?php
/**
 * Created by PhpStorm.
 * User: nevoband
 * Date: 2/20/14
 * Time: 4:07 PM
 */

class Rate
{
    private $sqlDataBase;
    private $rateId;
    private $rateName;
    private $rateTypeId;

    public function __construct(PDO $sqlDataBase)
    {
        $this->sqlDataBase = $sqlDataBase;
    }

    public function __destruct()
    {

    }

    public function CreateRate($rateName,$rateTypeId)
    {
        $queryAddRate = "INSERT INTO rates (rate_name)VALUES(:rate_name)";
        $addRate = $this->sqlDataBase->prepare($queryAddRate);
        $addRate->execute(array(":rate_name"=>$rateName));

        $this->rateId = $this->sqlDataBase->lastInsertId();

        $device = new Device($this->sqlDataBase);
        $devicesArr = $device->GetDevicesList();


        foreach($devicesArr as $id=>$rateDevice)
        {
            $queryAddRateToDevice = "INSERT INTO device_rate (rate,device_id,rate_id,min_use_time,rate_type_id)VALUES(0,:device_id,:rate_id,0,:rate_type_id)";
            $addRateToDevice = $this->sqlDataBase->prepare($queryAddRateToDevice);
            $addRateToDevice->execute(array(':device_id'=>$rateDevice['id'],':rate_id'=>$this->rateId,':rate_type_id'=>$rateTypeId));
        }

        $this->rateName = $rateName;
        $this->rateTypeId = $rateTypeId;
    }

    public function LoadRate($rateId)
    {
        $queryLoadRate = "SELECT rate_name, rateytpeid FROM rates WHERE id=:rate_id";
        $loadRatePrep = $this->sqlDataBase->prepare($queryLoadRate);
        $loadRatePrep->execute(array(":rate_id"=>$rateId));
        $loadRateArr = $loadRatePrep->fetch(PDO::FETCH_ASSOC);
        if($loadRateArr)
        {
            $this->rateId = $rateId;
            $this->rateName = $loadRateArr['rate_name'];
            $this->rateTypeId = $loadRateArr['rate_type_id'];
        }
    }

    public function UpdateRate()
    {
        $queryUpdateRate = "UPDATE rates SET rate_name=:rate_name, rate_type_id=:rate_type_id";
        $updateRatePrep = $this->sqlDataBase->prepare($queryUpdateRate);
        $updateRatePrep->execute(array(":rate_name"=>$this->rateName,":rate_type_id"=>$this->rateTypeId));
    }

    public function GetRateTypes()
    {
        $queryRateTypes = "SELECT rate_type_name, id FROM rate_types";
        $rateTypes = $this->sqlDataBase->query($queryRateTypes);
        return $rateTypes->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetRates()
    {
        $queryRatesList = "SELECT rate_name, id FROM rates";
        $rateList = $this->sqlDataBase->query($queryRatesList);
        return $rateList->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param mixed $rateTypeId
     */
    public function setRateTypeId($rateTypeId)
    {
        $this->rateTypeId = $rateTypeId;
    }

    /**
     * @return mixed
     */
    public function getRateTypeId()
    {
        return $this->rateTypeId;
    }


    /**
     * @param mixed $rateId
     */
    public function setRateId($rateId)
    {
        $this->rateId = $rateId;
    }

    /**
     * @return mixed
     */
    public function getRateId()
    {
        return $this->rateId;
    }

    /**
     * @param mixed $rateName
     */
    public function setRateName($rateName)
    {
        $this->rateName = $rateName;
    }

    /**
     * @return mixed
     */
    public function getRateName()
    {
        return $this->rateName;
    }

}
?>