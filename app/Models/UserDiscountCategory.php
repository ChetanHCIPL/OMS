<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class UserDiscountCategory extends Model
{
    protected $table = 'mas_user_discount_category';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'status', 'created_at', 'updated_at',
    ];


    public $timestamps = false; 

    /** 
     * Get Categories type array
     * @return array
     */
    public static function getAllCategories()
    {
        $query = DB::table('mas_user_discount_category');
        $query->select('id','name');
        $query->where(['status' => self::ACTIVE]);
        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
    * Get the UserDiscountCategory Data
    * @param integer Display Length
    * @param integer Display Start
    * @param string Sort order field
    * @param string Sort order Type ASC|DSC
    * @param string Searching Value
    * @param array Searching array fields and its serching value
    * @return array UserDiscountCategory data array
    */

    public static function getUserDiscountCategoryData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('mas_user_discount_category as mi');
        $query->select('mi.*');
        
        if (isset($search) && $search != "") {
            $query->where(function ($query) use ($search) {
                $query->orWhere('name', 'like', ''.$search.'%');
                if(strtolower($search) == 'active') {
                    $query->orWhere('mi.status', '=', '1');            
                }else if(strtolower($search) == 'inactive') {
                    $query->orWhere('mi.status', '=', '2');
                } 
            });
        }

        if(isset($search_arr) && count($search_arr) > 0){
            if(isset($search_arr['name']) && $search_arr['name'] != '')
                $query->Where('name', 'like', ''.$search_arr['name'].'%');
            if(isset($search_arr['status']) && $search_arr['status'] != '')
                $query->Where('mi.status', $search_arr['status']);
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
    * Add UserDiscountCategory Single
    * @param array UserDiscountCategory Data Array
    * @return array Respose after insert
    */
    public static function addUserDisocuntCategory($insert_array = array()) {
        return self::create($insert_array);
    }

    /**
    * Update UserDiscountCategory Single
    * @param integer UserDiscountCategory Id
    * @param array UserDiscountCategory Data Array
    * @return array Respose after Update
    */
    public static function updateUserDiscountCategory($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }

    /**
     * Get Single UserDiscountCategory data
     * @param int UserDiscountCategory Id 
     * @return array UserDiscountCategory data
    */
    public static function getUserDiscountCategoryDataFromId($id) {
        $query = self::select('mas_user_discount_category.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['mas_user_discount_category.id' => $id]);
        }
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }

    /**
     * Delete UserDiscountCategory
     * @param array UserDiscountCategory Ids Array
     * @return array Respose after Delete
    */
    public static function deleteUserDiscountCategoryData($id = array()) {
        return self::whereIn('id', $id)->delete();
    }

    /**
    * Update UserDiscountCategory Status
    * @param array UserDiscountCategory Ids Array
    * @param string UserDiscountCategory Status
    * @return array UserDiscountCategory after Update
    */
    public static function updateUserDiscountCategoryStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }    
}