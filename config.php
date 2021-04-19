<?php
error_reporting(0);
$token		= "";							// Replace your bot http access token.
$admin		= "0000000";					// Replace your account id.
$admins		= [$admin];						// ['1', '2', '3'] for multi admins.
$channel	= "iNeoTeam";					// Replace your channel username without @.
$api		= "https://api.ineo-team.ir";	// Don't change it !
$seller		= "https://crm.example.com/cart.php?a=add&domain=register&query="; // Replace your hosting address for register domain.
$char		= array("[", "]", ">", "<", "*", "`", "_", "https://", "http://", "www.", "(", ")", "{", "}", "\"", "'", ";", "$"); // Don't change it !
unlink("error_log");
