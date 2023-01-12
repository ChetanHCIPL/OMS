<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class AccountYear extends Model
{
    protected $table = 'mas_account_year';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'form_date','to_date', 'status', 'created_at', 'updated_at', 'created_by'];

    public $timestamps = true;
    
    /**
    * Get the AccountYear Data
    * @param integer Display Length
    * @param integer Display Start
    * @param string Sort order field
    * @param string Sort order Type ASC|DSC
    * @param string Searching Value
    * @param array Searching array fields and its serching value
    * @return array AccountYear data array
    */

    public static function getAccountYearData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = self::select('mas_account_year.*');
        
        if (isset($search) && $search != "") {
            $query->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', ''.$search.'%');
               // $query->orWhere('auto_approve_limit', 'like', ''.$search.'%');
                if(strtolower($search) == 'active') {
                    $query->orWhere('status', '=', '1');            
                }else if(strtolower($search) == 'inactive') {
                    $query->orWhere('status', '=', '2');
                } 
            });
        }

        if(isset($search_arr) && count($search_arr) > 0){
            if(isset($search_arr['name']) && $search_arr['name'] != '')
                $query->Where('name', 'like', ''.$search_arr['name'].'%');
            if(isset($search_arr['auto_approve_limit']) && $search_arr['auto_approve_limit'] != '')
                //$query->Where('auto_approve_limit', 'like', ''.$search_arr['auto_approve_limit'].'%');
            if(isset($search_arr['status']) && $search_arr['status'] != '')
                $query->Where('status', $search_arr['status']);
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
    * Add AccountYear Single
    * @param array AccountYear Data Array
    * @return array Respose after insert
    */
    public static function addAccountYear($insert_array = array()) {
        return self::create($insert_array);
    }

    /**
    * Update AccountYear Single
    * @param integer SerAccountYearies Id
    * @param array AccountYear Data Array
    * @return array Respose after Update
    */
    public static function updateAccountYear($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }

    /**
     * Get Single Account Year data
     * @param int Account Year Id 
     * @return array Account Year data
    */
    public static function getAccountYearDataFromId($id){
        $query = self::select('mas_account_year.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['id' => $id]);
        }
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }

    /**
     * Delete Account Year
     * @param array Account Year Ids Array
     * @return array Respose after Delete
    */
    public static function deleteAccountYearData($id = array()) {
        return self::whereIn('id', $id)->delete();
    }

    /**
    * Update Account Year Status
    * @param array Account Year Ids Array
    * @param string Account Year Status
    * @return array Account Year after Update
    */
    public static function updateAccountYearStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }    
}