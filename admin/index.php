<?php
header('Content-Type: text/html; charset=utf-8');
/**
 * Created by JetBrains PhpStorm.
 * User: student
 * Date: 14.08.13
 * Time: 10:43
 * To change this template use File | Settings | File Templates.
 */

include_once("../classMySQL.php");
include_once("classObj.php");
include_once("classContent.php");
include_once("classNotification.php");
include_once("classDisplay.php");
include_once("classGTFSStop.php");
include_once("classGTFSRoutes.php");
include_once("classGTFSAgency.php");
include_once("classLinie.php");
include_once("classMap.php");
include_once("classSpecialDeparture.php");

$seite = $_GET['seite'];
unset($_GET['seite']);
$content = new Content($seite,$_GET);