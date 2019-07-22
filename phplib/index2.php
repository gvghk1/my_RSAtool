<!DOCTYPE HTML>  
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>  

<?PHP
 
 /* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
$mysqli = new mysqli("localhost", "root", "", "demo");
 
// Check connection
if($mysqli === false){
    die("ERROR: Could not connect. " . $mysqli->connect_error);
}
 
// Attempt create table query execution
$sql = "create TABLE message(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    message ntext (100) NOT NULL
)";
if($mysqli->query($sql) === true){
    echo "Table created successfully.";
} else{
    echo "Encrypted message directory created" ;
}
 
// Close connection
$mysqli->close();

include('Crypt/RSA.php');
$ciphertext = "";
$plaintext = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	  if (empty($_POST["plaintext"])) {
    $plaintext = "";
  } else {
    $plaintext = test_input($_POST["plaintext"]);
  }
  
  if (empty($_POST["ciphertext"])) {
    $ciphertext = "";
  } else {
    $ciphertext = test_input($_POST["ciphertext"]);
  }

}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

class myRSA
{
    public static $privateKey = '';
    public static $publicKey = '';
    public static $keyPhrase = '';
     
    public static function createKeyPair()
    {
        $rsa = new Crypt_RSA();
        $password = base64_encode(sha1(time().rand(100000,999999)));
        $rsa->setPassword($password );
        $keys=$rsa->createKey(2048);     
        myRSA::$privateKey=$keys['privatekey'];
        myRSA::$publicKey=$keys['publickey'];
        myRSA::$keyPhrase=$password;
    }
 
    public static function encryptText($text)
    {
        $rsa = new Crypt_RSA();
        $rsa->loadKey(myRSA::$publicKey);
        $encryptedText = $rsa->encrypt($text);
        return $encryptedText;
    }
 
    public static function decryptText($encryText)
    {
        $rsa = new Crypt_RSA();
        $rsa->setPassword(myRSA::$keyPhrase);
        $rsa->loadKey(myRSA::$privateKey);
        $plaintext = $rsa->decrypt($encryText);
        return $plaintext;
    }
}
?>
 <h2>RSA tool</h2>
<p><span class="error">* required field</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  


   Enter text to be encryted: <textarea name="plaintext" rows="5" cols="40"><?php echo $plaintext;?></textarea>
  <br><br>

   Enter text to be decryted: <textarea name="ciphertext" rows="5" cols="40"><?php echo $ciphertext;?></textarea>
  <br><br>


  <input type="submit" name="submit" value="Submit">  
</form>
<?php

//create keys
myRSA::createKeyPair(1024);

if ($plaintext != "")
{
//Text to encrypt
$text = $plaintext;
echo 'Original Text : '.$text;
echo "<br>";
 
$secureText = myRSA::encryptText($text);
echo 'Encrypted : '.$secureText;
echo "<br>";
echo "<br>";

/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
$mysqli = new mysqli("localhost", "root", "", "demo");
 
// Check connection
if($mysqli === false){
    die("ERROR: Could not connect. " . $mysqli->connect_error);
}
 
// Attempt insert query execution
$sql = "INSERT INTO message (id, message) VALUES ('1', $secureText)";
if($mysqli->query($sql) === true){
    echo "Records inserted successfully.";
} else{
    echo "ERROR: Could not able to execute $sql. " . $mysqli->error;
}
 
// Close connection
$mysqli->close();
}
if ($ciphertext != "")
{
echo 'Your Encrypted Text : '.$ciphertext;
$decrypted_text =  myRSA::decryptText($ciphertext);
echo 'Decrypted Text : '.$decrypted_text;
}



?>

</body>