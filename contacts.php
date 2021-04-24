<?php

/**
* Ciaran Mooney 22/03/2021
* This php script with get all or get rows accodring to department.
 * In live the table 'contacts' resdies in the mooneycallans.com phpMyAdmon SQL 
 * usermanual Database, and table 'contacts'
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
// $conn = new mysqli('www.mooneycallans.com','m558405_test','usermanual','m558405_usermanual');
// $table = 'labcontacts';





/**
 * MAKE SURE TABLE NAME IS NOT SMAE AS FILENAME> OF PHP or wont work
 * Test databse confi on localhost XAMMP (to run start LAMP app), go to localhost:80
 * Test url in POST Man http://localhost/crud_api/api/contacts.php/labcontacts for getall
 * test url for just Haematology: http://localhost/crud_api/api/departments.php/departments/?departmentName=Haematology
 */

 /**
  * Turn on debugging on server side.
  * Outut will be in the server errorlog, same directory 
  * as the php files
  */

  error_reporting(E_ALL);
  ini_set('error_reporting', E_ALL);

 


$conn = new mysqli('localhost','root','','usermanual2');
$table = 'labcontacts';
//$db =  'usermanual2';
/**
   * Make suer the database on server we are reading / writing to as in UTF-8
   * standard
   */
//$conn -> set_charset('utf8');
   //mysqli_set_charset($conn,'utf8');
 /**
   * Make suer the database on server we are reading / writing to as in UTF-8
   * standard
   */

  mysqli_set_charset('usermanual2','utf8');

//Chekc connection_aborted
if($conn -> connect_error){
    die("Connection Failed: " .$conn->connect_error);

}

if ($_SERVER['REQUEST_METHOD'] === 'GET'){
     /**
     * Levae the option of either retriving rows matching a 
     * conatact department  or just retirn the entire table.
     * 'dept' is defied as a string representing the deprtment 
     * in the URL GET Call in the app
     * POSTMAN http://localhost/crud_api/api/contacts.php/contacts/ will retrn allof them
     * http://mooneycallans.com/crud_api/api/contacts.php/departments/?departmentName=Haematology retrun haematology only
     */

     if (isset($_GET['departmentName'])){
         //fetch all rows with contact name/department name  passed to GET 
        $department = $conn->real_escape_string($_GET['departmentName']);
        $sql = $conn-> query("SELECT * FROM $table WHERE department = '$department'");
        //$data = $sql-> fetch_assoc();//retrun row as an array 
        $data = array();
        while ($d = $sql->fetch_assoc()){
            //while sql has rows add each one otdata array
            $data[] = $d;
        }
    
    }else{
        //retunr the entire table if no department specfied or invalid argument
        $data = array();
        $sql = $conn->query("SELECT * FROM $table");
        while($d = $sql->fetch_assoc()){
            //add ever row $d to the data array
            $data[] = $d;
        }

    }

    //Return the JSON represenation of the data array; if one row is returned its an JSON object only
    //exit(json_encode($data));
    
    //For debugging print the data 
    //print_r($data);
    exit(json_encode($data, JSON_THROW_ON_ERROR, 512));

}

if ($_SERVER['REQUEST_METHOD']==='POST'){
    /**
     * Add a new row, ID is autmatically assigned so
     * does not need to be specifed when teh method is called
     * 
     * POSTMAN : POST http://localhost/crud_api/api/departments.php/contacts
     * ake sure you post an JSON obet iwth DB vales (see instructions)
     */

     //Columns = position name email extension phone department
     $data = json_decode(file_get_contents("php://input")); //get data pased to this script
     $sql = $conn->query("INSERT INTO $table (position, name, email, extension, phone, department) VALUES ('".$data->position."','".$data->name."','".$data->email."','".$data->extension."','".$data->phone."','".$data->department."')");

     if ($sql){
         //if sql query valid insert id and retrun sql object
         $data->id = $conn->insert_id;
         exit(json_encode($data));//return SQL insert as JSON oject fro POSTMAN troublshooting
     }else{
         //somthing went wrong, send the follwoing message as JSON 
         exit(json_encode(array('status' => 'error: ', 'SQL - ' => '$sql')));
     }
}

if ($_SERVER['REQUEST_METHOD']==='PUT'){
    /**
     * Update a row according to ID passed to script
     * POSTMAN PUT  http://localhost/crud_api/api/departments.php/contacts/?id=6
     */

     if (isset($_GET['id'])){
         $id = $conn->real_escape_string($_GET['id']);
         //id is passed during the call to this function
         $data = json_decode(file_get_contents("php://input"));
         $sql = $conn -> query("UPDATE $table SET position= '".$data->position."', name = '".$data->name."', email= '".$data->email."',extension= '".$data->extension."',phone= '".$data->phone."',department= '".$data->department."' WHERE id = '$id'");

         if ($sql){
             exit(json_encode(array('status'=>'success', 'SQL command : ', '$sql')));
         }else{
             exit(json_encode(array('status'=>'error', 'SQL command is -' => '$sql')));
         }

     }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE'){
    /**
     * Delete accoring to the id of teh row
     * passed form teh function call
     * 
     * POSTMAN test : http://localhost/crud_api/api/departments.php/contacts/?id=1
     * Where id is the row id to delete
     */
    if (isset($_GET['id'])){
        $id = $conn-> real_escape_string($_GET['id']);
        $sql = $conn -> query("DELETE FROM $table WHERE contacts.id = '$id'");

        if($sql){
            exit(json_encode((array('status' => 'success', 'SQL Statment: ', '$sql'))));
        }else{
            //something went wrong
            exit(json_encode((array('status'=>'error id not deleted..', 'SQL command: ', '$sql'))));
        }

    }

}

?>
