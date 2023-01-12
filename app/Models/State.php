<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class State extends Model {
	protected $table = 'mas_state';
	const ACTIVE = 1;
	const INACTIVE = 2;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'country_id','state_name','state_code', 'display_order', 'status', 'created_at',
	];

	/**
	 * Function :  Get All State records for ajax with country code wise
	 * @return  json $stateData
	 */
	public static function getStateCountryWise($countryId = NULL, $stateId = NULL) {
		$query = self::from('mas_state');
		if ($stateId != '') {
			$query->where(['id' => $stateId]);
		} else {
			$query->where(['country_id' => $countryId]);
		}
		$query->where(['status' => '1']);
		$stateData = $query->get()->toArray();
		return $stateData;
	}

	/**
	 * Get Single State data From country code
	 * @param   array $where_arr
	 * @return  array $result
	 */
	public static function getStateDataFromCountryId($where_arr) {
		$query = self::from('mas_state AS ms');
		$query->select('ms.id', 'ms.state_name', 'ms.country_id');
		if (isset($where_arr['country_id'])) {
			if (is_array($where_arr['country_id'])) {
				$query->whereIn('ms.country_id', $where_arr['country_id']);
			} else {
				$query->where('ms.country_id', $where_arr['country_id']);
			}
		}
		if (isset($where_arr['status']) && $where_arr['status'] != "") {
			$query->where('ms.status', $where_arr['status']);
		}
		$result = $query->orderBy('ms.state_name', 'ASC')->get()->toArray();
		return $result;
	}

	/**
	 * Get the State Data
	 * @param integer Display Length
	 * @param integer Display Start
	 * @param string Sort order field
	 * @param string Sort order Type ASC|DSC
	 * @param string Searching Value
	 * @param array  Searching array fields and its serching value
	 * @return array State Data array
	 */
	public static function getStateData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
		$query = DB::table('mas_state');
		$query->select('mas_state.*','mc.country_name');
		$query->leftjoin('mas_country AS mc','mc.id','=','mas_state.country_id');
		if (isset($search) && $search != "") {
			$query->where(function ($query) use ($search) {
				$query->orWhere('state_code', 'like', '' . $search . '%');
				$query->orWhere('state_name', 'like', '' . $search . '%');
				if (strtolower($search) == 'active') {
					$query->orWhere('mas_state.status', '=', '1');
				} else if (strtolower($search) == 'inactive') {
					$query->orWhere('mas_state.status', '=', '2');
				}
			});
		}

		if (isset($search_arr) && count($search_arr) > 0) {
			if (isset($search_arr['staName']) && $search_arr['staName'] != '') {
				$query->Where('state_name', 'like', '' . $search_arr['staName'] . '%');
			}

			if (isset($search_arr['staCode']) && $search_arr['staCode'] != '') {
				$query->Where('state_code', 'like', '' . $search_arr['staCode'] . '%');
			}

			if (isset($search_arr['couCode']) && $search_arr['couCode'] != '') {
				$query->Where('mc.country_name', 'like', '' . $search_arr['couCode'] . '%');
			}

			if (isset($search_arr['staStatus']) && $search_arr['staStatus'] != '') {
				$query->Where('mas_state.status', $search_arr['staStatus']);
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
		//echo $result = $query->toSql();exit;
		$result = $query->get();
		return $result;
	}

	/**
	 * Function to get associated array of state_code:state_name pair
	 *
	 * @param    array  $whereArr
	 * @return   array  $data
	 */
	public static function getStateFromCountryCode($whereArr) {
		$query = self::from('mas_state AS ms');
		if (isset($whereArr['country_id']) && $whereArr['country_id'] != "") {
			$query->where('ms.country_id', $whereArr['country_id']);
		}
		if (isset($whereArr['status']) && $whereArr['status'] != "") {
			$query->where('ms.status', $whereArr['status']);
		}
		$data = $query->pluck('ms.state_name', 'ms.id')->toArray();
		return $data;
	}

	/** Update State Single
	 * @param integer State Id
	 * @param array Products Data Array
	 * @return array Respose after Update
	 */
	public static function updateState($id, $update_array = array()) {
		return self::where('id', $id)->update($update_array);
	}

	
	/** Update Country name
	 * @param integer country Id
	 
	 */
	public static function updateCountryName($country_id, $update_array = array()) {
		return self::where('country_id', $country_id)->update($update_array);
	}
	/**
	 * Delete States
	 * @param array States Ids Array
	 * @return array Respose after Delete
	 */
	public static function deleteStates($id = array()) {
		return self::whereIn('id', $id)->delete();
	}

	/**
	 * Get Single State data
	 * @param integer State id
	 * @return array
	 */
	public static function getStateDataFromId($id) {
		$query = self::select('mas_state.*');
		

		if (is_array($id)) {
			$query->whereIn('mas_state.id', $id);
		} else {
			$query->where(['mas_state.id' => $id]);
		}

		$result = $query->get()->toArray();

		return json_decode(json_encode($result), true);
	}

	/**
	 * Add State Single
	 * @param  array State Data Array
	 * @return array Respose after insert
	 */
	public static function addState($insert_array = array()) {
		return self::create($insert_array);
	}


	/**
	 * Update States Status
	 * @param array States Ids Array
	 * @param string States Status
	 * @return array Respose after Update
	 */
	public static function updateStatesStatus($id = array(), $status) {
		return self::whereIn('id', $id)->update(['status' => $status]);
	}

	/**
	 * delete state by country code
	 * @param array States Ids Array
	 * @param string States Status
	 * @return array Respose after Update
	 */
	public static function deleteStatesByCountryId($country_id) {
		return self::whereIn('country_id', $country_id)->delete();
	}

	/**
	 * get State Data
	 * @return array Respose 
	 */
	public static function getStateDataForMember() {
		$query = self::select('id','state_name');
        $query->where('status',State::ACTIVE);
        $result =  $query->get()->toArray();
        return $result;
	}

	/**
	 * get State Data
	 * @return array Respose 
	 */
	public static function getStateDataForMemberList($state_id_arr) {
		$query = self::select('id','state_name');
	//	$query->where('country_code','IN');/ fix for india 
        $query->whereIn('id', $state_id_arr);
        $result =  $query->get()->toArray();
        return $result;
    }

    /**
	 * get State All Data
	 * @return array Respose 
	 */
	public static function getStateAllDataForMember() {
		$query = self::select('id','state_name');
        //	$query->where('country_code','IN');/ fix for india 
        $result =  $query->get()->toArray();
        return $result;
	}
	
}