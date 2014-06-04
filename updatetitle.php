<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta name="HandheldFriendly" content="true" />
  <meta name="MobileOptimized" content="320" />
  <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scaleable=no, width=device-width" />

  <title>Update Title</title>
  <link rel="stylesheet" href="storytapes.css" />
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
</head>

<body>
  <?php
  include "../db/functions.php";
  //Check if user is legitimately logged in
  session_start();
  if (!isset($_SESSION["valid_user"]))
  {
  	// User not logged in, redirect to login page
  	header("Location: index.php");
  	exit();
  }
  	$userid = $_SESSION["user_id"];
      $author = @$_POST["author"];
  	$sid = @$_POST["sid"];
  	$sentfrom = @$_POST["sentfrom"];
  	$aid = @$_POST["aid"];
  	$title = @$_POST["title"];
  	$lastlistendt = @$_POST["lastlistendt"];
  	$listenct = @$_POST['listenct'];
  	$comment = @$_POST['comment'];
  	$tags = @$_POST['tags'];
  	$genre = @$_POST['genre'];
  	$seriesorder = @$_POST['seriesorder'];
  	$filepath = @$_POST['filepath'];
  	//$another = @$_POST['another'];
  	
  	$another="";
  	
  	if (isset($_POST['another']))
  	{
  	       //echo "another pressed";
  	       $another = "<input type='hidden' name='another' value='$aid'/>";
  	}
  	       
  	$mysqli = iConnect("tv");
  	
  if ($sentfrom == "addtitle.php")
  {
  	$addeddt = date("Y-m-d H:i:s");
  	$data = $mysqli->query("insert into storytapes(authorid,title,comment,genre,addeddt,listenct,
  	  seriesorder,tags,filepath) values($aid,'$title','$comment','$genre','$addeddt',$listenct,
  	  $seriesorder,'$tags','$filepath')");
  	  
  	//echo "insert into storytapes(authorid,title,comment,genre,addeddt,listenct,
  	  //seriesorder,tags,filepath) values($aid,'$title','$comment','$genre','$addeddt',$listenct,
  	  //$seriesorder,'$tags','$filepath')";
  	  
  	if ($another == "")
  	{
  	       $action = "home.php";
  	}
  	else
  	{
  	       $action = "addtitle.php";
  	}
  }
  else if ($sentfrom == "edittitle.php")
  {       
      $lastlistendt = date('Y-m-d',ukstrtotime($lastlistendt));
  	
  	if ($seriesorder == "-")
  	       $seriesorder = 0;
  	
  	$data = $mysqli->query("update storytapes set title='$title',
  	  lastlistendt='$lastlistendt', 
  	  listenct=$listenct,
  	  comment = '$comment',
  	  tags = '$tags',
  	  seriesorder = $seriesorder,
  	  filepath = '$filepath',
  	  genre = '$genre'
  	  where id=$sid");
  	  $action = "home.php";        
  	
  	$today = date("Y-m-d");
  	
  	if($lastlistendt == $today)
  	{
  	       $datau = $mysqli->query("insert into userstats(userid,titleid,lastlistendt)
  	         values($userid,$sid,'$lastlistendt')");
  	       
  	       $dataq = $mysqli->query("update queue set active='N' where titleid=$sid");
  	}
  }
  else if ($sentfrom == "addauthor.php")
  {       
  	$data = $mysqli->query("insert into authors(author) values('$author')");
  	$action = "addtitle.php";
  	
  	$data2 = $mysqli->query("select id
  	  from authors
  	  where author='$author'");
  	
  	$row = $data2->fetch_array();
  	$authorid = $row['id'];
  	
  	$another = "<input type='hidden' name='another' value='$authorid' />";
  }
  else if ($sentfrom == "queue")
  {       
  	$addeddt = date("Y-m-d H:i:s");
         $data = $mysqli->query("insert into queue(userid,titleid,queueddt) values('". $_SESSION["short_name"] ."',$sid,'$addeddt')");
  	$action = "home.php";
  	$title = "";
  }

else if ($sentfrom == "complete")
  {       
    $data = $mysqli->query("update storytapes set id3tags='N' where id=$sid"); // set it to no, such that the id3 tagging and m3u generation is done
  	$action = "home.php";
  }
     
  echo "<form method='post' action='$action' id='autosubmit' name='autosubmit'>";
  echo "<input type='hidden' name='q' value='$title' />";
  echo "$another";
  echo "</form>";
  ?><script language="JavaScript">
  document.autosubmit.submit();
  </script>
</body>
</html>
