<?php
	$CFG['dbuser'] = 'hcenter_db1';
	$CFG['dbpass'] = 'lUcJSs5Pw';
	$CFG['dbhost'] = 'localhost';
	$CFG['dbname'] = 'hcenter_db';

	$timezone = new DateTimeZone("Asia/Kolkata" );
	$dateformat = new DateTime();
	$dateformat->setTimezone($timezone);
	$dttime = $dateformat->format('Y-m-d G:i:s');
	$CFG['dttime'] = $dttime;
	
	define('BASEURL', 'http://hcenter.com/webservices/');
	define('IMGDIRURL', 'http://hcenter.com/');

	// define('BASEURL', 'http://hcenter.nethost.co.il/NEWCMS/webservices/');
	// define('IMGDIRURL', 'http://hcenter.nethost.co.il/NEWCMS/');

	// define('BASEURL', 'http://192.168.1.25/hcenter/webservices/');
	// define('IMGDIRURL', 'http://192.168.1.25/hcenter/');
	
	define('current_date',$dttime = $dateformat->format('Y-m-d'));
	define('current_time',$dttime = $dateformat->format('G:i:s'));
	define('current_datetime',$dttime = $dateformat->format('Y-m-d G:i:s'));
	
	define('GOOGLE_API_KEY', 'AIzaSyCTaHoZWXw2pqjII8Km1j9qy5CqVFR3LJs'); // For GCM

