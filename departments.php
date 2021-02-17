<?php
/**
 * 16/02/21 C.Mooney
 * 
 * This php script with get all or get rows accodring to department.
 * In live the table 'departments' resdies in the mooneycallans.com phpMyAdmon SQL 
 * usermanual Database, and table 'departments'
 * 
 * The API is in mooneycallans server uder /public_html/crud_api/api
 * 
 * In test it resdis in XAMPP localhot phpMyAdmin (double click LAMP in Applications)
 * Tested in POSTMAN  https://grey-meteor-482790.postman.co/workspace/bf6898e8-9ad1-407b-887d-e0cdc42d0f49/history/14041089-0d2a84f5-9ad9-420c-afda-0033eccb928d
 * 
 * The call made to the API form the usermanl app passes a string representing
 * the department. 
 */
require_once 'headers.php';

//LIVE CONFIG: Set up connection details on mooneycalla.com - Server, username, password, database name
//$conn = new mysqli('www.mooneycallans.com','m558405_test','usermanual','m558405_usermanual');
//$table = "departments";



/**
 * Test databse confi on localhost XAMMP (to run start LAMP app), go to localhost:80
 * Test url in POST Man http://localhost/crud_api/api/departments.php/departments/ for getall
 * test url for just Haematology: http://localhost/crud_api/api/departments.php/departments/?departmentName=Haematology
 */
$conn = new mysqli('localhost','root','','Usermanual');
$table = 'departments';


//Check connection
if ($conn -> connect_error){
    die("Connection Failed: " .$conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    /**
     * Levae the option of either retriving rows matching a 
     * dept name or just retirn the entire table.
     * 'dept' is defied as a string representing the deprtment 
     * in the URL GET Call in the app
     * POSTMAN http://localhost/crud_api/api/departments.php/departments/
     */

     if (isset($_GET['departmentName'])){
         //Fetch all rows with departName passed to GET call
         $department = $conn->real_escape_string($_GET['departmentName']);
         $sql = $conn->query("SELECT * FROM $table WHERE departmentName = '$department'");
         $data = $sql->fetch_assoc();//return row /s as an array

     }else{
         //If deprtmentname not found retun all rows
         $data = array();
         $sql = $conn->query("SELECT * FROM $table");
         while($d = $sql->fetch_assoc()){
             //add every row $d returned to teh data array
             $data[] = $d;
         }
     }

     //Reunr the JSON reprsenation of the data
     exit(json_encode($data));

}


if ($_SERVER['REQUEST_METHOD'] ===  'POST'){
    /**
     * Add a new row, ID is autmatically assigned so
     * does not need to be specifed when teh method is called
     * 
     * POSTMAN : POST http://localhost/crud_api/api/departments.php/departments
     * ake sure you post an JSON obet iwth DB vales (see instructions)
     */

     $data = json_decode(file_get_contents("php://input"));//get data passed to function
     $sql = $conn->query("INSERT INTO $table (departmentName, mainNumber,extension,email,newsAlerts,general,openingHours,oncall) VALUES ('".$data->departmentName."','".$data->mainNumber."','".$data->extension."','".$data->email."','".$data->newsAlerts."','".$data->general."','".$data->openingHours."','".$data->oncall."')");

     if ($sql){
         //if sql query valid insert id and return $sql object
         $data->id = $conn->insert_id;
         exit(json_encode($data));//return SQL insert as JSON to caller or test in POSTMAN
     }else{
         exit(json_encode(array('status' => 'error: ', 'SQL - ' => '$sql')));
     }
}

if ($_SERVER['REQUEST_METHOD']==='PUT'){
    /**
     * Update a row according to ID
     */
    if (isset($_GET['id'])){
    $id = $conn->real_escape_string($_GET['id']);
        //id is apssed during te call to this function
        $data = json_decode(file_get_contents("php://input"));
        $sql = $conn -> query("UPDATE $table SET departmentName= '".$data->departmentName."', mainNumber = '".$data->mainNumber."',
        extension = '".$data->extension."', email = '".$data->email."', newsAlerts = '".$data->newsAlerts."',
        general = '".$data->general."', openingHours = '".$data->openingHours."', oncall = '".$data->oncall."' WHERE id = '$id'");


        if ($sql){
            exit(json_encode(array('status' => 'success')));
        }else{
            exit(json_encode(array('status' => 'error', 'SQL command-' => '$sql')));
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE'){
    /**
     * Delete accoring to the id of teh row
     * passed form teh function call
     * 
     * POSTMAN test : http://localhost/crud_api/api/departments.php/departments/?id=1
     * Where id is the row id to delete
     */
    if (isset($_GET['id'])){
        $id = $conn -> real_escape_string($_GET['id']);
        $sql = $conn->query("DELETE FROM $table WHERE departments.id = '$id'");

        //If SQL commend succeds or fails retun message to caller
        if ($sql){
            exit(json_encode(array('status' => 'success')));
        }else{
            exit(json_encode(array('status'=>'error id not deleted')));
        }
    }
}




?>