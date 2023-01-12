<?php
namespace App\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Districts extends Model { 
	protected $table = 'mas_district';
	const ACTIVE = 1;
	const INACTIVE = 2;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		 'state_id','country_id', 'district_name', 'district_code', 'display_order','zone_id', 'status', 'created_at', 'updated_at',
	];

	/**
	 * Function :  Get All Districts records for ajax with state code wise
	 * @param : string state code
	 * @return  json $districtData
	 */
	public static function getDistrictsStateWise($stateId, $districtId = NULL) {
		$query = DB::table('mas_district');
		if ($districtId != '') {
			$query->where(['state_id' => $stateId, 'id' => $districtId]);
		} else {
			$query->where(['state_id' => $stateId]);
		}
		$districtData = $query->get()->toArray();
		
		return json_decode(json_encode($districtData), true);;
	}

	/**
	 * Function :  Get All Districts records for ajax with zone wise
	 * @param : string state code
	 * @return  json $districtData
	 */
	public static function getDistrictsZoneWise($zoneId) {
		$query = DB::table('mas_district');
		
		$query->where(['zone_id' => $zoneId]);
		
		$districtData = $query->get()->toArray();
		
		return json_decode(json_encode($districtData), true);;
	}
	/**
	 * Get zone with state date 
	 */
	public static function getDistrictsStateZoneWise($state,$zoneId) {
		$query = DB::table('mas_district');
		$query->where(['zone_id' => $zoneId,'state_id'=>$state]);
		$districtData = $query->get()->toArray();
		
		return json_decode(json_encode($districtData), true);;
	}
	/**
	 * Districts Status
	 * @return array
	 */
	public function renderDistrictStatus() {
		return [
			self::ACTIVE => ['label' => 'Active', 'code' => self::ACTIVE], self::INACTIVE => ['label' => 'Inactive', 'code' => self::INACTIVE],
		];
	}
	/**
	 * Get the Districts
	 * @param integer Display Length
	 * @param integer Display Start
	 * @param string Sort order field
	 * @param string Sort order Type ASC|DSC
	 * @param string Searching Value
	 * @param array Searching array fields and its serching value
	 * @return array Districts data array
	 */
	public function getDistrictsData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
		$query = DB::table($this->getTable());
		$query->select($this->getTable() . '.*','mc.country_name','ms.state_name','mz.zone_name');
		$query->leftjoin('mas_country AS mc','mc.id','=',$this->getTable() .'.country_id');
		$query->leftjoin('mas_state AS ms','ms.id','=',$this->getTable() .'.state_id');
		$query->leftjoin('mas_zone AS mz','mz.id','=',$this->getTable() .'.zone_id');
		if (isset($search) && $search != "") {
			$query->where(function ($query) use ($search) {
				$query->orWhere('district_name', 'like', '' . $search . '%');
				$query->orWhere('district_code', 'like', '' . $search . '%');
				if (strtolower($search) == 'active') {
					$query->orWhere('status', '=', self::ACTIVE);
				} else if (strtolower($search) == 'inactive') {
					$query->orWhere('status', '=', self::INACTIVE);
				}
			});
		}

		if (isset($search_arr) && count($search_arr) > 0) {
			if (isset($search_arr['district_name']) && $search_arr['district_name'] != '') {
				$query->Where('district_name', 'like', '' . $search_arr['district_name'] . '%');
			}

			if (isset($search_arr['district_code']) && $search_arr['district_code'] != '') {
				$query->Where('district_code', 'like', '' . $search_arr['district_code'] . '%');
			}
			// country name 
			if (isset($search_arr['country_name']) && $search_arr['country_name'] != '') {
				$query->Where('mc.country_name', 'like', '' . $search_arr['country_name'] . '%');
			}
			// state name 
			if (isset($search_arr['state_name']) && $search_arr['state_name'] != '') {
				$query->Where('ms.state_name', 'like', '' . $search_arr['state_name'] . '%');
			}
			// zone name 
			if (isset($search_arr['zone_name']) && $search_arr['zone_name'] != '') {
				$query->Where('mz.zone_name', 'like', '' . $search_arr['zone_name'] . '%');
			}
		 	if (isset($search_arr['status']) && $search_arr['status'] != '') {
				$query->Where('mas_district.status', $search_arr['status']);
			}

		}

		if (isset($iDisplayLength) && $iDisplayLength != "") {
			$query->limit($iDisplayLength);
		}
		if (isset($iDisplayStart) && $iDisplayStart != "") {
			$query->offset($iDisplayStart);
		}
		if (isset($sort) && $sort != "" && isset($sortdir) && $sortdir != "") {
			$query->orderBy($sort, $sortdir);
		}

		$result = $query->get();
		return $result;
	}

	/**
	 * Add Districts Single
	 * @param array Districts Data Array
	 * @return array Respose after insert
	 */
	public static function addDistricts($array = array()) {
		return self::create($array);
	}

	/**
	 * Update Districts Single
	 * @param integer Districts Id
	 * @param array Districts Data Array
	 * @return array Respose after Update
	 */
	public static function updateDistricts($id, $array = array()) {
		return self::where('id', $id)->update($array);
	}

	/**
	 * Update Districts Status
	 * @param array Districts Ids Array
	 * @param string Districts Status
	 * @return array Respose after Update
	 */
	public static function updateDistrictStatus($id = array(), $status) {
		return self::whereIn('id', $id)->update(['status' => $status]);
	}

	/**
	 * Delete Districts  Status
	 * @param array Districts Ids Array
	 * @return array Respose after Delete
	 */
	public static function deleteDistricts($id = array()) {
		return self::whereIn('id', $id)->delete();
	}

	/**
	 * Get Districts Data
	 * @return integer Districts Id
	 * @return array   Districts data
	 */
	public static function getDistrictsDataFromId($id) {
		$query = self::select('mas_district.*');
	
		if (is_array($id)) {
			$query->whereIn('id', $id);
		} else {
			$query->where(['mas_district.id' => $id]);
		}
		$result = $query->get()->toArray();
		return json_decode(json_encode($result), true);
	}


	/**
	 * Get All Active Districts
	 * @param   array   $where_arr
	 * @return array
	 */
	public static function getAllActiveDistricts($where_arr) {
		$query = self::from('mas_district AS mct');
		$query->select('mct.id','mct.district_code', 'mct.district_name', 'mct.state_id', 'mct.country_id');
		if (isset($where_arr['state_id']) && $where_arr['state_id'] != "") {
			$query->where('mct.state_id', $where_arr['state_id']);
		}
		if (isset($where_arr['country_id']) && $where_arr['country_id'] != "") {
			$query->where('mct.country_id', $where_arr['country_id']);
		}
		if (isset($where_arr['status']) && $where_arr['status'] != "") {
			$query->where('mct.status', $where_arr['status']);
		}
		$result = $query->orderBy('mct.district_name', 'ASC')->get()->toArray();
		return $result;
	}

	/**
	 * delete district by country code or state code
	 * @param string field name
	 * @param array country_id
	 * @return array Respose after delete
	 */
	public static function deleteDistrictsByCountryStateId($field_name, $code_arr) {
		return self::whereIn($field_name, $code_arr)->delete();
	}

	/**
	 * get Districts Data
	 * @return array Respose 
	 */
	public static function getDistrictsDataForMember() {
		$query = self::select('district_code','district_name');
        $query->where('status',Districts::ACTIVE);
        $result =  $query->get()->toArray();
        return $result;
	}
	/**
	 * get Districts Data
	 * @return array Respose 
	 */
	public static function getDistrictsDataFromStateCode($state_id_arr) {
		$query = self::select('state_id','district_code','district_name');
	//	$query->where('country_id','IN'); code fix country india 
		if(is_array($state_id_arr)){
			$query->whereIn('state_id', $state_id_arr);
		}else{
			$query->where('state_id', $state_id_arr);
		}
        $result =  $query->get()->toArray();
        return $result;
	}
	/**
	 * get Districts Data
	 * @return array Respose 
	 */
	public static function getDistrictsDataFromDistrictsCode($district_code) {
		$query = self::select('district_code','district_name');
		if(is_array($district_code)){
			$query->whereIn('district_code', $district_code);
		}else{
			$query->where('district_code', $district_code);
		}
        $result =  $query->get()->toArray();
        return $result;
	}

	/**
	 * Function :  Get All District records for ajax with State code wise
	 * @return  json $stateData
	 */
	public static function getDistrictStateWise($stateID = NULL) {
		$query = self::from('mas_district');
		if ($stateID != '') {
			$query->where(['state_id' => $stateID]);
		}
		$query->where(['status' => '1']);
		$districtData = $query->get()->toArray();
		return $districtData;
	}

	/**
	* Function :  Get All Districts records for ajax with state code wise
	* @param : string state code
	* @return  json $districtData
	*/
	public static function getTalukaDistrictWise($districtId ,$talukaId = NULL) {
		$query = DB::table('mas_taluka');
		if ($talukaId != '') {
			$query->where(['district_id' => $districtId, 'id' => $talukaId]);
		} else {
			$query->where(['district_id' => $districtId]);
		}
		$talukaData = $query->get()->toArray();
		return json_decode(json_encode($talukaData), true);;
	}

	/**
	 * Get District data From country and state id
	 * @param   array $where_arr
	 * @return  array $result
	 */
	public static function getDistrictsFromStateAndCountryId($where_arr) {
		$query = self::from('mas_district AS md');
		$query->select('md.id', 'md.district_name', 'md.state_id', 'md.country_id');
		if (isset($where_arr['country_id'])) {
			if (is_array($where_arr['country_id'])) {
				$query->whereIn('md.country_id', $where_arr['country_id']);
			} else {
				$query->where('md.country_id', $where_arr['country_id']);
			}
		}
		if (isset($where_arr['state_id'])) {
			if (is_array($where_arr['state_id'])) {
				$query->whereIn('md.state_id', $where_arr['state_id']);
			} else {
				$query->where('md.state_id', $where_arr['state_id']);
			}
		}
		if (isset($where_arr['status']) && $where_arr['status'] != "") {
			$query->where('md.status', $where_arr['status']);
		}
		$result = $query->orderBy('md.district_name', 'ASC')->get()->toArray();
		return $result;
	}
}