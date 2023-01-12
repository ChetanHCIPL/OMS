<?php ## Created by Iva Nirmal as on 20th Aug 2019

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\GuzzleRequest;
use Illuminate\Support\Facades\Response;
use Exception;
use config;
use Auth;

class HomeController extends Controller
{
	use GuzzleRequest;
    /**
     * Function: Load index page
     *
     * @return   view
     */
    public function index()
    {
    	if (Auth::guard('admin')->check()) {   // Check is user logged in
	        return redirect()->route('admin.dashboard');
	    } else {
	        return view('admin.auth.login');
	    }
	}
	/**
     * Function: Get Live Auction Data
     *
     * @return response array
     */
	public function getLiveAuction(){
		## API Call: Get live auctions
		$live_param = array();
		$AUCTION_RECORDS_ON_HOME_PAGE = config('settings.AUCTION_RECORDS_ON_HOME_PAGE');
		if($AUCTION_RECORDS_ON_HOME_PAGE > 0){
       		$live_param = array('limit' => $AUCTION_RECORDS_ON_HOME_PAGE);	
       	}else{
       		$live_param = array('limit' => '12');
       	}
		$response_live = $this->post('get-active-auction-list',$live_param);
		$live_data = isset($response_live['data'])?$response_live['data']:array();
		if (count($live_data)) {
			for($i=0;$i<count($live_data);$i++){
				$end_time = isset($live_data[$i]['end_date_time'])?date('Y/m/d H:i:s',strtotime($live_data[$i]['end_date_time'])):'';
				$live_data[$i]['end_date_time'] = $end_time;

				$auction_name = isset($live_data[$i]['auction_name'])?(strlen($live_data[$i]['auction_name']) >= 25) ? substr($live_data[$i]['auction_name'],0,25).'...' :$live_data[$i]['auction_name']:'';

				$live_data[$i]['auction_name'] = $auction_name;
			}
		}
		
		$count_live = ceil(count($live_data)/8);
		$live_chunk_data = array_chunk($live_data,$count_live,true);
		return Response::json($live_chunk_data); 
	}
}
