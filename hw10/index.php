<?php 
# Homework 10
# Sam Powell sam.powell@lasp.colorado.edu
# /var/www/html/hw10/hw10.php and other libs in that dir
# Purpose: to create a dynamic web app linked to mysql test1

#Pointer for the lib and authentication files
include_once('/var/www/html/hw10/hw10-lib.php');

#Tests for number or incorrect value
numcheck($i);
numcheck($cid);
numcheck($bid);
numcheck($pid);
numcheck($sid);

#Function to connect to database
connect($db);

#This adds the reusable header
include_once('/var/www/html/hw10/header.php');

echo "
<html>

<head> <title> TLEN5839 HW10:Sam Powell </title> </head>

<body> ";


#This does the switchy stuff once you choose an option
switch($i) { 
	case "0"; 
	default: 	 
                #This by default queries the DB for story ID 
		echo "<table> <tr> <td> <b> <u> Stories </b></u> </td></tr> \n ";
		$query="SELECT storyid, story from stories"; 
		$result=mysqli_query($db, $query); 
			while($row=mysqli_fetch_row($result)) {
        		echo "<br>\n <tr> <td> $row[0] </td><td> <a href=index.php?i=1&sid=$row[0]>$row[1] </a></td></tr> \n";
		}
                break;
        case "1": 
		#This queries the database for all books under one of the two stories
		echo "<table> <tr> <td> <b> <u> Books in this story </b></u> </td></tr> \n ";
		$sid=mysqli_real_escape_string($db, $sid);
		if ($stmt = mysqli_prepare($db, "SELECT bookid, title FROM books WHERE storyid=?")) {
                        mysqli_stmt_bind_param($stmt,"i", $sid);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $bid, $title);
	                while(mysqli_stmt_fetch($stmt)) { 
				$bid=htmlspecialchars($bid);
                        	$title=htmlspecialchars($title);
				echo "<tr> <td> <a href=index.php?bid=$bid&i=2>$title</a></td></tr>";
			}{
				mysqli_stmt_close($stmt);
	                }
			echo "</table>";
		}
		break;
        case "2":  
                #This queries the DB for characters by story. 
                echo "<table> <tr> <td> <b> <u> Characters in this book </b></u> </td></tr> \n ";
                $bid=mysqli_real_escape_string($db, $bid);
                if ($stmt = mysqli_prepare($db, "SELECT c.characterid, c.name FROM characters c, appears a WHERE bookid=? and c.characterid=a.characterid")) {
                        mysqli_stmt_bind_param($stmt,"i", $bid);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $cid, $title);
                        while(mysqli_stmt_fetch($stmt)) { 
                                $cid=htmlspecialchars($cid);
                                $title=htmlspecialchars($title);
                                echo "<tr> <td> <a href=index.php?cid=$cid&i=3>$title</a></td></tr>";
                        }{
                          	mysqli_stmt_close($stmt);
                        }
                        echo "</table>";
                }
                break;
        case "3": 
                #This queries the DB for a characters appearances.
		echo "<table> <tr> <td> <b> <u> Character appearances </b></u> </td></tr> \n ";
		echo "<table> <tr> <td> <b> <u> Character ==> Book ==> Story </b></u> </td></tr> \n ";
                $cid=mysqli_real_escape_string($db, $cid);
                if ($stmt = mysqli_prepare($db, "select c.name, b.title, s.story from characters c, books b, stories s, appears a WHERE b.storyid=s.storyid and b.bookid=a.bookid and c.characterid=a.characterid and c.characterid=?")) {
                        mysqli_stmt_bind_param($stmt,"i", $cid);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $character, $book, $story);
                        while(mysqli_stmt_fetch($stmt)) {
				$cid=htmlspecialchars($character);
				$bid=htmlspecialchars($book);
				$sid=htmlspecialchars($story);
                        	echo "<tr> <td> <a href=index.php?cid=$cid&i=4> $character $book $story</a></td></tr>";
                	}{
                  		mysqli_stmt_close($stmt);
                	}
			echo "</table>";
		}
		break;
        case "4":
		#This shows chars their pics
		echo "<table> <tr> <td> <b> <u> Characters and pictures </b></u> </td></tr> \n ";
                $pid=mysqli_real_escape_string($db, $pid);
                if ($stmt = mysqli_prepare($db, "SELECT c.name, p.url FROM characters c, pictures p WHERE c.characterid=p.characterid")) {
                        mysqli_stmt_bind_param($stmt, $pid, $cid);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_bind_result($stmt, $character, $picture);
                        while(mysqli_stmt_fetch($stmt)) { 
                                $pid=htmlspecialchars($picture);
                                $cid=htmlspecialchars($character);
                                echo "<tr> <td> <a href=index.php?i=5> $character <img src=$picture> </a></td></tr>";
                        }{
                          	mysqli_stmt_close($stmt);
                        }
                        echo "</table>";
                }
                break;

}

echo "</body> </html>";

?>
