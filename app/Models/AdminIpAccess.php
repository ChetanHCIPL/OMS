<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminIpAccess extends Model {

    protected $table = 'admin_ip_access';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'admin_id', 'ip_id' 
    ];
    public $timestamps = false;
	 

    /**
    * Add IpAccess
    * @param array IpAccess Array
    * @return array Respose after insert
    */
    public static function addAdminIpAccess($insert_array = array()) {
        return self::insert($insert_array);
    }
    
    /**
     * Delete IpAccess  Status
     * @param array IpAccess Array
     * @return array Respose after Delete
     */
    public static function deleteAdminIpAccess($admin_id) {
        if(is_array($admin_id)){
            return self::whereIn('admin_id', $admin_id)->delete();
        }else {
            return self::where('admin_id', $admin_id)->delete();
        }   
    }

    /**
     * Delete  IpAccess  Status
     * @param array IpAccess Array
     * @return array Respose after Delete
     */
    public static function getadmninIpId($admin_id) {
        $query = self::where('admin_id', $admin_id);
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);     
    }

    /**
     * Get IP data
     * @return array IP data
    */
    public static function getActiveIp($admin_id = NULL) {
        $query = self::select('mas_ip.*'); 
        $query->leftjoin('admin_ip_access AS aic', function($admin_ip_access_join) use($admin_id){
                $admin_ip_access_join->on('aic.ip_id', "=", 'mas_ip.id');
                $admin_ip_access_join->where('aic.admin_id', "=", $admin_id);
            });
        $query->where('mas_ip.status', self::ACTIVE);
        $result =  $query->get()->toArray();
        return json_decode(json_encode($result), true);
    }

}