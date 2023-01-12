<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'mas_section';
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

    public $timestamps = true;
    
    /**
    * Get the Section Data
    * @param integer Display Length
    * @param integer Display Start
    * @param string Sort order field
    * @param string Sort order Type ASC|DSC
    * @param string Searching Value
    * @param array Searching array fields and its serching value
    * @return array Section data array
    */

    public static function getSectionData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('mas_section as mi');
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
    * Add Section Single
    * @param array Section Data Array
    * @return array Respose after insert
    */
    public static function addSection($insert_array = array()) {
        return self::create($insert_array);
    }

    /**
    * Update Section Single
    * @param integer Section Id
    * @param array Section Data Array
    * @return array Respose after Update
    */
    public static function updateSection($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }

    /**
     * Get Single Section data
     * @param int Section Id 
     * @return array Section data
    */
    public static function getSectionDataFromId($id) {
        $query = self::select('mas_section.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['mas_section.id' => $id]);
        }
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }

    /**
     * Delete Section
     * @param array Section Ids Array
     * @return array Respose after Delete
    */
    public static function deleteSectionData($id = array()) {
        return self::whereIn('id', $id)->delete();
    }

    /**
    * Update Section Status
    * @param array Section Ids Array
    * @param string Section Status
    * @return array Section after Update
    */
    public static function updateSectionStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }

    /**
     * Get Section data
     * @param array Client ID passed
     * @return array Respose Client Array
     */
    public static function getSectionDataList() {
        $query = DB::table('mas_section');
        
        $query->select('*');
        $query->where(['status' => self::ACTIVE]);

        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }    
}