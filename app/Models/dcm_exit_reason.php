<?php
class DCM_exit_reason extends Doctrine_Record {

	public function setTableDefinition() {
		$this -> hasColumn('Name', 'varchar', 50);
		$this -> hasColumn('Active', 'varchar', 2);
	}

	public function setUp() {
		$this -> setTableName('dcm_exit_reason');
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("dcm_exit_reason") -> where("Active", "1");
		$purposes = $query -> execute();
		return $purposes;
	}
	public function getAllHydrated() {
		$query = Doctrine_Query::create() -> select("*") -> from("dcm_exit_reason") -> where("Active", "1");
		$purposes = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $purposes;
	}

	public function getTotalNumber() {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Purposes") -> from("dcm_exit_reason");
		$total = $query -> execute();
		return $total[0]['Total_Purposes'];
	}

	public function getPagedPurposes($offset, $items) {
		$query = Doctrine_Query::create() -> select("Name") -> from("dcm_exit_reason") -> offset($offset) -> limit($items);
		$purpose = $query -> execute();
		return $purpose;
	}
	
	public function getThemAll($access_level="") {
		if($access_level="" || $access_level=="facility_administrator"){
			$query = Doctrine_Query::create() -> select("*") -> from("dcm_exit_reason");
		}
		else{
			$query = Doctrine_Query::create() -> select("*") -> from("dcm_exit_reason") -> where("Active", "1");
		}
		$purposes = $query -> execute();
		return $purposes;
	}
	
	public static function getSource($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("dcm_exit_reason") -> where("id = '$id'");
		$ois = $query -> execute();
		return $ois[0];
	}

}
?>