<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
   
	 protected $table="member";   
	protected $fillable = ['parent_member_id','payment_id','first_name','last_name','mobile_number','whatsapp_number','birth_date','email','gender','profession_id','university','account_type','user_type','education_id','pin_code','nationality','country_code','state_code','district','city_code','address','password','referral_code','device_type','parent_referral_code','plan_id','registered_from','lang_code','from_ip','created_at','status','association_name','association_type','govid','gov_reg_number','wallet_balance'];

	 public function parent()
		{
			return $this->belongsTo('App\Models\User', 'parent_member_id')->with('parent');
		}
		
	public static function updateMemberWallet($member_id, $amount )
	{
		$res= User::where('id', $member_id)->update(['wallet_balance'=>$amount]);
		return $res;
	}	
	
		
		
}
