<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\GuzzleRequest;
use Illuminate\Support\Facades\Response;
use Exception;
use config;

class TestPaymentController extends Controller
{
	use GuzzleRequest;

    public function index()
    {
		return view('test.pay_form');
	}

	public function sampleImageUpload(Request $request){
		$image = $request->file('image');
		$file_real_path = $image->getRealPath();
		$fileExt        = $image->getClientOriginalExtension();
	    $data = file_get_contents($file_real_path);
	    $base64 = 'data:image/' . $fileExt . ';base64,' . base64_encode($data);
	    $param = array('image' => $base64);
		$response = $this->post('sample-image-upload', $param);
		echo "<pre>";
		print_r($response);
		exit;
	}
}
