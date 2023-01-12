<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SendSystemEmail extends Model
{

    protected $table = 'send_system_email';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['from', 'cc', 'reply_to', 'to_email', 'subject','body','status'];

    public $timestamps = false;
       
    /**
     * Add Send email data
     * @param array User Data Array
     * @return array Respose after insert
     */
    public static function addSendMailData($insert_array = array()) {
        return self::insert($insert_array);
        
    }

    /**
     * Add Send email data
     * @param array User Data Array
     * @return array Respose id
     */
    public static function addSystemSendMailData($insert_array = array()) {
        return self::insertGetId($insert_array);
    }

    /**
     * Get All mail data
     * @return array $data
    */
    public static function getSendMailData() {
        return self::where('status','!=',1)->get()->toArray();
    }

    /**
     * Update Send email Status
     * @param array User Data Array
     * @return array Respose after insert
     */
    public static function updateSendMail($id,$update_array = array()) {
        return self::where('id',$id)->update($update_array);
    }
}