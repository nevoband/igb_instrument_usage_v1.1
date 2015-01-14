<?php

class Bills
{
    private $sqlDataBase;
    private $groupBy;
    private $userId;
    private $deviceId;

    const CONTINUOUS_RATE = 1;
    const MONTHLY_RATE = 2;
    const GROUP_CFOP = 1;
    const GROUP_USER = 2;
    const GROUP_DEVICE = 3;

    public function __construct(PDO $sqlDataBase)
    {
        $this->sqlDataBase = $sqlDataBase;
        $this->groupBy = 0; //Do not group by default
        $this->userId = 0;
        $this->deviceId = 0;
    }

    public function __destruct()
    {

    }

    /**
     * Verify a session by id
     * @param $billId
     */
    public function VerifyBill($billId)
    {
        $session = new Session($this->sqlDataBase);
        $session->LoadSession($billId);
        $session->Verify();
    }

    /**
     * Return one month of bills
     * @param $year
     * @param $month
     * @param $rateType
     * @return mixed
     */
    public function GetMonthCharges($year, $month, $rateType)
    {
        $pdoParameters = array();
        $querySelectClauseMonthUsage = "SELECT s.id, uc.cfop,s.cfop_id, s.rate, s.user_id, s.device_id,  d.full_device_name, u.user_name, s.start, s.stop,CONCAT(u.first, ', ', u.last) as full_name, s.description,r.rate_name, dr.min_use_time, g.group_name  ";
        $queryTablesClauseMonthUsage = " FROM device_rate dr, device d, users u, rates r, groups g,session s LEFT JOIN user_cfop uc ON uc.id=s.cfop_id";
        $queryWhereClauseMonthUsage =" WHERE d.id = s.device_id AND dr.device_id = d.id AND g.id=u.group_id AND dr.rate_id = u.rate_id AND u.id=s.user_id AND r.id=u.rate_id AND MONTH(start)=:month AND YEAR(start)=:year AND dr.rate_type_id=:rate_type_id";
        $pdoParameters[':year'] = $year;
        $pdoParameters[':month'] = $month;
        $pdoParameters[':rate_type_id'] = $rateType;

        if ($this->userId) {
            $queryWhereClauseMonthUsage .= " AND s.user_id=:user_id";
            $pdoParameters[':user_id'] = $this->userId;
        }

        if ($this->deviceId) {
            $queryWhereClauseMonthUsage .= " AND s.device_id=:device_id";
            $pdoParameters[':device_id'] = $this->deviceId;
        }

        switch ($this->groupBy) {
            case self::GROUP_CFOP:
                $queryWhereClauseMonthUsage .= " GROUP BY s.cfop";
                $querySelectClauseMonthUsage .= ", s.elapsed";
                break;
            case self::GROUP_DEVICE:
                $querySelectClauseMonthUsage .= ", SUM(s.elapsed) as elapsed";
                $queryWhereClauseMonthUsage .= " GROUP BY s.device_id";
                break;
            case self::GROUP_USER:
                $queryWhereClauseMonthUsage .= " GROUP BY s.user_id";
                $querySelectClauseMonthUsage .= ", s.elapsed";
                break;
            default:
                $querySelectClauseMonthUsage .= ", s.elapsed";
        }

        $queryMonthUsagePrepare = $this->sqlDataBase->prepare($querySelectClauseMonthUsage.$queryTablesClauseMonthUsage.$queryWhereClauseMonthUsage, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $queryMonthUsagePrepare->execute($pdoParameters);
        $monthUsage = $queryMonthUsagePrepare->fetchAll(PDO::FETCH_ASSOC);

        return $monthUsage;
    }


    /**Return available months for billing
     * @return mixed
     */
    public function GetAvailableBillingMonths()
    {
        $queryAvailableMonths = "SELECT distinct DATE_FORMAT(start,'%M %Y') as mon_yr, MONTH(start) AS month, YEAR(start) AS year FROM session ORDER BY start DESC";
        $availableMonths = $this->sqlDataBase->query($queryAvailableMonths);
        return $availableMonths;
    }

    /**Get excel file from year month and rate type
     * @param $year
     * @param $month
     * @param $rateType
     */
    public function GetExcelCharges($year, $month, $rateType)
    {
        $monthUsageArr = $this->GetMonthCharges($year, $month, $rateType);
        Report::create_excel_2007_report($monthUsageArr, "facilities_billing");
    }

    /**Calculate total to bill for given rate type
     * @param $elapsed
     * @param $rateTypeId
     * @param $rate
     * @param $min_use_time
     * @return mixed
     */
    public function CalcTotal($elapsed, $rateTypeId, $rate, $min_use_time)
    {
        switch($rateTypeId)
        {
            case Bills::CONTINUOUS_RATE:
                $total = $this->CalcContinuous($elapsed,$rate,$min_use_time);
                break;
            case Bills::MONTHLY_RATE:
                $total = $this->CalcMonthly($elapsed,$rate,$min_use_time);
                break;
        }

        return $total;
    }

    /**Calculate continuous rate
     * @param $elapsed
     * @param $rate
     * @param $min_use_time
     * @return mixed
     */
    private function CalcContinuous($elapsed,$rate,$min_use_time)
    {
        if($min_use_time > $elapsed)
        {
            $elapsed = $min_use_time;
        }

        return $elapsed * $rate;
    }

    /**Calculate monthly rate
     * @param $elapsed
     * @param $rate
     * @param $min_use_time
     * @return mixed
     */
    private function CalcMonthly($elapsed, $rate, $min_use_time)
    {
        return $rate * 60;
    }
    /**Set which column you would like to group the bills by
     * Options available are GROUP_CFOP, GROUP_USER, GROUP_DEVICE
     * @param mixed $groupBy
     */
    public function setGroupBy($groupBy)
    {
        $this->groupBy = $groupBy;
    }


    /**
     * @param mixed $deviceId
     */
    public function setDeviceid($deviceId)
    {
        $this->deviceId = $deviceId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
}


?>
