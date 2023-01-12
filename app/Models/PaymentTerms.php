<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class PaymentTerms extends Model
{
    protected $table = 'mas_payment_terms';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'term', 'status', 'due_type', 'due_type_value', 'created_at', 'updated_at',
    ];

    public $timestamps = true; 
     
    /**
    * Add PaymentTerms Single/Multiple
    * @param array PaymentTerms Data Array
    * @return array Respose after insert
    */
    public static function addPaymentTerms($insert_array = array()) {
        return self::create($insert_array);
    }
    
    /**
     * Update PaymentTerms Single
     * @param integer Id
     * @param array PaymentTerms Data Array
     * @return array Respose after Update
    */
    public static function updatePaymentTerms($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }
    
    /**
     * Update PaymentTerms  Status
     * @param array PaymentTerms Ids Array
     * @param string PaymentTerms Status
     * @return array PaymentTerms after Update
    */
    public static function updatePaymentTermsStatus($id = array(), $status) {
        return self::whereIn('id', $id)->update(['status' => $status]);
    }

    /**
     * Get the PaymentTerms Data
     * @param integer Display Length
     * @param integer Display Start
     * @param string Sort order field
     * @param string Sort order Type ASC|DSC
     * @param string Searching Value
     * @param array Searching array fields and its serching value
     * @return array PaymentTerms data array
    */

    public static function getPaymentTermsData($iDisplayLength = NULL, $iDisplayStart = NULL, $sort = NULL, $sortdir = NULL, $search = NULL, $search_arr = array()) {
        $query = DB::table('mas_payment_terms as mi');
        $query->select('mi.*');
        
        if (isset($search) && $search != "") {
            $query->where(function ($query) use ($search) {
                $query->orWhere('term', 'like', ''.$search.'%');
                if(strtolower($search) == 'active') {
                    $query->orWhere('mi.status', '=', '1');            
                }else if(strtolower($search) == 'inactive') {
                    $query->orWhere('mi.status', '=', '2');
                } 
            });
        }

        if(isset($search_arr) && count($search_arr) > 0){
            if(isset($search_arr['term']) && $search_arr['term'] != '')
                $query->Where('term', 'like', ''.$search_arr['term'].'%');
            if(isset($search_arr['status']) && $search_arr['status'] != '')
                $query->Where('mi.status', $search_arr['status']);
            if(isset($search_arr['due_type']) && $search_arr['due_type'] != '')
                $query->Where('mi.due_type', $search_arr['due_type']);
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
     * Get Single PaymentTerms data
     * @param int PaymentTerms Id 
     * @return array PaymentTerms data
    */
    public static function getPaymentTermsDataFromId($id) {
        $query = self::select('mas_payment_terms.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['mas_payment_terms.id' => $id]);
        }
        
        $result =  $query->get()->toArray();

        return json_decode(json_encode($result), true);
    }

    /**
     * Return Payment Terms List
     * @return mixed
     */
    public static function getAllPaymentTermsData() {
        $query = self::select('mas_payment_terms.*');
        $query->where(['mas_payment_terms.status' => self::ACTIVE]);
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Delete PaymentTerms
     * @param array PaymentTerms Ids Array
     * @return array Respose after Delete
    */
    public static function deletePaymentTermsData($id = array()) {
        return self::whereIn('id', $id)->delete();
    }
}