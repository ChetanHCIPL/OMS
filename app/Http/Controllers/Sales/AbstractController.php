<?php
/**
* 
*/
namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AbstractController extends Controller{

	protected $user = array();
	protected $userId = array();

	/**
	* Contructor
	*/
	public function __construct(){
		
	}

	/**
	* Current logged in user id
	* @param void
	* @return int|null
	*/
	public function getCurrentUserId()
	{
		return;
	}

	/**
	* Current logged in user data
	* @param void
	* @return array
	*/
	public function getCurrentUser()
	{
		return;
	}
}