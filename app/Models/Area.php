<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'mas_zone';
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
    * Get All Active Zone 
    */
    public static function getAllActiveArea($id = NULL){
        if($id != '')
        {   
            $id = explode(',',$id);
            $query = self::select('*');
            if(is_array($id)){

                $query->whereIn('mas_zone.id', $id);
            }else {
               
                $query->where(['mas_zone.id' => $id]);
            } 
            $result =  $query->get()->toArray();

            return $result;

        }else{
            return self::where(['status' =>'1'])->get()->toArray();    
        }
    }
}