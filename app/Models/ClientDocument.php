<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Database\Eloquent\Model;

class ClientDocument extends Model
{
    protected $table = 'client_document';
    protected $primaryKey = 'id';
    
    const ACTIVE    = 1;
    const INACTIVE   = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id', 'document_id', 'file_name', 'notes', 'uploaded_by', 'upload_date', 'verified_by', 'is_verified', 'verified_date', 'created_at', 'updated_at'
    ];

    public $timestamps = true; 
    /**
    * Add ClientDocument Single/Multiple
    * @param array ClientDocument Data Array
    * @return array Respose after insert
    */
    public static function addClientDocuments($insert_array = array()) {
        return self::create($insert_array);
    }
    
    /**
    * Add/Update ClientDocument Single
    * @param array ClientDocument Data Array
    * @return array Respose after insert
    */
    public static function addClientDocument($data)
    {
        $check = self::where('client_id','=',$data['client_id'])->where('document_id','=',$data['document_id'])->count();
        if($check == 0)
        {
            return self::create($data);
        }else{
            return self::where('client_id','=',$data['client_id'])->where('document_id','=',$data['document_id'])->update($data);
        }        
    }

    public static function updateClientDocument($id, $update_array = array()) {
        return self::where('id', $id)->update($update_array);
    }

     /**
     * Get Client Document List Array
     * @param integer Id
     * @param array ClientDocument Data Array
    */
    public static function getClientDocumentData($client_id) {
        $documentData = [];
        $countDocument = 0;
        $countVerifyDocument = 0;
        $documents = DB::table('mas_document AS md')
                    ->leftJoin('client_document AS cd', function($join) use ($client_id)
                    {
                        $join->on('md.id', '=', 'cd.document_id')->where('cd.client_id','=',$client_id);
                    })
                    ->leftJoin('admin AS a','a.id','=','cd.uploaded_by')
                    ->select('md.id','md.title','cd.id as client_document_id','cd.client_id','cd.file_name','cd.notes','cd.is_verified','a.first_name as updated_by','cd.verified_date')
                    ->where('md.status', '=', 1)->orderBy('md.display_order','asc')->get()->toArray();        
        foreach ($documents as $index => $value) {
            foreach ($value as $key => $val) {
              $documentData[$index][$key] = $val;
            }
            if(!empty($value->client_id))
            {
                $countDocument++;
            }

            if($value->is_verified)
            {
                $countVerifyDocument++;
            }
        }
        return ['documentData' => $documentData,'countDocument' => $countDocument,'countVerifyDocument'=>$countVerifyDocument];
    }    
}