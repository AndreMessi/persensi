<?php
// SET HEADER
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PUT");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// INCLUDING DATABASE AND MAKING OBJECT
require '../config/database.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();

// GET DATA FORM REQUEST
$data = json_decode(file_get_contents("php://input"));

//CHECKING, IF ID AVAILABLE ON $data
if(isset($data->id)){
    
    $msg['message'] = '';
    $post_id = $data->id;
    
    //GET POST BY ID FROM DATABASE
    $get_post = "SELECT * FROM absen WHERE id=:post_id";
    $get_stmt = $conn->prepare($get_post);
    $get_stmt->bindValue(':post_id', $post_id,PDO::PARAM_INT);
    $get_stmt->execute();
    
    
    //CHECK WHETHER THERE IS ANY POST IN OUR DATABASE
    if($get_stmt->rowCount() > 0){
        
        // FETCH POST FROM DATBASE 
        $row = $get_stmt->fetch(PDO::FETCH_ASSOC);
        
        // CHECK, IF NEW UPDATE REQUEST DATA IS AVAILABLE THEN SET IT OTHERWISE SET OLD DATA
        $post_title = isset($data->nama) ? $data->nama : $row['nama'];
        $post_body = isset($data->jam_masuk) ? $data->jam_masuk : $row['jam_masuk'];
        $post_author = isset($data->jabatan) ? $data->jabatan : $row['jabatan'];
        $post_status = isset($data->status) ? $data->status : $row['status'];
        
        $update_query = "UPDATE absen SET nama = :nama, jam_masuk = :jam_masuk, jabatan = :jabatan, status = :status 
        WHERE id = :id";
        
        $update_stmt = $conn->prepare($update_query);
        
        // DATA BINDING AND REMOVE SPECIAL CHARS AND REMOVE TAGS
        $update_stmt->bindValue(':nama', htmlspecialchars(strip_tags($post_title)),PDO::PARAM_STR);
        $update_stmt->bindValue(':jam_masuk', htmlspecialchars(strip_tags($post_body)),PDO::PARAM_STR);
        $update_stmt->bindValue(':jabatan', htmlspecialchars(strip_tags($post_author)),PDO::PARAM_STR);
        $update_stmt->bindValue(':status', htmlspecialchars(strip_tags($post_status)),PDO::PARAM_STR);
        $update_stmt->bindValue(':id', $post_id,PDO::PARAM_INT);
        
        
        if($update_stmt->execute()){
            $msg['message'] = 'Data updated successfully';
        }else{
            $msg['message'] = 'data not updated';
        }   
        
    }
    else{
        $msg['message'] = 'Invlid ID';
    }  
    
    echo  json_encode($msg);
    
}
?>