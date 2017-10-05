<?php

#variable instantiation
isset ( $_REQUEST['i'] ) ? $i = strip_tags($_REQUEST['i']) : $i = ""; 
isset ( $_REQUEST['image'] ) ? $image = strip_tags($_REQUEST['image']) : $image = ""; 
isset ( $_REQUEST['userid'] ) ? $userid = strip_tags($_REQUEST['userid']) : $userid = "";
isset ( $_REQUEST['username'] ) ? $username = strip_tags($_REQUEST['username']) : $username = "";
isset ( $_REQUEST['password'] ) ? $password = strip_tags($_REQUEST['password']) : $password = "";
isset ( $_REQUEST['email'] ) ? $email = strip_tags($_REQUEST['email']) : $email= "";
isset ( $_REQUEST['salt'] ) ? $salt = strip_tags($_REQUEST['salt']) : $salt= "";
isset ( $_REQUEST['xUser'] ) ? $xUser = strip_tags($_REQUEST['xUser']) : $xUser= "";
isset ( $_REQUEST['xPassword'] ) ? $xPassword = strip_tags($_REQUEST['xPassword']) : $xPassword = "";
isset ( $_REQUEST['var'] ) ? $var = strip_tags($_REQUEST['var']) : $var= "";
isset ( $_REQUEST['epass'] ) ? $epass = strip_tags($_REQUEST['epass']) : $epass= "";
isset ( $_REQUEST['i'] ) ? $i = strip_tags($_REQUEST['i']) : $i = ""; 
isset ( $_REQUEST['cid'] ) ? $cid = strip_tags($_REQUEST['cid']) : $cid = ""; 
isset ( $_REQUEST['bid'] ) ? $bid = strip_tags($_REQUEST['bid']) : $bid = "";
isset ( $_REQUEST['pid'] ) ? $pid = strip_tags($_REQUEST['pid']) : $pid = "";
isset ( $_REQUEST['sid'] ) ? $sid = strip_tags($_REQUEST['sid']) : $sid = "";
isset ( $_REQUEST['title'] ) ? $title = strip_tags($_REQUEST['title']) : $title = "";
isset ( $_REQUEST['character'] ) ? $character = strip_tags($_REQUEST['character']) : $character= "";
isset ( $_REQUEST['book'] ) ? $book = strip_tags($_REQUEST['book']) : $book = "";
isset ( $_REQUEST['story'] ) ? $story = strip_tags($_REQUEST['story']) : $story = "";
isset ( $_REQUEST['picture'] ) ? $picture = strip_tags($_REQUEST['picture']) : $picture = "";
isset ( $_REQUEST['characterName'] ) ? $characterName = strip_tags($_REQUEST['characterName']) : $characterName = "";
isset ( $_REQUEST['characterRace'] ) ? $characterRace = strip_tags($_REQUEST['characterRace']) : $characterRace = "";
isset ( $_REQUEST['characterSide'] ) ? $characterSide = strip_tags($_REQUEST['characterSide']) : $characterSide = "";
isset ( $_REQUEST['action'] ) ? $action = strip_tags($_REQUEST['action']) : $action = "";
isset ( $_REQUEST['ip'] ) ? $ip = strip_tags($_REQUEST['ip']) : $ip = "";
isset ( $_REQUEST['date'] ) ? $date = strip_tags($_REQUEST['date']) : $date = "";
isset ( $_REQUEST['loginid'] ) ? $loginid = strip_tags($_REQUEST['loginid']) : $loginid = "";
isset ( $_REQUEST['username'] ) ? $username = strip_tags($_REQUEST['username']) : $username = "";
isset ( $_REQUEST['iv'] ) ? $iv = strip_tags($_REQUEST['iv']) : $iv = "";
isset ( $_REQUEST['mcryptEncrypt'] ) ? $mcryptEncrypt = strip_tags($_REQUEST['mcryptEncrypt']) : $mcryptEncrypt = "";
isset ( $_REQUEST['mcryptDecrypt'] ) ? $mcryptDecrypt = strip_tags($_REQUEST['mcryptDecrypt']) : $mcryptDecrypt = "";
isset ( $_REQUEST['isbnid'] ) ? $isbnid = strip_tags($_REQUEST['isbnid']) : $isbnid = "";
isset ( $_REQUEST['isbn'] ) ? $isbn = strip_tags($_REQUEST['isbn']) : $isbn = "";
isset ( $_REQUEST['g-recaptcha-response'] ) ? $reCaptcha = strip_tags($_REQUEST['g-recaptcha-response']) : $reCaptcha = "";
$reCaptchaKey='6LfTHgkTAAAAAC2BXEOw6JPZgY60IX-8bs_qThWl';
isset ( $_REQUEST['url'] ) ? $url = strip_tags($_REQUEST['url']) : $url = "";
isset ( $_REQUEST['status'] ) ? $status = strip_tags($_REQUEST['status']) : $status = "";

#funtion to connect to database
function connect(&$db){
	$mycnf="/etc/hw5-mysql.conf";
	if (!file_exists($mycnf)) { 
		echo "Error file not found: $mycnf"; 
		exit;
	}

	$mysql_ini_array=parse_ini_file($mycnf); 
	$db_host=$mysql_ini_array["host"]; 
	$db_user=$mysql_ini_array["user"];
	$db_pass=$mysql_ini_array["pass"]; 
	$db_port=$mysql_ini_array["port"];
	$db_name=$mysql_ini_array["dbName"];

	$db = mysqli_connect(
		$db_host, 
		$db_user, 
		$db_pass, 
		$db_name, 
		$db_port
	);

	if (!$db) { 
		print "Error connecting to DB: " . mysqli_connect_error(); 
		exit;

	}

}

#Checks for numeric values
function numcheck($i) {
     if ($i != null) {
          if(!is_numeric($i)) {
		} 
	}
}

#This runs the select from database
function authenticate($db, $xUser, $xPassword){
		#Blocks for more than 5 logins in an hour
		#if ($stmt = mysqli_prepare($db, "SELECT user AND action='failed' AND loginid > 5 WHERE date > (DATE_SUB(NOW(), INTERVAL 1 HOUR)")) {
		#	mysqli_stmt_bind_param($stmt);
		#	mysqli_stmt_execute($stmt);
		#	mysqli_stmt_bind_result($stmt);
		#	echo "Too many login attempts";
                #        header("Location: /hw10/login.php");
                #        exit;
		#	}

                $xUser=mysqli_real_escape_string($db, $xUser);
                if ($stmt = mysqli_prepare($db, "SELECT password, salt, userid from users WHERE username=?")){
                        mysqli_stmt_bind_param($stmt, "s" , $xUser);
                        mysqli_stmt_execute($stmt);
			mysqli_stmt_bind_result($stmt, $password, $salt, $userid);
                        while(mysqli_stmt_fetch($stmt)) {
                                $userid=$userid;
				$password=$password;
				$salt=$salt;
			}
			mysqli_stmt_close($stmt);
			$epass=hash('sha256', $xPassword . $salt);
			if ($epass == $password) {
				$_SESSION['userid']=$userid;
                                #$_SESSION['email']=$email;
                                $_SESSION['authenticated']="yes";
                                $_SESSION['ip']=$_SERVER['REMOTE_ADDR'];
				$_SESSION['HTTP_USER_AGENT']=md5($_SERVER['HTTP_USER_AGENT']);
				$_SESSION['created']=time();

                        	$ip=mysqli_real_escape_string($db, $_SERVER['REMOTE_ADDR']);
                        	if ($stmt = mysqli_prepare($db, "INSERT INTO login set loginid='', action='accepted', ip=?, user=?, date=now()")) {
                                	mysqli_stmt_bind_param($stmt, "ss", $ip, $xUser);
                                	mysqli_stmt_execute($stmt);
                                	mysqli_stmt_close($stmt);
				}
	
                        } else {
                        $ip=mysqli_real_escape_string($db, $_SERVER['REMOTE_ADDR']);
                        if ($stmt = mysqli_prepare($db, "INSERT INTO login set loginid='', action='failed', ip=?, user=?, date=now()")) {
                                mysqli_stmt_bind_param($stmt, "ss",$ip, $xUser);
                                mysqli_stmt_execute($stmt);
                                mysqli_stmt_close($stmt);
				echo "Failed to Login";
                        	header("Location: /hw10/login.php");
                        	exit;
				}
			}

		} else {
			echo "Failed to Login";
                        header("Location: /hw10/login.php");
                        exit;
                }
}

function checkAuth(){
	if (isset($_SESSION['HTTP_USER_AGENT'])) {
		if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'])) {
			session_destroy();
		} 
	} else { 
		session_destroy();
	}

	if (isset($_SESSION['ip'])) {
                if ($_SESSION['ip'] != $_SERVER['REMOTE_ADDR']) {
                        session_destroy();
                } 
        } else {
                session_destroy();
        }

	if (isset($_SESSION['created'])) {
                if (time() - $_SESSION['created'] > 1800) {
                        session_destroy();
                } 
        } else { 
                session_destroy();
        }

	if ("POST" == $_SERVER["REQUEST_METHOD"]) {
		if (isset($_SERVER['HTTP_ORIGIN'])) {
                	if ($_SESSION['HTTP_ORIGIN'] != "https://172.20.74.23") {
                        	session_destroy();
                	} 
        } else { 
                
        }
}

#Function to initialize the vector for mcrypt
$iv=mcrypt_create_iv(
    mcrypt_get_iv_size(
         MCRYPT_RIJNDAEL_256,
         MCRYPT_MODE_ECB)
    ,MCRYPT_RAND);

#function to enable recaptcha
function captchaCheck($reCaptcha, $reCaptchaKey) {
	$url="https://www.google.com/recaptcha/api/siteverify?secret=" . $reCaptchaKey;
	$url=$url . "&response=" . $reCaptcha;
	$url=$url . "&remoteip=" . $_SERVER['REMOTE_ADDR'];
	$status=json_decode(file_get_contents($url));
	if ($status->success == false) {
		logout();
		exit;
	}
}

}
?>
