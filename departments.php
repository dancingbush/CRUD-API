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


?>