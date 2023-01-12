<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'mas_country';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE  = 2;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_name','country_code', 'isd_code', 'display_order', 'flag','status','created_at', 'updated_at'
    ]; 
     
    /**
    * Function :  Get All Country records for ajax
    * @return  json $userArray 
    */
    public static function getAllCountryData($countryId = NULL)
    {   
         $query = DB::table('mas_country'); 
         if($countryId != ''){
            $query->where('id', $countryId);
         }
         $userData =  $query->get()->toArray();
         return $userData;
    }   
    /**
     * Get Country ISD Code
     *
     * @param  array $where_arr
     * @return array $data
    */
    public static function getCountryISDCodeData($where_arr=array()) {
        $query = self::from('mas_country AS mc');
        $query->select('mc.id','mc.isd_code','mc.flag');
        if(isset($where_arr['status']) && $where_arr['status'] != ""){
            $query->where('mc.status', $where_arr['status']);
        }
        if(isset($where_arr['country_id']) && $where_arr['country_id'] != ""){
            $query->where('mc.id', $where_arr['country_id']);
        }
        $query->where('mc.isd_code', '!=', '');
        $data = $query->get()->toArray();
        return $data;
    }
    

    /**
     * Get the Country Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array Country data array
     */

    public static function getCountryData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('mas_country');
        $query->select('mas_country.*');
       
        if (isset($search) && $search != "") {
            $query->where(function ($query) use ($search) {
                $query->orWhere('country_name', 'like', ''.$search.'%');
                $query->orWhere('isd_code', 'like', ''.$search.'%');
                if(strtolower($search) == 'active') {
                    $query->orWhere('status', '=', '1');            
                }else if(strtolower($search) == 'inactive') {
                    $query->orWhere('status', '=', '2');
                }
            });
        }

        if(isset($search_arr) && count($search_arr) > 0){
            if(isset($search_arr['couName']) && $search_arr['couName'] != '')
                $query->Where('country_name', 'like', ''.$search_arr['couName'].'%');
                if(isset($search_arr['couCode']) && $search_arr['couCode'] != '')
                $query->Where('country_Code', 'like', ''.$search_arr['couCode'].'%');
            if(isset($search_arr['isdCode']) && $search_arr['isdCode'] != '')
                $query->Where('isd_code', 'like', ''.$search_arr['isdCode'].'%');            
            if(isset($search_arr['couStatus']) && $search_arr['couStatus'] != '')
                $query->Where('status', $search_arr['couStatus']);
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
    * Add Country Single
    * @param array Country Data Array
    * @return array Respose after insert
    */
    public static function addCountry($insert_array = array()) {
        return self::create($insert_array);
    }
    
    /**
     * Update Country Single
     * @param integer Country Id
     * @param array Country Data Array
     * @return array Respose after Update
    */
    public static function updateCountry($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }
    
    /**
     * Update Product  Status
     * @param array Country Ids Array
     * @param string Country Status
     * @return array Respose after Update
    */
    public static function updateCountriesStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }
    
    /**
     * Delete Product  Status
     * @param array Country Ids Array
     * @return array Respose after Delete
    */
    public static function deleteCountries($id = array()) {
        return self::whereIn('id', $id)->delete();
    }
    
    /**
     * Get Single Country data
     * @param int Country Id 
     * @return array Country data
    */
    public static function getCountryDataFromId($id) {
        $query = self::select('mas_country.*');
        

        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['mas_country.id' => $id]);
        }
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }
    
    /**
     * Get All Active Country data
     * @param   array   $where_arr
     * @return  array   $result
    */
    public static function getAllActiveCountries($where_arr=array()) {
        $query = self::from('mas_country AS mc');
        $query->select('mc.id','mc.country_name');
        if(isset($where_arr['status']) && $where_arr['status'] != ""){
            $query->where('mc.status', $where_arr['status']);
        }
        $result = $query->orderBy('mc.country_name', 'ASC')->get()->toArray();
        return $result;
    }

     /**
     * Function to get associated array of country_name 
     * @param  array $whereArr
     * @return array $data
    */
    public static function getCountryAssocArr($whereArr=array()) {
        $query = self::from('mas_country AS mc');
        if(isset($whereArr['status']) && $whereArr['status'] != ""){
            $query->where('mc.status', $whereArr['status']);
        }
        $data = $query->pluck('mc.country_name', 'mc.id')->toArray();
        return $data;
    }

    /**
     * Get Country Name
     *
     * @param string country Code
     * @return array Country Name Data   
    */
    public static function getCountryNameById($countryId)
    {
        $query = self::select('country_name');
        $query->where('id','=', $countryId);
        $data = $query->get()->toArray();
        return $data;
    }
    
    /**
     * Get Country ISD Code
     *
     * @param  array $where_arr
     * @return array $data
    */
    public static function getCountryISDCode($where_arr=array()) {
        $query = self::from('mas_country AS mc');
        $query->select('mc.id','mc.isd_code','mc.flag');
        if(isset($where_arr['status']) && $where_arr['status'] != ""){
            $query->where('mc.status', $where_arr['status']);
        }
        $query->where('mc.isd_code', '!=', '');
        $data = $query->orderBy('mc.id', 'ASC')->get()->toArray();
        return $data;
    }   
}