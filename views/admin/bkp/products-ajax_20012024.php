<?php 
require('includes/config.php');

session_start();

if(@$_POST['formName']=='warrantyform'){

    $firstName=$_POST['firstName'];
    $lastName=$_POST['lastName'];
    $address=$_POST['address'];
    $facebook=$_POST['facebook'];
    $email=$_POST['email'];
    $phone=$_POST['phone'];
    $twitter=$_POST['twitter'];
    $googlePlus=$_POST['googlePlus'];
    $dealerEmail=$_POST['dealerEmail'];
    $pass=$_POST['pass'];

    $selectProds=$dbConn->execute("INSERT INTO contact_form_leads(first_name,last_name,lead_email,lead_phone,lead_city,lead_state,lead_note)VALUES('$firstName','$lastName','$email','$phone','$city','$state','$message')");
    if($selectProds){
        echo 0;
    }else{
        echo 1;
    }

}


if(@$_POST['formName']=='contactform_submit'){

    $firstName=$_POST['firstName'];
    $lastName=$_POST['lastName'];
    $city=$_POST['city'];
    $state=$_POST['state'];
    $email=$_POST['email'];
    $phone=$_POST['phone'];
    $message=$_POST['message'];

    $selectProds=$dbConn->execute("INSERT INTO contact_form_leads(first_name,last_name,lead_email,lead_phone,lead_city,lead_state,lead_note)VALUES('$firstName','$lastName','$email','$phone','$city','$state','$message')");
    if($selectProds){
        echo 0;
    }else{
        echo 1;
    }

}

if(@$_POST['formName']=='filter-sort'){

    $loadMore=$_POST['loadMore'];
    
    if($_POST['finishes']==''){

        $finishesCheck='';

    }else{
        $finishArr=explode(',',$_POST['finishes']);      
        $finishesCheck=''; 
        
        for($k=0;$k<count($finishArr);$k++){
            if($k==0){
                $finishesCheck.=" AND ";
            }else{
                $finishesCheck.=" OR ";
            }

            $finishesCheck.='prod_finishes like \'%"'.$finishArr[$k].'"%\'';
    
        }

    }


    if($_POST['finishes']!=''){
        $condition=' OR ';
    }else{
        $condition=' AND ';
    }

    $stoneCats=$_POST['stoneCats']=='' ? '' : $condition.' prod_stone_cat IN ('.$_POST['stoneCats'].') ';

    if($_POST['colors']==''){
        $colors='';
    }else{
        $colors='';

        $colorArr=explode('|',$_POST['colors']);        
        for($j=0;$j<count($colorArr);$j++){

            if($j==0){
                

                if($_POST['stoneCats']=='' && $_POST['finishes']=='' ){
                    $condition=' AND ';
                }else{
                    $condition=' OR ';
                }

                $colors.=$condition;
                
            }else{
                $colors.=" OR ";
            }

            $colors.="prod_primary_color like '%".$colorArr[$j]."%'";
        }

    }

    // $colors=$_POST['colors']=='' ? '' : $condition.' prod_primary_color IN ("'.$_POST['colors'].'") ';

    if($_POST['stoneCats']=='' && $_POST['finishes']=='' && $_POST['colors']==''){
        $condition=' AND ';
    }else{
        $condition=' OR ';
    }
    $sizes=$_POST['sizes']=='' ? '' : $condition.' ( prod_slab_size1 IN ("'.$_POST['sizes'].'") OR prod_slab_size2 IN ("'.$_POST['sizes'].'") )';
    $sortVal=$_POST['sortVal']==0 ? "created_date DESC" : ( $_POST['sortVal']==1 ? 'prod_name ASC' : 'prod_stone_cat DESC' ) ;
    $selectProds=$dbConn->execute("SELECT prod_id,prod_stone_cat,prod_name,prod_primary_color , prod_slab_size1 , prod_slab_size2 ,prod_stone_cat , prod_finishes ,prod_images From products_data WHERE prod_images IS NOT NULL AND prod_status!=1 $finishesCheck $stoneCats $colors $sizes ORDER BY $sortVal limit $loadMore");

    // echo "SELECT prod_id,prod_stone_cat,prod_name,prod_primary_color , prod_slab_size1 , prod_slab_size2 ,prod_stone_cat , prod_finishes ,prod_images From products_data WHERE prod_images IS NOT NULL AND prod_status!=1 $finishesCheck $stoneCats $colors $sizes ORDER BY $sortVal limit $loadMore";

    $products='';
     while($selectProdsRow=mysqli_fetch_array($selectProds)){ 

        $products.='<a href="product-view.php?data='.$selectProdsRow['prod_id'].'" class="wrap" data-color="'.$selectProdsRow['prod_primary_color'].'" data-finish="'.implode(',',json_decode($selectProdsRow['prod_finishes'])).'" data-cat="'.$selectProdsRow['prod_stone_cat'].'" data-size1="'.$selectProdsRow['prod_slab_size1'].'" data-size2="'.$selectProdsRow['prod_slab_size2'].'" >
            <img src="images/resized/'.json_decode($selectProdsRow['prod_images'])[0].'">
            <h5>'.$selectProdsRow['prod_name'].'</h5>
        </a>';

     }
     echo $products;


}

if(@$_POST['formName']=='uploadImages'){

    $imgArr=array();
    for($i=0;$i< count($_FILES['images']['name']);$i++){
        $fileName = preg_replace('/\s+/', '', $_FILES['images']['name'][$i]);
        copy( $_FILES['images']['tmp_name'][$i] , '../images/'.$fileName);

   // Define the new width and height
   $newWidth = 452; // Change this to your desired width
   $newHeight = 250; // Change this to your desired height

   // Get the original image details
   $originalImagePath = $_FILES['images']['tmp_name'][$i];
   list($originalWidth, $originalHeight, $type) = getimagesize($originalImagePath);

   // Create a new image with the desired width and height
   $newImage = imagecreatetruecolor($newWidth, $newHeight);

   // Load the original image
   switch ($type) {
       case IMAGETYPE_JPEG:
           $originalImage = imagecreatefromjpeg($originalImagePath);
           break;
       case IMAGETYPE_PNG:
           $originalImage = imagecreatefrompng($originalImagePath);
           break;
       case IMAGETYPE_GIF:
           $originalImage = imagecreatefromgif($originalImagePath);
           break;
       default:
           // Handle other image types if needed
           die('Unsupported image type');
   }

   // Resize the original image to fit the new dimensions
   imagecopyresampled($newImage, $originalImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
    // Save the new image
    $newImagePath = "../images/resized/" . image_type_to_extension($type); // Adjust the extension based on the original image type
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($newImage, $newImagePath);
            break;
        case IMAGETYPE_PNG:
            imagepng($newImage, $newImagePath);
            break;
        case IMAGETYPE_GIF:
            imagegif($newImage, $newImagePath);
            break;
        default:
            // Handle other image types if needed
            die('Unsupported image type');
    }

   // Clean up resources
   imagedestroy($originalImage);
   imagedestroy($newImage);

        array_push($imgArr,$fileName);
    }  

    echo json_encode($imgArr);

}

if(@$_POST['formName']=='delete_prod'){
    $prodId=$_POST['prod_id'];
    $query=$dbConn->execute("UPDATE products_data SET prod_status=1 WHERE prod_id=$prodId");
    if($query){
        echo 1;
    }else{
        echo 0;
    }

}

if(@$_POST['formName']=='login'){
    $email=$_POST['email'];
    $password=$_POST['password'];
    $query=$dbConn->execute("SELECT user_id,user_type,user_name,user_log_id FROM users WHERE user_email='$email' AND user_password='$password'");
    // $error=mysqli_error($dbConn);
    $id=mysqli_fetch_array($query);
    if($id['user_id']=='' || $id['user_id']=='undefined'){
        echo 1;
    }else{
        $_SESSION['user_id']=$id['user_id'];
        $_SESSION['user_type']=$id['user_type'];
        $_SESSION['user_name']=$id['user_name'];
        $_SESSION['user_log_id']=$id['user_log_id'];
        echo 0;
    }
}


if(@$_POST['formName']=='productUpload'){

    $imgArr=array();
    for($i=0;$i< count($_POST['images']);$i++){
        array_push($imgArr,$_POST['images'][$i]);
    }
    $postData=json_decode($_POST['postData']);
    $imgArrJson=json_encode($imgArr);
    $prodName=$postData->prodName;
    $primColor=$postData->primColor;
    $stoneCat=$postData->stoneCat;
    $prodOrigin=$postData->prodOrigin;
    $prodSeries=$postData->prodSeries;
    $prodSku=$postData->prodSku;
    $prodFinish=$postData->prodFinishs;
    $priceRange=$postData->priceRange;
    $slabSize1=$postData->slabSize1;
    $slabId1=$postData->slabId1;
    $slabSize2=$postData->slabSize2;
    $flooringResidential=$postData->flooringResidential;
    $flooringCommercial=$postData->flooringCommercial;
    $counterResidential=$postData->counterResidential;
    $counterCommercial=$postData->counterCommercial;
    $wallResidential=$postData->wallResidential;
    $wallCommercial=$postData->wallCommercial;
    $exterior=$postData->exterior;

    $insertProducts=$dbConn->execute("INSERT INTO `products_data` (`prod_name`,`prod_images`, `prod_primary_color`, `prod_origin`, `prod_sku`, `prod_series`, `prod_stone_cat`, `prod_finishes`,`prod_price_range`, `prod_slab_size1`, `prod_slab_id1`, `prod_slab_size2`, `prod_floor_residential`, `prod_floor_commercial`, `prod_counter_residential`, `prod_counter_commercial`, `prod_wall_residential`, `prod_wall_commercial`, `exterior`) VALUES ( '$prodName','$imgArrJson', '$primColor', '$prodOrigin', '$prodSku', '$prodSeries', '$stoneCat','$prodFinish', '$priceRange', '$slabSize1', '$slabId1', '$slabSize2', '$flooringResidential', '$flooringCommercial', '$counterResidential', '$counterCommercial', '$wallResidential', '$wallCommercial', '$exterior')");

    if($insertProducts){
        echo "0";
    }else{
        echo mysqli_error($dbConn->conn);
    }

}

if(@$_POST['formName']=='productUpdate'){

    $imgArr=array();
    for($i=0;$i< count($_POST['images']);$i++){
        array_push($imgArr,$_POST['images'][$i]);
    }
    $postData=json_decode($_POST['postData']);
    $imgArrJson=json_encode($imgArr);
    $prodId=$postData->prodId;
    $prodName=$postData->prodName;
    $primColor=$postData->primColor;
    $stoneCat=$postData->stoneCat;
    $prodOrigin=$postData->prodOrigin;
    $prodSeries=$postData->prodSeries;
    $prodSku=$postData->prodSku;
    $prodFinish=$postData->prodFinishs;
    $priceRange=$postData->priceRange;
    $slabSize1=$postData->slabSize1;
    $slabId1=$postData->slabId1;
    $slabSize2=$postData->slabSize2;
    $flooringResidential=$postData->flooringResidential;
    $flooringCommercial=$postData->flooringCommercial;
    $counterResidential=$postData->counterResidential;
    $counterCommercial=$postData->counterCommercial;
    $wallResidential=$postData->wallResidential;
    $wallCommercial=$postData->wallCommercial;
    $exterior=$postData->exterior;

    $updateProducts = $dbConn->execute("
    UPDATE `products_data`
SET
  `prod_name` = '$prodName',
  `prod_images` = '$imgArrJson',
  `prod_primary_color` = '$primColor',
  `prod_origin` = '$prodOrigin',
  `prod_sku` = '$prodSku',
  `prod_series` = '$prodSeries',
  `prod_stone_cat` = '$stoneCat',
  `prod_finishes` = '$prodFinish',
  `prod_price_range` = '$priceRange',
  `prod_slab_size1` = '$slabSize1',
  `prod_slab_id1` = '$slabId1',
  `prod_slab_size2` = '$slabSize2',
  `prod_floor_residential` = '$flooringResidential',
  `prod_floor_commercial` = '$flooringCommercial',
  `prod_counter_residential` = '$counterResidential',
  `prod_counter_commercial` = '$counterCommercial',
  `prod_wall_residential` = '$wallResidential',
  `prod_wall_commercial` = '$wallCommercial',
  `exterior` = '$exterior'
WHERE
  `prod_id` = $prodId;
");

    if($updateProducts){
        echo "0";
    }else{
        echo mysqli_error($dbConn->conn);
    }

}

?>