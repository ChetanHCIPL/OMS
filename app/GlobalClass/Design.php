<?php

namespace App\GlobalClass;

class Design
{
	public static function button($btnType,$btnText=NULL,$btnColor=NULL,$attrbite=NULL) {
		$button_arr = array();
		$btnData = '';
		switch ($btnType) {
			case "edit" :
				$btnData = '<span class="btn btn-icon btn-secondary waves-effect waves-light"><i class="la la-edit"></i></span>';
				break;
			case "view" :
				$btnData = '<span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-eye"></i></span>';
				break;
			case "reply" :
				$btnData = '<span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-reply"></i> View & Reply</span>';
				break;
			case "print" :
				$btnData = '<span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-plus-circle"></i></span>';
			case "show" :
				$btnData = '<span class="btn btn-icon btn-secondary btn-light waves-effect waves-light"><i class="la la-print"></i></span>';
			case "language" :
				$btnData = '<span class="btn btn-icon btn-warning waves-effect waves-light"><i class="la la-book"></i></span>';
				break;
		}
		return $btnData;
	}
	public static function blade($btnType,$btnText=NULL,$btnColor=NULL,$attrbite=NULL) {
		$btnData = '';
		switch ($btnType) {
			case "status" :
				$btnData = '<span class="badge badge-border '.$btnColor.' round badge-'.$btnColor.'" '.$attrbite.'>'.$btnText.'</span>';
				break;
		}
		return $btnData;
	}

}
?>