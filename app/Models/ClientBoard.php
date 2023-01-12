<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class ClientBoard extends Model
{
    protected $table = 'client_board';
    protected $primaryKey = 'id';
    
    const ACTIVE     = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'client_id','board_id','status'
    ];
    public $timestamps = false; 

    /**
     * Get Client data
     * @param array Client ID passed
     * @return array Respose Client Array
     */
    public static function getClientBoardData($client_id = NULL) {
        $query = DB::table('client_board');
        $query->select('client_board.*');
        $query->where(['status' => 1]);
        $result = $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Get Client Board data
     * @param array Client ID passed
     * @return array Respose Client Array
     */
     public static function addClientBoard($insert_array = array()) {
        return self::create($insert_array);
    }

     /**
     * Update Clients Single
     * @param integer Id
     * @param array Clients Data Array
     * @return array Respose after Update
    */
    public static function updateClientBoard($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }

      /**
     * Get all ids of client 
      * @param int Client Id 
     * @return array ClientAddress data
     */
    public static function getClientBoardIdsByClientId($cid){
        $result = self::where(['client_id' => $cid])->pluck('board_id AS id')->toArray();
        return json_decode(json_encode($result), true);
    }

    /**
     * Delete Client Board
     * @param array Client Ids Array
     * @return array Respose after Delete
    */
    public static function deleteClientBoardsData($client_id) {
        return self::where('client_id', $client_id)->delete();
    }
}