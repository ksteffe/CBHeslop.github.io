<?php

    function checkEmail($clientEmail) {
        $valEmail = filter_var($clientEmail, FILTER_VALIDATE_EMAIL);
        return $valEmail;
    }


    // Check the password for a minimum of 8 characters,
    // at least one 1 capital letter, at least 1 number and
    // at least 1 special character
    function checkPassword($clientPassword) {
        $pattern = '/^(?=.*[[:digit:]])(?=.*[[:punct:]])(?=.*[A-Z])(?=.*[a-z])([^\s]){8,}$/';
        return preg_match($pattern, $clientPassword);
    }


    function dynaNavigation() {
        // Build a navigation bar using the $categories array
        $categories = getCategories();

        $navList = '<ul>';
        $navList .= "<li><a href='/acme/index.php' title='View the Acme home page'>Home</a></li>";
        foreach ($categories as $category) {
            $navList .= "<li><a href='/acme/products/?action=category&categoryName="
            .urlencode($category['categoryName']).
            "' title='View our $category[categoryName] product line'>$category[categoryName]</a></li>";
        }
        $navList .= '</ul>';

        return $navList;
    }

    function catDropDownList() {
         // Build a dropDown list using the $categories array
        $categories = getCategories();
/*
        $catList = "<select name='categoryId' id='categoryId'>";
        $catList .= "<option>Choose a Category</option>"; 
        foreach ($categories as $category) {
            $catList .= "<option value=" . $category['categoryId'] . ">" . $category['categoryName'] . "</option>";
        }
        $catList .= '</select>';

        */
        // Build the categories option list
        $catList = '<select name="catType" id="catType">';
        $catList .= "<option>Choose a Category</option>";
        foreach ($categories as $category) {
            $catList .= "<option value='$category[categoryId]'";
            if(isset($catType)){
                if($category['categoryId'] === $catType){
                    $catList .= ' selected ';
                }
            } elseif(isset($prodInfo['categoryId'])){
                if($category['categoryId'] === $prodInfo['categoryId']){
                    $catList .= ' selected ';
                }
            }
        $catList .= ">$category[categoryName]</option>";
        }
        $catList .= '</select>';

        return $catList;

    }

    // Build the categories select list 
    function buildCategoryList($categories) { 
        $catList = '<select name="categoryId" id="categoryList">'; 
        $catList .= "<option>Choose a Category</option>"; 
        foreach ($categories as $category) { 
            $catList .= "<option value='$category[categoryId]'>$category[categoryName]</option>"; 
        } 
        $catList .= '</select>'; 
        return $catList; 
   }

   // Build a display of products from an unordered list
   function buildProductsDisplay($products){
        $pd = '<ul id="prod-display">';
        foreach ($products as $product) {
            $pd .= '<li>';
            //$pd .= "<img src='$product[invThumbnail]' alt='Image of $product[invName] on Acme.com'>";
            $pd .= "<a href='/acme/products/?action=itemInformation&invId=".urlencode($product['invId'])."'><img src='$product[invThumbnail]' alt='Image of $product[invName] on Acme.com'></a>";
            $pd .= '<hr>';
            //$pd .= "<h2>$product[invName]</h2>";
            $pd .= "<a href='/acme/products/?action=itemInformation&invId=".urlencode($product['invId'])."'><h2>$product[invName]</h2></a>";
            $pd .= "<span>$$product[invPrice]</span>";
            $pd .= '</li>';
        }
        $pd .= '</ul>';
        return $pd;
   }

   // Build a display for product information when clicked
   function buildProductsInfoDisplay($productInformation) {
        $productInfo = '<div id="prod-display">';
        foreach ($productInformation as $product) {
            $productInfo .= '<div>';
                $productInfo .= "<img src='$product[invImage]' alt='Image of $product[invName] on Acme.com'>";
            $productInfo .= '</div>';
            $productInfo .= '<ul>';
                $productInfo .= '<li>';
                $productInfo .= "<h2>$product[invName]</h2>";
                $productInfo .= "<h2>Product Description: $product[invDescription]</h2>";
                $productInfo .= "<h2>A $product[invVendor] product</h2>";
                $productInfo .= "<h2>Primary Material: $product[invStyle]</h2>";
                $productInfo .= "<h2>Product Weight: $product[invWeight]</h2>";
                $productInfo .= "<h2>Ships From: $product[invLocation]</h2>";
                $productInfo .= "<h2>Number In Stock: $product[invStock]</h2>";
                $productInfo .= "<span>Product Price: $$product[invPrice]</span>";
                $productInfo .= '</li>';
            $productInfo .= '</ul>';
        }
        $productInfo .= '</div>';
        return $productInfo;
   }


    function buildImagesInfoDisplay($thumbnailImage) {
        $imageInfo = '<div id="product-display">';
        foreach ($thumbnailImage as $images) {
            $imageInfo .= '<div>';
                $imageInfo .= "<img src='$images[imgPath]' alt='Image of $images[imgName] on Acme.com'>";
            $imageInfo .= '</div>';
        }
        $imageInfo .= '</div>';
        return $imageInfo;
    }


    /* * ********************************
    *  Functions for working with images
    * ********************************* */

    // Adds "-tn" designation to file name
    function makeThumbnailName($image) {
        $i = strrpos($image, '.');
        $image_name = substr($image, 0, $i);
        $ext = substr($image, $i);
        $image = $image_name . '-tn' . $ext;
        return $image;
    }

    // Build images display for image management view
    function buildImageDisplay($imageArray) {
        $id = '<ul id="image-display">';
        foreach ($imageArray as $image) {
            $id .= '<li>';
            $id .= "<img src='$image[imgPath]' title='$image[invName] image on Acme.com' alt='$image[invName] image on Acme.com'>";
            $id .= "<p><a href='/acme/uploads?action=delete&imgId=$image[imgId]&filename=$image[imgName]' title='Delete the image'>Delete $image[imgName]</a></p>";
            $id .= '</li>';
        }
        $id .= '</ul>';
        return $id;
    }

    // Build the products select list
    function buildProductsSelect($products) {
        $prodList = '<select name="invId" id="invId">';
        $prodList .= "<option>Choose a Product</option>";
        foreach ($products as $product) {
            $prodList .= "<option value='$product[invId]'>$product[invName]</option>";
        }
        $prodList .= '</select>';
        return $prodList;
    }

    // Handles the file upload process and returns the path
    // The file path is stored into the database
    function uploadFile($name) {
        // Gets the paths, full and local directory
        global $image_dir, $image_dir_path;
        if (isset($_FILES[$name])) {
            // Gets the actual file name
            $filename = $_FILES[$name]['name'];
            if (empty($filename)) {
                return;
            }
        // Get the file from the temp folder on the server
        $source = $_FILES[$name]['tmp_name'];
        // Sets the new path - images folder in this directory
        $target = $image_dir_path . '/' . $filename;
        // Moves the file to the target folder
        move_uploaded_file($source, $target);
        // Send file for further processing
        processImage($image_dir_path, $filename);
        // Sets the path for the image for Database storage
        $filepath = $image_dir . '/' . $filename;
        // Returns the path where the file is stored
        return $filepath;
        }
    }

    // Processes images by getting paths and 
    // creating smaller versions of the image
    function processImage($dir, $filename) {
        // Set up the variables
        $dir = $dir . '/';
    
        // Set up the image path
        $image_path = $dir . $filename;
    
        // Set up the thumbnail image path
        $image_path_tn = $dir.makeThumbnailName($filename);
    
        // Create a thumbnail image that's a maximum of 200 pixels square
        resizeImage($image_path, $image_path_tn, 200, 200);
    
        // Resize original to a maximum of 500 pixels square
        resizeImage($image_path, $image_path, 500, 500);
    }

    // Checks and Resizes image
    function resizeImage($old_image_path, $new_image_path, $max_width, $max_height) {
        
        // Get image type
        $image_info = getimagesize($old_image_path);
        $image_type = $image_info[2];
    
        // Set up the function names
        switch ($image_type) {
        case IMAGETYPE_JPEG:
            $image_from_file = 'imagecreatefromjpeg';
            $image_to_file = 'imagejpeg';
        break;
        case IMAGETYPE_GIF:
            $image_from_file = 'imagecreatefromgif';
            $image_to_file = 'imagegif';
        break;
        case IMAGETYPE_PNG:
            $image_from_file = 'imagecreatefrompng';
            $image_to_file = 'imagepng';
        break;
        default:
            return;
    } // ends the resizeImage function
    
        // Get the old image and its height and width
        $old_image = $image_from_file($old_image_path);
        $old_width = imagesx($old_image);
        $old_height = imagesy($old_image);
    
        // Calculate height and width ratios
        $width_ratio = $old_width / $max_width;
        $height_ratio = $old_height / $max_height;
    
        // If image is larger than specified ratio, create the new image
        if ($width_ratio > 1 || $height_ratio > 1) {
    
            // Calculate height and width for the new image
            $ratio = max($width_ratio, $height_ratio);
            $new_height = round($old_height / $ratio);
            $new_width = round($old_width / $ratio);
    
            // Create the new image
            $new_image = imagecreatetruecolor($new_width, $new_height);
    
            // Set transparency according to image type
            if ($image_type == IMAGETYPE_GIF) {
                $alpha = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
                imagecolortransparent($new_image, $alpha);
            }
    
            if ($image_type == IMAGETYPE_PNG || $image_type == IMAGETYPE_GIF) {
                imagealphablending($new_image, false);
                imagesavealpha($new_image, true);
            }
    
            // Copy old image to new image - this resizes the image
            $new_x = 0;
            $new_y = 0;
            $old_x = 0;
            $old_y = 0;
            imagecopyresampled($new_image, $old_image, $new_x, $new_y, $old_x, $old_y, $new_width, $new_height, $old_width, $old_height);
    
            // Write the new image to a new file
            $image_to_file($new_image, $new_image_path);
            // Free any memory associated with the new image
            imagedestroy($new_image);
            } else {
            // Write the old image to a new file
            $image_to_file($old_image, $new_image_path);
            }
            // Free any memory associated with the old image
            imagedestroy($old_image);
    } // ends the if - else began on line 36


    // Function for display the review form
    function reviewForm() {
        
    }

    // Function for displaying the reviews
    function reviewInfoDisplay($regOutcome, $screenName) {
        $reviewDisplay = '<div id="review-display">';
        $reviewDisplay .= "<p>'$screenName'</p>";
        foreach ($regOutcome as $review) {
            $reviewDisplay .= "<h2>'$review[reviewDate]':</h2>";
        }
        
        $reviewDisplay .= "<p>'$review[reviewText]'</p>";       
    
        $reviewDisplay .= '</div>';
        return $reviewDisplay;
    }

?>