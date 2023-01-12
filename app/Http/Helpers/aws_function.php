<?php
/**
 * Store Origional file in AWS S3 Server
 * @param string Tempory file Name
 * @param string Storage Folder path 
 * @param string File Name
 * @param object File Object 
 */
function storeFileinAWS($fileTmpName, $destinationPath, $fileName, $File){
    
    $filePath = $fileName;
    $s3 = \Storage::disk('s3');
    $s3->put($filePath, file_get_contents($File), 'public');
    
}
/**
 * Store Origional files in AWS S3 Server
 * @param string Tempory file Name
 * @param string Storage Folder path
 * @param string File Name Array
 * @param object Multiple Files Object
 */
function storeMultipleFilesinAWS($fileTmpName, $destinationPath, $fileName, $Files){
    
    $s3 = \Storage::disk('s3');
    if(is_array($Files)){
        foreach($Files as $File){
            $fileNameNew = $fileName[$File->getClientOriginalName()];
            $filePath = $fileNameNew;
            $s3->put($filePath, file_get_contents($File), 'public');
        }
    }    
}

/**
 * Store Images in AWS S3 Server with Multiple Resizes
 * @param string Tempory file Name
 * @param string Storage Folder path
 * @param object Image Object
 * @param array Size array for Multiple Sizes of Image
 */
function storeImageinAWS($fileTmpName, $destinationPath, $vImage, $size_array){

    if (!empty($size_array)) {
        $s3 = Storage::disk('s3');
        if(Storage::disk('s3')->exists($destinationPath)){
            $s3->makeDirectory($destinationPath);
        }
        $i = 1;
        foreach ($size_array as $rowSizeArray) {
            $filePath = $destinationPath . $i . '_' . $vImage;
            $i++;
            $imgExt = pathinfo($vImage, PATHINFO_EXTENSION);
            $img = Image::make($fileTmpName);

            if($rowSizeArray["W"] != '' && $rowSizeArray["H"] != ''){
                $img->resize($rowSizeArray["W"], $rowSizeArray["H"]);
            }
           
            $img->encode($imgExt);
            $s3->put($filePath, $img->__toString(), 'public');


        }
    }
}
/**
 * Store Multiple Images in AWS S3 Server with Multiple Resizes
 * @param string Tempory file Name
 * @param string Storage Folder path
 * @param object Multiple Image Object
 * @param array Size array for Multiple Sizes of Image
 */
function storeMultipleImagesinAWS($fileTmpName, $destinationPath, $vImages, $size_array){
    if (!empty($size_array) && is_array($vImages)) {
        $s3 = Storage::disk('s3');
        if(Storage::disk('s3')->exists($destinationPath)){
            $s3->makeDirectory($destinationPath);
        }
        foreach($vImages as $vImage){
            $i = 1;
            foreach ($size_array as $rowSizeArray) {
                $filePath = $destinationPath . $i . '_' . $vImage;
                $i++;
                $imgExt = pathinfo($vImage, PATHINFO_EXTENSION);
                $img = Image::make($fileTmpName);
                if($rowSizeArray["W"] != '' && $rowSizeArray["H"] != ''){
                    $img->resize($rowSizeArray["W"], $rowSizeArray["H"]);
                }
                
                $img->encode($imgExt);
                $s3->put($filePath, $img->__toString(), 'public');
            }
        }
    }
}
/**
 * Store Images in Defined folder of AWS S3 Server with Multiple Resizes
 * @param string Tempory file Name
 * @param string Storage Folder path
 * @param object Image Object
 * @param array Size array for Multiple Sizes of Image
 */
function storeImageinDefinedFolderofAWS($fileTmpName, $destinationPath, $vImage, $size_array){
    if (!empty($size_array)) {
        $s3 = Storage::disk('s3');
        if(Storage::disk('s3')->exists($destinationPath)){
            $s3->makeDirectory($destinationPath);
        }
        $i = 1;
        foreach ($size_array as $rowSizeArray) {
            // Create Folder Here
            $s3->makeDirectory($i);
            $filePath = $destinationPath . $i . '/' . $vImage;
            $i++;
            $imgExt = pathinfo($vImage, PATHINFO_EXTENSION);
            $img = Image::make($fileTmpName);
            if($rowSizeArray["W"] != '' && $rowSizeArray["H"] != ''){
                $img->resize($rowSizeArray["W"], $rowSizeArray["H"]);
            }
            
            $img->encode($imgExt);
            $s3->put($filePath, $img->__toString(), 'public');
        }
    }
}
/**
 * Create Folder in AWS S3 Server
 * @param string Folder Name
 */
function createFolderinAWS($folderName){
    $s3 = Storage::disk('s3');
    $s3->makeDirectory($folderName);
}

/**
* Image from AWS S3 Server
* @param Prefined Path (user), Prefix for image,imagename
*/
function getImagefromAWS($prePath,$imagePrefix,$image)
{
    $imgUrl = '';
    $destinationPath = Config::get('filesystems.disks.s3.url').Config::get('filesystems.disks.s3.bucket')."/";
    $imagePath = $prePath.$imagePrefix.$image;
    // $isExistFlag = isAwsFileExist($imagePath);
    // if($isExistFlag){
        $imgUrl = $destinationPath.$imagePath;
    // }  
    return $imgUrl;
}

/**
* Check AWS S3 Server FILE Exists
* @param URL to AWS S3 Server
* @return bool
*/
function isAwsFileExist($awsUrl)
{
    return Storage::disk('s3')->exists($awsUrl);
}

## Remove Images from aws server with multiple size
function deleteImageFromAWS($image, $destinationPath, $size_array){
    $s3 = Storage::disk('s3');
    $cnt = count($size_array);
    if(is_array($image)){
        if(!empty($image)){
            $imgCnt = count($image);
            for ($j = 0; $j < $imgCnt; $j++){
                for ($i = 1; $i <= $cnt; $i++) {
                    $filePath = $destinationPath . $i . '_' . $image[$j];
                    if ($s3->exists($filePath) == 1) {
                        $s3->delete($filePath);
                    }
                }
            }
        }
    }else{
        for ($i = 1; $i <= $cnt; $i++) {
            $filePath = $destinationPath . $i . '_' . $image;
            if ($s3->exists($filePath) == 1) {
                $s3->delete($filePath);
            }
        }
    }
}

## Remove Images from aws server with multiple size
function deleteImageFromDefinedFolderofAWS($image, $destinationPath, $size_array){
    $s3 = Storage::disk('s3');
    $cnt = count($size_array);
    if(is_array($image)){
        if(!empty($image)){
            $imgCnt = count($image);
            for ($j = 0; $j < $imgCnt; $j++){
                for ($i = 1; $i <= $cnt; $i++) {
                    $filePath = $destinationPath . $i . '/' . $image[$j];
                    if ($s3->exists($filePath) == 1) {
                        $s3->delete($filePath);
                    }
                }
            }
        }
    }else{
        for ($i = 1; $i <= $cnt; $i++) {
            $filePath = $destinationPath . $i . '/' . $image;
            if ($s3->exists($filePath) == 1) {
                $s3->delete($filePath);
            }
        }
    }
}

/**
* Function to Remove SubFolder which are created using Parent Id
*/
function deleteSingleOrMultiSubFolderFromAWS($folderName){
    $s3 = Storage::disk('s3');
    if(is_array($folderName) && !empty($folderName)){
        foreach($folderName as $name){
            $s3->deleteDirectory($name);
        }
    }else{
        $s3->deleteDirectory($folderName);
    }
    
}



