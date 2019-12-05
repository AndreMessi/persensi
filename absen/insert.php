<?php
// SET HEADER
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// INCLUDING DATABASE AND MAKING OBJECT
require '../config/database.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();

// GET DATA FORM REQUEST
$data = json_decode(file_get_contents("php://input"));

//CREATE MESSAGE ARRAY AND SET EMPTY
$msg['message'] = '';
// CHECK IF RECEIVED DATA FROM THE REQUEST
if(isset($data->nama) && isset($data->jam_masuk) && isset($data->jabatan) && isset($data->status)){
    // CHECK DATA VALUE IS EMPTY OR NOT
    if(!empty($data->nama) && !empty($data->jam_masuk) && !empty($data->jabatan) && !empty($data->status)){
        
        $insert_query = "INSERT INTO absen (nama,jam_masuk,jabatan,status) VALUES(:nama,:jam_masuk,:jabatan,:status)";
        
        $insert_stmt = $conn->prepare($insert_query);
        // DATA BINDING
        $insert_stmt->bindValue(':nama', htmlspecialchars(strip_tags($data->nama)),PDO::PARAM_STR);
        $insert_stmt->bindValue(':jam_masuk', htmlspecialchars(strip_tags($data->jam_masuk)),PDO::PARAM_STR);
        $insert_stmt->bindValue(':jabatan', htmlspecialchars(strip_tags($data->jabatan)),PDO::PARAM_STR);
        $insert_stmt->bindValue(':status', htmlspecialchars(strip_tags($data->status)),PDO::PARAM_STR);
        
        if($insert_stmt->execute()){
            $msg['message'] = 'Data Inserted Successfully';
        }else{
            $msg['message'] = 'Data not Inserted';
        } 
        
    }else{
        $msg['message'] = 'Oops! empty field detected. Please fill all the fields';
    }
}
else{
    $msg['message'] = 'Please fill all the fields | title, body, author';
}
//ECHO DATA IN JSON FORMAT
echo  json_encode($msg);
?>