<?php
require_once 'headers.php';

//echo "In app.php file.";

//Create connection- sob database xmaple
//$conn = new mysqli('localhost','root','','sob');

//creat cpnnection for usernameaul database on XAMP localhost
$conn = new mysqli('localhost','root','','Usermanual');
$table = "Tests";

//Set up connection details on mooneycalla.com
//$conn = new mysqli('http://mooneycallans.com','m558405_test','usermanual','m558405_usermanual');

//Check connection 
if ($conn -> connect_error){
    die("Connection failed: " .$conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    
   // $id = $conn->real_escape_string($_GET['id']);
   //print_r($_GET);
    if (isset($_GET['id'])){
        //if id not null, id used to fetch single row
        $id = $conn->real_escape_string($_GET['id']);
        $sql = $conn->query("SELECT * FROM $table WHERE id = '$id'");
        $data = $sql->fetch_assoc();
    }else{
        //else fetch all rows
        $data = array();
        $sql = $conn->query("SELECT * FROM $table");
        while($d = $sql->fetch_assoc()){
            $data[] = $d;

        }
    }

    // Retunr json data
    exit(json_encode($data));

}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    /**
     * Add a new row and automtaictly assign ID primary incremnatl key 
     * (MAKE SURE ID IS SET TO PRIMARY AND AUTOINCREMENT)
    */
     //echo 'post';
    $data = json_decode(file_get_contents("php://input"));
    //print_r("POST: data recuved = " + $data);
    //$sql = $conn->query("INSERT INTO students (name, address, phone) VALUES ('".$data->name."','".$data->address."','".$data->phone."')");
    $sql = $conn->query("INSERT INTO $table (investigation, SpecimanType, container, TAT, refrange, Lab, Comments, Alias, phone, profileTests, phoneCriteria, referred, refWebsite, refNumber, username) VALUES ('".$data->investigation."','".$data->SpecimanType."','".$data->container."','".$data->TAT."','".$data->refrange."','".$data->Lab."','".$data->Comments."','".$data->Alias."','".$data->phone."','".$data->profileTests."','".$data->phoneCriteria."','".$data->referred."','".$data->refWebsite."', '".$data->refNumber."', '".$data->username."')");

    //print_r("SWL COmmand = " + $sql);

    if ($sql){
        $data->id = $conn->insert_id; // appedn id to data object
        exit(json_encode($data));
    }else{
        exit(json_encode(array('status' => 'error: ', 'SQL - ' => '$sql')));

    }

}

if ($_SERVER['REQUEST_METHOD'] === 'PUT'){
    //Upadte a row with id tag
    //echo 'put';
    if(isset($_GET['id'])){
        $id = $conn-> real_escape_string($_GET['id']);
        //Take JSON string sent fomr app and convert tp php object / var, phpinput=raw data form teh reqeust
        $data = json_decode(file_get_contents("php://input"));

        // (investigation, SpecimanType, container, TAT, refrange, Lab, Comments, Alias, phone, 
        // profileTests, phoneCriteria, referred, refWebsite, refNumber) VALUES 
        // ('".$data->investigation."','".$data->SpecimanType."','".$data->container."',
        // '".$data->TAT."','".$data->refrange."','".$data->Lab."',
        // '".$data->Comments."','".$data->Alias."','".$data->phone."',
        // '".$data->profileTests."','".$data->phoneCriteria."',
        // '".$data->referred."','".$data->refWebsite."', '".$data->refNumber."')");
        $sql = $conn -> query("UPDATE $table SET investigation= '".$data->investigation."', SpecimanType = '".$data->SpecimanType."',
        container = '".$data->container."', TAT = '".$data->TAT."', refrange = '".$data->refrange."',
        Lab = '".$data->Lab."', Comments = '".$data->Comments."', Alias = '".$data->Alias."', phone = '".$data->phone."',
        profileTests= '".$data->profileTests."', referred = '".$data->referred."', refWebsite = '".$data->refWebsite."',
        refNumber = '".$data->refNumber."',username = '".$data->username."' WHERE id = '$id'");

        //$sql = $conn-> query("UPDATE students SET name = '".$data->name."', address = '".$data->address."', phone = '" .$data->phone."' WHERE id = '$id'");
        
        if ($sql){
            //echo 'Success!';
            exit(json_encode(array('status' => 'success')));
            
        }else{
            //echo 'Failed';
            exit(json_encode(array('status' => 'error')));
    
        }
    }
    //else{
    //     echo 'var GET ig not set';
    // }

}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE'){
    //echo 'delete';
    if (isset($_GET['id'])){
        
        $id = $conn-> real_escape_string($_GET['id']);
        //$sql = $conn->query("DELETE FROM students WHERE students.id = '$id'");
        $sql = $conn->query("DELETE FROM $table WHERE tests.id = '$id'");
       

        if($sql){
            exit(json_encode(array('status' => 'success')));
            

        }else{
            exit(json_encode(array('status' => 'error')));
        }
    }else{
        //echo 'var GET id not set';
        exit(json_encode(array('status' => 'error id not set')));
    }



    
}