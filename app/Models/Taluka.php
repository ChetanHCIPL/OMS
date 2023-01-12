<?php
namespace App\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Taluka extends Model { 
    use SoftDeletes;

	protected $table = 'mas_taluka';
    // protected $softDelete = true;
	const ACTIVE = 1;
	const INACTIVE = 2;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'zone_id', 'district_id', 'state_id', 'country_id', 'taluka_name', 'taluka_code', 'display_order', 'status', 'created_at', 'updated_at', 'deleted_at',
	];

	/**
	 * Function :  Get All Taluka records for ajax with state code wise
	 * @param : string state code
	 * @return  json $districtData
	 */
	public static function getTalukaStateWise($stateId, $districtId = NULL) {
		$query = DB::table('mas_district');
		if ($districtId != '') {
			$query->where(['state_id' => $stateId, 'id' => $districtId]);
		} else {
			$query->where(['state_id' => $stateId]);
		}
        $query->where('deleted_at', null);
		$districtData = $query->get()->toArray();
		
		return json_decode(json_encode($districtData), true);;
	}
	/**
	 * Taluka Status
	 * @return array
	 */
	public function renderTalukaStatus() {
		return [
			self::ACTIVE => ['label' => 'Active', 'code' => self::ACTIVE], self::INACTIVE => ['label' => 'Inactive', 'code' => self::INACTIVE],
		];
	}
	/**
	 * Get the Taluka
	 * @param integer Display Length
	 * @param integer Display Start
	 * @param string Sort order field
	 * @param string Sort order Type ASC|DSC
	 * @param string Searching Value
	 * @param array Searching array fields and its serching value
	 * @return array Taluka data array
	 */
	public function getTalukaData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
		$query = DB::table($this->getTable());
		$query->select($this->getTable() . '.*','mc.country_name', 'ms.state_name', 'mz.zone_name', 'md.district_name');
		$query->leftjoin('mas_country AS mc','mc.id','=',$this->getTable() .'.country_id');
		$query->leftjoin('mas_state AS ms','ms.id','=',$this->getTable() .'.state_id');
        $query->leftjoin('mas_district AS md','md.id','=',$this->getTable() .'.district_id');
        $query->leftjoin('mas_zone AS mz','mz.id','=',$this->getTable() .'.zone_id');
		if (isset($search) && $search != "") {
			$query->where(function ($query) use ($search) {
				$query->orWhere('taluka_name', 'like', '' . $search . '%');
				$query->orWhere('taluka_code', 'like', '' . $search . '%');
				if (strtolower($search) == 'active') {
					$query->orWhere('status', '=', self::ACTIVE);
				} else if (strtolower($search) == 'inactive') {
					$query->orWhere('status', '=', self::INACTIVE);
				}
			});
		}

		if (isset($search_arr) && count($search_arr) > 0) {
			if (isset($search_arr['taluka_name']) && $search_arr['taluka_name'] != '') {
				$query->Where('taluka_name', 'like', '' . $search_arr['taluka_name'] . '%');
			}

			if (isset($search_arr['taluka_code']) && $search_arr['taluka_code'] != '') {
				$query->Where('taluka_code', 'like', '' . $search_arr['taluka_code'] . '%');
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
			// district name 
			if (isset($search_arr['district_name']) && $search_arr['district_name'] != '') {
				$query->Where('md.district_name', 'like', '' . $search_arr['district_name'] . '%');
			}
		 	if (isset($search_arr['status']) && $search_arr['status'] != '') {
				$query->Where('mas_taluka.status', $search_arr['status']);
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

        $query->where('deleted_at', null);
        
		$result = $query->get();
		return $result;
	}

	/**
	 * Add Taluka Single
	 * @param array Taluka Data Array
	 * @return array Respose after insert
	 */
	public static function addTaluka($array = array()) {
		return self::create($array);
	}

	/**
	 * Update Taluka Single
	 * @param integer Taluka Id
	 * @param array Taluka Data Array
	 * @return array Respose after Update
	 */
	public static function updateTaluka($id, $array = array()) {
		return self::where('id', $id)->update($array);
	}

	/**
	 * Update Taluka Status
	 * @param array Taluka Ids Array
	 * @param string Taluka Status
	 * @return array Respose after Update
	 */
	public static function updateTalukaStatus($id = array(), $status) {
		return self::whereIn('id', $id)->update(['status' => $status]);
	}

	/**
	 * Delete Taluka  Status
	 * @param array Taluka Ids Array
	 * @return array Respose after Delete
	 */
	public static function deleteTaluka($id = array()) {
        self::whereIn('id', $id)->update(['status' => 3]);
		return self::whereIn('id', $id)->delete();
	}
	/**
	 * Get Taluka data 
	 * @return integer Taluka Id array
	 * @return array   Taluka data
	 */
	public static function getTalukaDataByDistrictsID($Did = array()) {
		$query = self::select('mas_taluka.*');
		if (is_array($Did)) {
			$query->whereIn('district_id', $Did);
		} else {
			$query->where(['mas_taluka.district_id' => $Did]);
		}
		$result = $query->get()->toArray();
		return json_decode(json_encode($result), true);
	}
	/**
	 * Get Taluka Data
	 * @return integer Taluka Id
	 * @return array   Taluka data
	 */
	public static function getTalukaDataFromId($id) {
		$query = self::select('mas_taluka.*');
		if (is_array($id)) {
			$query->whereIn('id', $id);
		} else {
			$query->where(['mas_taluka.id' => $id]);
		}
        $query->where('deleted_at', null);
		$result = $query->get()->toArray();
		return json_decode(json_encode($result), true);
	}

	/**
    * Get All Active Taluka 
    */
    public static function getAllActiveTaluka($id = NULL){
        if($id != '')
        {   
            $id = explode(',',$id);
            $query = self::select('*');
            if(is_array($id)){
                $query->whereIn('mas_taluka.id', $id);
            }else {
                $query->where(['mas_taluka.id' => $id]);
            } 
            $result =  $query->get()->toArray();

            return $result;

        }else{
            return self::where(['status' =>'1'])->get()->toArray();    
        }
    }

    /**
	 * Get Taluka data From country and state and district id
	 * @param   array $where_arr
	 * @return  array $result
	 */
	public static function getTalukaFromCSDId($where_arr) {
		$query = self::select('mas_taluka.id', 'mas_taluka.taluka_name', 'mas_taluka.district_id', 'mas_taluka.state_id', 'mas_taluka.country_id');
		if (isset($where_arr['country_id'])) {
			$query->where('mas_taluka.country_id', $where_arr['country_id']);
		}
		if (isset($where_arr['state_id'])) {
			$query->where('mas_taluka.state_id', $where_arr['state_id']);
		}
		if (isset($where_arr['district_id'])) {
			$query->where('mas_taluka.district_id', $where_arr['district_id']);
		}
		if (isset($where_arr['status']) && $where_arr['status'] != "") {
			$query->where('mas_taluka.status', $where_arr['status']);
		}
		$result = $query->orderBy('mas_taluka.taluka_name', 'ASC')->get()->toArray();
		return $result;
	}
}