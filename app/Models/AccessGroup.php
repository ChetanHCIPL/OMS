<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class AccessGroup extends Model {
	protected $table = 'admin_access_group';
    protected $primaryKey = 'id';

    const ACTIVE = 1;
    const INACTIVE = 2;

    const TODAY     = 1;
    const DAYS7     = 2;
    const MONTH1    = 3;
    const MONTHS3   = 4;
    const MONTHS6   = 5;
    const YEAR1     = 6;
    const NOLIMIT   = 7;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'access_group', 'status'
    ];
    public $timestamps = false;


     /**
    * Status
    * @return array
    */
    public static function renderDatePriode()
    {
        return [
            self::TODAY => ['label' => 'Today','code' => self::TODAY],self::DAYS7 => ['label' => '7 Days','code' => self::DAYS7],self::MONTH1 => ['label' => '1 Month','code' => self::MONTH1],self::MONTHS3 => ['label' => '3 Months','code' => self::MONTHS3],self::MONTHS6 => ['label' => '6 Months','code' => self::MONTHS6],self::YEAR1 => ['label' => '1 Year','code' => self::YEAR1],self::NOLIMIT => ['label' => 'No Limit','code' => self::NOLIMIT]
        ];
    }

    /**
     * Get the Access group Data
     * @param integer $length 
     * @param  integer $start 
     * @param string $search
     * @param string $sort order Type ASC|DSC
     * @param string $sortDir order field
     * @return array Reason data array
     */
    public static function getAccessGroupData($length = NULL,$start = NULL,$sort = NULL,$sortDir = NULL,$search = NULL,$search_arr  = array())
    {
    	 $query = DB::table('admin_access_group as aa');
         $query->select('aa.id','aa.access_group','aa.status',DB::raw("count(a.id) as users"));
    	 
    	 if(isset($search) && $search != ""){
    	 	$query->orWhere('aa.access_group','like',$search.'%');
    	 	if (strtolower($search) == "active"){
    	 		$query->orWhere('aa.status','=', self::ACTIVE);
    	 	}elseif (strtolower($search) == "inactive"){
    	 		$query->orWhere('aa.status','=',self::INACTIVE);
    	 	}
    	 }

        if(isset($search_arr) && count($search_arr) > 0){
            if(isset($search_arr['access_group']) && $search_arr['access_group'] != ''){
                $query->where('aa.access_group','like',$search_arr['access_group'].'%');
            }
            if(isset($search_arr['users']) && $search_arr['users'] != ''){
                 $query->having('users', '=', $search_arr['users']);
            }
            if(isset($search_arr['status']) && $search_arr['status'] != ''){
                $query->where('aa.status',$search_arr['status']);
            }
        }

        $query->leftjoin('admin_access_group_permission as ap', 'aa.id', '=', 'ap.access_group_id');
        $query->leftjoin('admin as a', 'ap.admin_id', '=', 'a.id');

    	if (isset($length) && $length != "") {
            $query->limit($length);
        }
        if (isset($start) && $start != "") {
            $query->offset($start);
        }
        if (isset($sort) && $sort != "" && isset($sortDir) && $sortDir != "") {
            $query->orderBy($sort, $sortDir);
        }
        $query->groupBy('aa.id');
        $result = $query->get();

        // echo '<pre>'; print_r($result); echo '</pre>'; exit();

        return $result;
        	
    }

    /**Add Acess group  Data
    *
    * @param array Access Group Data Array
    * @return array Respose after insert
    *
    **/
    public static function addAccessGroup($array = array()){
    	return	self::create($array);
    }

    /** Get  Access Group data 
     * @param int Access Group id 
     * @return array
    */
    public static function getAccessGroupDataFromId($id) {
        $query = self::select('*');
        if(is_array($id)){
            $query->whereIn('admin_access_group.id', $id);
        }else {
            $query->where(['admin_access_group.id' => $id]);
        }
        
        $result =  $query->get()->toArray();

        return $result;
    }

    /**Update Acess group Data
    *
    * @param array Access Group Data Array
    * @param integer Access Group Id
    * @return array Respose after Update
    *
    **/
    public static function updateAccessGroup($array = array(),$id){
    	return self::where('id', $id)->update($array);
    }


    /** 
     * Update Access Group Status
     * @param array Access Group Id
     * @param string Access Group Status
     * @return array Respose after Update
    */
    public static function updateAccessGroupStatus($id,$status){
         return self::whereIn('id',$id)->update(['status'=>$status]);
    }

    /** Delete Access Group 
     * @param array Access Group Id
     * @return array Respose after Delete
    */
    public static function deleteGroup($id){
        return self::whereIn('id', $id)->delete();
    }

     /** Get the Access Group Id
    * @param integer iAGroupId
    * @return integer id
    */
    public static function getAccessGroupId($iAGroupId){
        return self::where(['id' => $iAGroupId])
                            ->get()->toArray();
    }

    /** Get the Access Group Name
    * @param array Access Group Id
    * @return array Access Group Name array
    */
    public static function getAccessGroupName($id){
        return self::whereIn('id', $id)
                ->select(DB::raw("GROUP_CONCAT(access_group) AS access_group"))
                ->get()->toArray();
    }

     /** Get Active Access Group datas
    * @param integer iAGroupId
    * @return integer id
    */
    public static function getActiveAccessGroup($id = NULL){
        if($id != '')
        {   
            $id = explode(',',$id);
            $query = self::select('*');
            if(is_array($id)){

                $query->whereIn('admin_access_group.id', $id);
            }else {
               
                $query->where(['admin_access_group.id' => $id]);
            } 
            $result =  $query->get()->toArray();

            return $result;

        }else{
            return self::where(['status' =>'1'])->get()->toArray();    
        }
    }
}