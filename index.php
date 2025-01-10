<?php
session_start();
// Ambil parameter dari URL segmen
$param1 = isset($_GET['param1']) ? $_GET['param1'] : null;
$param2 = isset($_GET['param2']) ? $_GET['param2'] : null;
$param3 = isset($_GET['param3']) ? $_GET['param3'] : null;

// Routing logika berdasarkan parameter
switch($param1) {
    case '':
        include 'pages/dashboard.php';
        break;
    case 'dashboard':
        include 'pages/dashboard.php';
        break;
    case 'fileManager':
        include 'pages/fileManager.php';
        break;
    case 'login':
        include 'pages/login.php';
        break;
    case 'logout':
        include 'pages/logout.php';
        break;
    default:
        include 'pages/404.php';
        break;
}
?>
