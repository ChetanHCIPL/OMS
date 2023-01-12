<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SendSystemEmailAttachment extends Model
{

    protected $table = 'send_system_mail_attachment';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['send_system_email_id', 'attachment'];

    public $timestamps = false;
       
    /**
     * Add Send email data
     * @param array User Data Array
     * @return array Respose after insert
     */
    public static function addSendMailAttachmentData($insert_array = array()) {
        return self::insert($insert_array);
    }

    /**
     * Get All mail data
     * @return array $data
    */
    public static function getSendMailAttachmentData($id) {
        return self::where('send_system_email_id','=',$id)->get()->toArray();
    }


}