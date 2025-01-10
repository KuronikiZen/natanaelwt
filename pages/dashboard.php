<?php

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login');
    exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .home-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        p {
            margin-bottom: 30px;
            font-size: 1.2rem;
        }

        .features h2 {
            margin-bottom: 10px;
        }

        .features ul {
            list-style-type: none;
            margin-bottom: 20px;
        }

        .features ul li {
            margin: 10px 0;
        }

        .features ul li a {
            text-decoration: none;
            color: #007bff;
            font-size: 1.1rem;
        }

        .features ul li a:hover {
            text-decoration: underline;
        }

        .logout-button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.2rem;
            transition: background-color 0.3s;
        }

        .logout-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="home-container">
        <h1>Selamat Datang di Dashboard</h1>
        <p>Halo, Anda berhasil login!</p>
        <div class="features">
            <h2>Fitur-fitur:</h2>
            <ul>
                <li><a href="fileManager">File Manager</a></li>
                <li><a href="#">Fitur 2</a></li>
                <li><a href="#">Fitur 3</a></li>
                <li><a href="#">Fitur 4</a></li>
                <li><a href="#">Fitur 5</a></li>
            </ul>
        </div>
        <a href="dashboard?logout=true" class="logout-button">Logout</a>
    </div>
</body>

</html>