<?php include('../_connect.php');?>
<?php include('../../includes/helpers/short.php');?>
<?php 
	//-------------------------- ERRORS -------------------------//
	$error_core = array('No data passed', 'API key not passed', 'Invalid API key');
	$error_passed = array('Name not passed', 'Brand ID not passed.', 'Brand ID not identified');
	//-----------------------------------------------------------//
	
	//--------------------------- POST --------------------------//

	//api_key	
	$api_key = isset($_POST['api_key']) ? mysqli_real_escape_string($mysqli, $_POST['api_key']) : null;
    
	//name
	$name = isset($_POST['name']) ? mysqli_real_escape_string($mysqli, $_POST['name']) : null;

	//brand_id
	$app = isset($_POST['brand_id']) ? mysqli_real_escape_string($mysqli, $_POST['brand_id']) : null;
	
	//----------------------- VERIFICATION ----------------------//
	//Core data
	if($api_key==null && $name==null && $app==null)
	{
		echo $error_core[0];
        http_response_code(400);
		exit;
	}
	if($api_key==null)
	{
		echo $error_core[1];
		http_response_code(400);
        exit;
	}
	else if(!verify_api_key($api_key))
	{
		echo $error_core[2];
        http_response_code(400);
		exit;
	}
	
	//Passed data
	if($name==null)
	{
		echo $error_passed[0];
        http_response_code(400);
		exit;
	}
	else if($app==null)
	{
		echo $error_passed[1];
        http_response_code(400);
		exit;
	}
    
    $q = 'SELECT id FROM apps where app_name = "'.$app.'"';
    $r = mysqli_query($mysqli, $q);
    if (mysqli_num_rows($r) == 0){
        echo $error_passed[2];
        http_response_code(400);
        exit;
    }else{
        while($row = mysqli_fetch_array($r))
            $appId = $row['id'];
    }

    $q = 'SELECT id FROM login ORDER BY id ASC LIMIT 1';
    $r = mysqli_query($mysqli, $q);
    if ($r) 
        while($row = mysqli_fetch_array($r)) 
            $userID = $row['id'];
    
    $q = 'SELECT id FROM lists WHERE name = "'.$name.'"';
    $r = mysqli_query($mysqli, $q);
    if (mysqli_num_rows($r) > 0){
        while($row = mysqli_fetch_array($r)) 
            $listId = $row['id'];
        echo $listId;
        exit;
    }

    $q = 'INSERT INTO lists (name, app, userID) VALUES ("'.$name.'",'.$appId.','.$userID.')';
    $r = mysqli_query($mysqli, $q);
    if($r){
        echo mysqli_insert_id($mysqli);
        exit;
    }else{
        echo 'list not created';
        http_response_code(500);
        exit;
    }

?>
