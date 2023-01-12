<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class SalesStructure extends Model {
	protected $table = 'sales_structure';
    protected $primaryKey = 'id';

    const ACTIVE = 1;
    const INACTIVE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'parent_id', 'title', 'path', 'seq_no', 'status'
    ];
    public $timestamps = false;

    /** Get Active Sales Structure
    * @param integer iAGroupId
    * @return integer id
    */
    public static function getActiveSalesStructure($id = NULL){
        $query = self::select('*');
        if($id != ''){   
            $id = explode(',',$id);
            if(is_array($id)){
                $query->whereIn('sales_structure.id', $id);
            }else {
                $query->where(['sales_structure.id' => $id]);
            } 
        }
        $query->where(['sales_structure.status' =>'1']);
        $query->orderBy('sales_structure.display_order','ASC');
        //$query->limit(3);
        $result =  $query->get()->toArray();
        return $result;
    }

    public static function getSalesStructureDataFromId($id) {
        $query = self::select('sales_structure.*');
        if(is_array($id)){
            $query->whereIn('id', $id);
        }else {
            $query->where(['sales_structure.id' => $id]);
        }
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }
}