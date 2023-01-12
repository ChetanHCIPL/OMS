<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'mas_document';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'display_order', 'status', 'created_at', 'updated_at',
    ];

    public $timestamps = true;
    
    /**
    * Get the Document Data
    * @param integer Display Length
    * @param integer Display Start
    * @param string Sort order field
    * @param string Sort order Type ASC|DSC
    * @param string Searching Value
    * @param array Searching array fields and its serching value
    * @return array Document data array
    */

    public static function getDocumentData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('mas_document as mi');
        $query->select('mi.*');
        
        if (isset($search) && $search != "") {
            $query->where(function ($query) use ($search) {
                $query->orWhere('title', 'like', ''.$search.'%');
                $query->orWhere('display_order', 'like', ''.$search.'%');
                if(strtolower($search) == 'active') {
                    $query->orWhere('mi.status', '=', '1');            
                }else if(strtolower($search) == 'inactive') {
                    $query->orWhere('mi.status', '=', '2');
                } 
            });
        }

        if(isset($search_arr) && count($search_arr) > 0){
            if(isset($search_arr['title']) && $search_arr['title'] != '')
                $query->Where('title', 'like', ''.$search_arr['title'].'%');
            if(isset($search_arr['display_order']) && $search_arr['display_order'] != '')
                $query->Where('display_order', 'like', ''.$search_arr['display_order'].'%');
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
    * Add Document Single
    * @param array Document Data Array
    * @return array Respose after insert
    */
    public static function addDocument($insert_array = array()) {
        return self::create($insert_array);
    }

    /**
    * Update Document Single
    * @param integer SerDocumenties Id
    * @param array Document Data Array
    * @return array Respose after Update
    */
    public static function updateDocument($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }

    /**
     * Get Single Document data
     * @param int Document Id 
     * @return array Document data
    */
    public static function getDocumentDataFromId($id){
        $query = self::select('mas_document.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['mas_document.id' => $id]);
        }
        
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
    /**
     *  @return array Respose Array
     */
    public static function getAllActiveDocumentData() {
        $query = self::select('mas_document.*');
        $query->where(['status' => self::ACTIVE]);
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
    /**
     * Delete Document
     * @param array Document Ids Array
     * @return array Respose after Delete
    */
    public static function deleteDocumentData($id = array()) {
        return self::whereIn('id', $id)->delete();
    }

    /**
    * Update Document Status
    * @param array Document Ids Array
    * @param string Document Status
    * @return array Document after Update
    */
    public static function updateDocumentStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }    
}