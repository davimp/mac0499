<?php   

/* This file is part of HIHAT v1.1
   ================================
   Copyright (c) 2007 HIHAT-Project      

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.
  
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 
*/

    // name of module, adjust to selected application
    $thisModule_23cdx_ = "php";    
    
    // login-information for logserver
    $link23 = mysqli_connect("localhost", "username", "password","honeyweb");
    
    
    // *** DO NOT MODIFY ANYTHING BELOW THIS LINE ***  
    if(!$link23) {
        die("No connection to database server!");
    }
    if(!mysqli_select_db($link23, "honeyweb")) {
        die("Unable to use database, error: ".mysqli_error($link23));
    }   
        
    $Array2String = create_function( '$Array','
      $SeparationValue23 = ";semcl";
      $retString = "";
      if (!is_array($Array))
        $retString = $Array;
      else
        foreach ($Array as $Key => $Value) {
          if(is_array($Value)) {          
            //$retString .="MULTI"; 
            foreach ($Value as $Key2 => $Value2) 
               if(is_array($Value2)) {
                  //$retString .="MULTI2 ";
                  foreach ($Value2 as $Key3 => $Value3) 
                    if(is_array($Value3)) {
                         //$retString .="MULTI3 ";
                         foreach ($Value3 as $Key4 => $Value4) 
                              if(is_array($Value4))
                                   $retString .= "NO5DimArraySUPPORT";
                              else
                                  $retString .= $Key4. "=".$Value4. $SeparationValue23;   
                    }                        
                    else
                        $retString .= $Key3. "=".$Value3. $SeparationValue23;                
              }
               else
                  $retString .= $Key2. "=".$Value2. $SeparationValue23;                        
          }
          else
            $retString .= $Key."=".$Value. $SeparationValue23;    
        }
      return $retString;
    ');         
         
    // Select arrays to add to database
    $dbArrays = array( $_SERVER, $_GET, $_POST, $_COOKIE );
    $arrayContent_23cdx_[ 0 ] = "--"; // init array with nonsense
    
    // read RAW post data         
    $rawData_23cdx_ = explode( "&", file_get_contents("php://input"));         
    foreach ( $rawData_23cdx_ AS $key_23cdx_ => $value_23cdx_ )  {      // put into form: key=value
        $tempAr_23cdx_ = explode( "=", $rawData_23cdx_[$key_23cdx_], 2 );
        $rawData2_23cdx_[$tempAr_23cdx_[0]] = $tempAr_23cdx_[1];     // if identical keys exist: only last entry counts
    }    
    foreach( $rawData2_23cdx_ AS $key2_23cdx_ => $value2_23cdx_)    // add to Post array if new stuff is found
       if (!isset($_POST[$key2_23cdx_])) {                        
            $_POST[ $key2_23cdx_ ] = $value2_23cdx_;                
       }
   
    // for each of these arrays:  read out last entry to avoid duplicates + add data if no duplicate
    foreach ( $dbArrays AS $dbKeyArray => $dbValueArray ) {       
       
        $arrayContent_23cdx_[ $dbKeyArray ] = $Array2String( $dbValueArray );    
   }      
   
   $ip_23cdx_      =  isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : "";       
   $browser_23cdx_ =  isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : "";
   $source_23cdx_  =  isset( $_SERVER['PHP_SELF'] ) ? $_SERVER['PHP_SELF'] : "";  
   
   $duplicateCounter_23cdx_ = 0;    
     
    // checks if same script has made same data-entry        
    $sql_23cdx_ = "SELECT Source, Value_Server, Value_Get, Value_Post, Value_Cookie, Module FROM main_logs
                   WHERE        
                         Value_Server = '".addslashes( htmlentities ( $arrayContent_23cdx_[ 0 ] , ENT_QUOTES )).
                  "' AND Value_Get =    '".addslashes( htmlentities ( $arrayContent_23cdx_[ 1 ] , ENT_QUOTES )). 
                  "' AND Value_Post =   '".addslashes( htmlentities ( $arrayContent_23cdx_[ 2 ] , ENT_QUOTES )).
                  "' AND Value_Cookie = '".addslashes( htmlentities ( $arrayContent_23cdx_[ 3 ] , ENT_QUOTES )).
                  "' AND Module       = '".addslashes( htmlentities ( $thisModule_23cdx_ , ENT_QUOTES )).
                  "' AND Source =       '".addslashes( htmlentities ( $source_23cdx_ , ENT_QUOTES)). "'";    
                  
    $result_23cdx_ = mysqli_query($link23, $sql_23cdx_) OR die(mysqli_error($link23));
    while($row23 = mysqli_fetch_assoc($result_23cdx_)) {
        $duplicateCounter_23cdx_++;                 
    }
      
     // add data of array to database
     $sql_23cdx_ = "INSERT INTO main_logs ( attackerIP, attackerBrowser, Value_Server, 
                                         Value_Get, Value_Post,Value_Cookie, Source, Module ) VALUES
              ('".addslashes( htmlentities ( $ip_23cdx_, ENT_QUOTES))."',     
              '".addslashes( htmlentities ( $browser_23cdx_, ENT_QUOTES))."',
              '".addslashes( htmlentities ( $arrayContent_23cdx_[ 0 ], ENT_QUOTES))."',
              '".addslashes( htmlentities ( $arrayContent_23cdx_[ 1 ], ENT_QUOTES))."',
              '".addslashes( htmlentities ( $arrayContent_23cdx_[ 2 ], ENT_QUOTES))."',
              '".addslashes( htmlentities ( $arrayContent_23cdx_[ 3 ], ENT_QUOTES))."',
              '".addslashes( htmlentities ( $source_23cdx_, ENT_QUOTES))."',                 
              '".addslashes( htmlentities ( $thisModule_23cdx_ , ENT_QUOTES )). "')";
               
    if ( $duplicateCounter_23cdx_ == 0 & $arrayContent_23cdx_[ 0 ] != '' )     // don't add duplicates or empty fields
        mysqli_query($link23, $sql_23cdx_) OR die(mysqli_error($link23));             
    
    @mysqli_free_result($result_23cdx_);
    @mysqli_close($link23);  
    $value = "";
    $key = "";
    $max_23cdx_ = "";
    $sql_23cdx_ = "";
    
    $dbArrays = "";    
    $dbNameOfTargettable = "";
?>

<!DOCTYPE HTML>  
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>  

<?php
// define variables and set to empty values
$nameErr = $emailErr = $genderErr = $websiteErr = "";
$name = $email = $gender = $comment = $website = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["name"])) {
    $nameErr = "Name is required";
  } else {
    $name = test_input($_POST["name"]);
    // check if name only contains letters and whitespace
    if (!preg_match("/^[a-zA-Z-' ]*$/",$name)) {
      $nameErr = "Only letters and white space allowed";
    }
  }
  
  if (empty($_POST["email"])) {
    $emailErr = "Email is required";
  } else {
    $email = test_input($_POST["email"]);
    // check if e-mail address is well-formed
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = "Invalid email format";
    }
  }
    
  if (empty($_POST["website"])) {
    $website = "";
  } else {
    $website = test_input($_POST["website"]);
    // check if URL address syntax is valid (this regular expression also allows dashes in the URL)
    if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$website)) {
      $websiteErr = "Invalid URL";
    }
  }

  if (empty($_POST["comment"])) {
    $comment = "";
  } else {
    $comment = test_input($_POST["comment"]);
  }

  if (empty($_POST["gender"])) {
    $genderErr = "Gender is required";
  } else {
    $gender = test_input($_POST["gender"]);
  }
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>

<h2>PHP Form Validation Example</h2>
<p><span class="error">* required field</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
  Name: <input type="text" name="name" value="<?php echo $name;?>">
  <span class="error">* <?php echo $nameErr;?></span>
  <br><br>
  E-mail: <input type="text" name="email" value="<?php echo $email;?>">
  <span class="error">* <?php echo $emailErr;?></span>
  <br><br>
  Website: <input type="text" name="website" value="<?php echo $website;?>">
  <span class="error"><?php echo $websiteErr;?></span>
  <br><br>
  Comment: <textarea name="comment" rows="5" cols="40"><?php echo $comment;?></textarea>
  <br><br>
  Gender:
  <input type="radio" name="gender" <?php if (isset($gender) && $gender=="female") echo "checked";?> value="female">Female
  <input type="radio" name="gender" <?php if (isset($gender) && $gender=="male") echo "checked";?> value="male">Male
  <input type="radio" name="gender" <?php if (isset($gender) && $gender=="other") echo "checked";?> value="other">Other  
  <span class="error">* <?php echo $genderErr;?></span>
  <br><br>
  <input type="submit" name="submit" value="Submit">  
</form>

<?php
echo "<h2>Your Input:</h2>";
echo $name;
echo "<br>";
echo $email;
echo "<br>";
echo $website;
echo "<br>";
echo $comment;
echo "<br>";
echo $gender;
?>

</body>
</html>
