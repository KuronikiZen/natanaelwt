<?php

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login');
    exit;
}

// Handle link addition
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['status' => 'error', 'message' => 'Failed to add link.'];
    $folder = 'texts/';
    $filename = $folder . 'links.txt';

    if (!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }

    $name = htmlspecialchars(trim($_POST['name']));
    $link = htmlspecialchars(trim($_POST['link']));

    if ($name && $link) {
        $data = "$name|$link\n";
        if (file_put_contents($filename, $data, FILE_APPEND | LOCK_EX)) {
            file_put_contents("log.txt", "The link " . $name . " has been uploaded by ".$_SESSION['user']."\n", FILE_APPEND | LOCK_EX);
            $response = ['status' => 'success', 'message' => 'Link added successfully.'];
        } else {
            $response['message'] = 'Failed to save link.';
        }
    }

    echo json_encode($response);
    exit;
}

function getLinks($folder)
{
    $filename = $folder . 'links.txt';
    if (!file_exists($filename)) {
        return '';
    }

    $linksData = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $html = "";

    foreach ($linksData as $linkData) {
        list($name, $link) = explode('|', $linkData);
        $html .= "<div class='col-sm-6 my-3 mb-sm-0'>
            <div class='card'>
                <div class='card-body'>
                    <h5 class='card-title'>" . htmlspecialchars($name) . "</h5>
                    <p class='card-text'>" . htmlspecialchars($link) . "</p>
                    <a href='" . htmlspecialchars($link) . "' target='_blank' class='btn btn-primary'>Visit Link</a>
                </div>
            </div>
        </div>";
    }

    return $html;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }

        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
        }

        .header h1 {
            margin: 0;
        }
    </style>
</head>

<body>
    <header class="header text-center">
        <h1>Link Manager</h1>
    </header>
    <main class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="dashboard" class="btn btn-secondary">← Back to Dashboard</a>
        </div>
        <form id="linkForm" method="POST" class="mb-4">
            <div class="input-group mb-3">
                <span class="input-group-text">Name</span>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text">Link</span>
                <input type="url" class="form-control" name="link" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Link</button>
        </form>
        <div class="row">
            <?php echo getLinks('texts/'); ?>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('linkForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            });
        });
    </script>
</body>

</html>
