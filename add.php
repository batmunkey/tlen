<?php
session_start();
session_regenerate_id();

error_log("failed",3,"/var/log/httpd/ssl_error_log");

#Adds session authentication
include_once('/var/www/html/hw10/hw10-lib.php');
connect($db);

if (!isset($_SESSION['authenticated'])) {
	authenticate($db, $username, $password);
}

#Includes header
include_once('/var/www/html/hw10/header.php');

#adds recaptcha support
#captchaCheck();

#Checks authentication
checkAuth();

#This does the switchy stuff once you choose an option
switch($i) { 
	case "0";
	default:
 		echo "
		<center>
                <table> <tr> <td> <b> <u> Add character to books</b></u> </td></tr> \n
                <form method=post action=index.php>  
                        Character Name: <input type=\"text\" name=\"characterName\"><br>\n 
                        Race: <input type=\"text\"  name=\"characterRace\"><br>\n 
                        Good: <input type=\"radio\" name=\"characterSide\" value=\"Good\">
                        Evil: <input type=\"radio\" name=\"characterSide\" value=\"Evil\"><br>\n
                        <input type=\"hidden\" name=\"i\" value=\"1\">
                        <input type=\"submit\" value=\"submit\" name=\"submit\">
                        </form>
                        </table>
		        </form><br> <br> <a href=add.php?i=99> Logout </a> <br> 
					<a href=add.php?i=90> Add New Users </a> <br> 
					<a href=add.php?i=105> View ISBN's in the database </a> <br> 
					<a href=add.php?i=101> Failed logins </a> <br> 
					<a href=add.php?i=100> Show App Users </a> <br>
  				</center>
 				</body>
				</html>
                ";
                break;
	case "1":
                #This adds characters to characters table.
                $characterName=mysqli_real_escape_string($db, $characterName);
                $characterRace=mysqli_real_escape_string($db, $characterRace);
                $characterSide=mysqli_real_escape_string($db, $characterSide);
                if ($stmt = mysqli_prepare($db, "INSERT INTO characters set characterid='', name=?, race=?, side=?")) {
                        mysqli_stmt_bind_param($stmt, "sss", $characterName, $characterRace, $characterSide);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                }
                if ($stmt = mysqli_prepare($db, "SELECT characterid from characters where username=? and password=? and email=? order by characterid desc limit 1")) {
                        mysqli_stmt_bind_param($stmt, "sss", $characterName, $characterRace, $characterSide);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $cid);
                        while(mysqli_stmt_fetch($stmt)) {
                                $cid=$cid;
                        }
                        mysqli_stmt_close($stmt);
                } else {
                        echo "Error with query";
                }
                break;

         case "2": echo "put next form here";
                break;

         case "3": echo "put next sql stmt here";
                break;

         case "4": echo "put next form here";
                break;

         case "5": echo "put next sql stmt here";
                break;

	 case "90": #This is the USER add form 
		if ($_SESSION[‘authenticated’]=“yes”) {
		echo "
 		<tr> <td> <colspan=2> <b> <u> Enter credentials to create a new user </b></u> </td></tr> \n
        		</table>        
                	<form method=post action=add.php>
                	Username: <input type=\"text\" name=\"username\"><br>
                	Password: <input type=\"text\" name=\"password\"><br>
			Email: <input type=\"text\" name=\"email\"><br>\n
                	<input type=\"hidden\" name=\"i\" value=\"91\">
                	<input type=\"submit\" value=\"submit\" name=\"submit\">
                	</form>
                        </table>
                        </form><br> <br> <a href=add.php?i=99> Logout </a> <br> 
                                        <a href=add.php?i=90> Add New Users </a> <br> 
                                        <a href=add.php?i=105> View ISBN's in the database </a> <br> 
                                        <a href=add.php?i=101> Failed logins </a> <br> 
                                        <a href=add.php?i=100> Show App Users </a> <br>
                                </center>
                                </body>
                                </html>
		";
		} else {
			header("Location: /hw10/login.php");
		}			
		break;
	 case "91": #This is the insert command to add a user
		$username=mysqli_real_escape_string($db, $username);
                $password=mysqli_real_escape_string($db, $password);
                $epass=hash(‘sha256’, $password . $salt);
		$email=mysqli_real_escape_string($db, $email);
                if ($stmt = mysqli_prepare($db, "INSERT INTO users set userid='', username=?, password=?, email=?")) {
                        mysqli_stmt_bind_param($stmt, "sss", $username, $epass, $email);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                } else {
                        echo "Error with query";
                }
		break;

	case "99": #This is the logout
		session_destroy();
		header("Location: /hw10/login.php");	
		break;

	case "100": #Displays all app users
	if ($_SESSION['userid']="1") {
                echo "<table> <tr> <td> <b> <u> Users list </b></u> </td></tr> \n ";
                $sid=mysqli_real_escape_string($db, $sid);
                if ($stmt = mysqli_prepare($db, "SELECT username, email FROM users")) {
                        mysqli_stmt_bind_param($stmt, $username, $email);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $username, $email);
                        while(mysqli_stmt_fetch($stmt)) { 
                                $username=htmlspecialchars($username);
				$email=htmlspecialchars($email);
                                echo "<tr> <td> <a href=index.php?>$username $email</a></td></tr>";
                        }{
                          	mysqli_stmt_close($stmt);
                        }
                        echo "</table>";
                }
                break;
	} else {
        	header("Location: /hw10/login.php");
        }                       
        break;

	case "101": #Displays all failed logins to admin
        if ($_SESSION['userid']="1") {
                echo "<table> <tr> <td> <b> <u> Admin failed logins </b></u> </td></tr> \n ";
                if ($stmt = mysqli_prepare($db, "SELECT user, action, date FROM login")) {
                        mysqli_stmt_bind_param($stmt, $username, $action, $date);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $username, $action, $date);
                        while(mysqli_stmt_fetch($stmt)) { 
                                $username=htmlspecialchars($username);
                                $action=htmlspecialchars($action);
				$date=htmlspecialchars($date);
                                echo "<tr> <td> <a href=index.php?>$user $action $date</a></td></tr>";
                        }{
                          	mysqli_stmt_close($stmt);
                        }
                        echo "</table>";
			}
        	} else {
                	header("Location: /hw10/login.php");
        	}                       
		break;

	case "105": #Displays ISBN's to admin
        if ($_SESSION['userid']="1") {
                echo "<table> <tr> <td> <b> <u> LOTR ISBN's </b></u> </td></tr> \n ";
                if ($stmt = mysqli_prepare($db, "SELECT isbn.isbnid, isbn.isbn, users.salt FROM isbn, users WHERE users.userid=1")) {
                        mysqli_stmt_bind_param($stmt, $isbnid, $isbn, $salt);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $isbnid, $isbn, $salt);
                        while(mysqli_stmt_fetch($stmt)) { 
                                $isbnid=htmlspecialchars($isbnid);
                                $isbn=htmlspecialchars($isbn);
				$salt=htmlspecialchars($salt);
                                
				#encrypts and decrypts the isbn using mcrypt
				$mcryptEncrypt=mcrypt_encrypt(
    					MCRYPT_RIJNDAEL_256,
    					$salt, $isbn,
    					MCRYPT_MODE_CBC, $iv);

				$mcryptDecrypt=mcrypt_decrypt(
    					MCRYPT_RIJNDAEL_256,
    					$salt, $mcryptEncrypt,
    					MCRYPT_MODE_CBC, $iv);
				
                                echo "<tr> <td> <a href=index.php?>$isbnid  $mcryptEncrypt  $mcryptDecrypt </a></td></tr>";
                        }{
                          	mysqli_stmt_close($stmt);
                        }
                        echo "</table>	
					<br> <br> <a href=add.php?i=99> Logout </a> <br> 
                                        <a href=add.php?i=90> Add New Users </a> <br>
					<a href=add.php?i=105> View ISBN's in the database </a> <br>  
                                        <a href=add.php?i=106> Add ISBN's to database </a> <br> 
                                        <a href=add.php?i=101> Failed logins </a> <br> 
                                        <a href=add.php?i=100> Show App Users </a> <br>
                                </center>
                                </body>
                                </html>
			";
			}
        	} else {
                header("Location: /hw10/login.php");
		}
		break;

	case "106": #This is the ISBN add form 
                if ($_SESSION['userid']="1") {
                echo "
                <tr><td> <b> <u> Enter the information for the new ISBN </b></u> </td></tr> \n
                        </table>        
                        <form method=post action=add.php>
                        <select name=\"isbnid\">
			        <option value=\"\"> Choose a book, then enter the isbn... </option> 
				<option value=\"1\"> The Hobbit </option> 
        			<option value=\"2\"> LOTR: Fellowship of the Ring </option> 
        			<option value=\"3\"> LOTR: The Two Towers </option> 
				<option value=\"4\"> LOTR: The Return of the King </option> \n
				
				Enter the ISBN: <input type=\"text\" name=\"isbn\"><br>\n

                        <input type=\"hidden\" name=\"i\" value=\"107\">
                        <input type=\"submit\" value=\"submit\" name=\"submit\">
                        </form>
                        </table>
                        </form><br> <br> <a href=add.php?i=99> Logout </a> <br> 
                                        <a href=add.php?i=90> Add New Users </a> <br> 
                                        <a href=add.php?i=105> View ISBN's in the database </a> <br>
					<a href=add.php?i=106> Add ISBN's to database </a> <br>  
                                        <a href=add.php?i=101> Failed logins </a> <br> 
                                        <a href=add.php?i=100> Show App Users </a> <br>
                                </center>
                                </body>
                                </html>
                ";
                } else {
                        header("Location: /hw10/login.php");
                }                       
                break;
		
		case "107":#This is the insert command to add an isbn 
        	        $isbnid=mysqli_real_escape_string($db, $isbnid);
                	$isbn=mysqli_real_escape_string($db, $isbn);
                	if ($stmt = mysqli_prepare($db, "UPDATE isbn SET isbn=? WHERE isbnid=?")) {
                        	mysqli_stmt_bind_param($stmt, "ss", $isbn, $isbnid);
                        	mysqli_stmt_execute($stmt);
                        	mysqli_stmt_close($stmt);
                	} else {
                        	echo "Error with query";
                	}
                	break;

        break;

}
?>
