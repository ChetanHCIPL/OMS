<?php ## Created by Iva Nirmal as on 20th August 2019

namespace App\GlobalClass;

use App\Traits\GuzzleRequest;

class GeneralFunction
{
    use GuzzleRequest;

    /**
     * Function: Get Master Data (API Call)
     *
     * @param   array $data_request
     * @param   array $where_arr
     * @return  array $data
     */
    public static function getMasterData($data_request, $where_arr = array()){
        ## API: Master Data 
        $params = array(
            'data_request' => $data_request,
        );
        $response = self::post('get-master-data', $params);
        ## Return Response
        $data = isset($response['data'])?$response['data']:array(); 
        return $data;
    }

	/**
     * Function: Get CMS Page Data (API Call)
     *
     * @param   array $where_arr
     * @return  array $data
     */
    public static function getCMSPageData($where_arr = array()){
        
        ## API: CMS Page Data 
        $response = self::post('getcmspagedata', $where_arr);
        ## Return Response
        $data = isset($response['data'])?$response['data']:array(); 
        return $data;
    }

	/**
     * Function: Get Recent Sold Items (API Call)
     *
     * @param   array $where_arr
     * @return  array $data
     */
    public static function getRecentSoldData($where_arr = array()){
        ## API: Recent Sold Items Data 
        $response = self::post('get-recent-sold-auctions', $where_arr);
        ## Return Response
        $data = isset($response['data'])?$response['data']:array(); 
        return $data;
    }

	/**
     * Function: Get Category Data (API Call)
     *
     * @param   array $where_arr
     * @return  array $data
     */
    public static function getCategoryData($where_arr = array()){
        ## API: Category Data 
        $response = self::post('get-category-data', $where_arr);
        ## Return Response
        $data = isset($response['data'])?$response['data']:array(); 
        return $data;
    }

    /**
     * Function: Get Latest User Auth Token (API Call)
     *
     * @return  array
     */
    public static function getLatestUserAuthToken(){
        ## API: Get Latest User Auth Token
        return self::post('get-latest-user-auth-token');
    }
}
