<?php

namespace App\Http\Controllers\Admin\Catalog;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \App\Http\Controllers\Admin\AbstractController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use App\User;
use Config;
use Validator;
use App;
use Auth; 
use App\GlobalClass\Design;

class OrderController extends AbstractController
{
    public $_cityModel = '';
    protected $_cityLanguageModel = '';
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
        $this->middleware('accessrights');
    }


    /**
     * Show the All city records
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin.catalog.order_add',array('currentClass'=>$this));
    }

    /**
    * Show the All city records
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    public function option()
    {
        return view('admin.catalog.order_add_old',array('currentClass'=>$this));
    }

}
