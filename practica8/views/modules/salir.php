<?php
	session_start();
	if(!$_SESSION["validar"]){
		header("location:index.php?action=ingresar");
		exit();
	}
	session_destroy();
    header("location:index.php?action=ingresar");
?>