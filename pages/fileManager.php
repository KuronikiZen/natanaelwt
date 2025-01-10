<?php

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login');
    exit;
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['status' => 'error', 'message' => 'File upload failed.'];
    $targetDir = 'uploads/';
    $targetFile = $targetDir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;

    // Check if file already exists
    if (file_exists($targetFile)) {
        $response['message'] = 'File already exists.';
        $uploadOk = 0;
    }

    if ($uploadOk) {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
            file_put_contents("log.txt", "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded to " . $targetDir . " by ".$_SESSION['user']."\n", FILE_APPEND | LOCK_EX);
            $response = ['status' => 'success', 'message' => 'File uploaded successfully.'];
        } else {
            $response['message'] = 'Error uploading the file.';
        }
    }
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager - Natanael WT</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
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

        .table th {
            background-color: #007bff;
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }

        .btn-upload {
            background-color: #007bff;
            color: white;
        }

        .btn-upload:hover {
            background-color: #0056b3;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }

        .upload-info {
            font-size: 0.9rem;
            margin-top: 5px;
        }
    </style>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <header class="header text-center">
        <h1>File Manager</h1>
    </header>
    <main class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="dashboard" class="btn btn-back">← Back to Dashboard</a>
        </div>
        <form id="uploadForm" enctype="multipart/form-data" class="mb-4">
            <div class="input-group">
                <input class="form-control" type="file" name="fileToUpload" id="fileToUpload" required>
                <button type="submit" class="btn btn-upload">Upload</button>
            </div>
            <div class="progress mt-3" style="display: none;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="progressBar">0%</div>
            </div>
            <div class="upload-info" style="display: none;" id="uploadInfo"></div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Size</th>
                        <th scope="col">Date</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $folder = 'uploads/';
                    $files = scandir($folder);
                    foreach ($files as $file) {
                        if ($file !== '.' && $file !== '..') {
                            $size = filesize("$folder$file");
                            $formatted_size = number_format($size / 1024, 2) . ' KB';
                            $last_modified_date = date("Y-m-d H:i:s", filemtime("$folder$file"));
                            $filePath = "$folder$file";

                            echo "<tr>
                                <td>$file</td>
                                <td>$formatted_size</td>
                                <td>$last_modified_date</td>
                                <td><a href='$filePath' download class='btn btn-sm btn-success'>Download</a></td>
                            </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('uploadForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            var xhr = new XMLHttpRequest();
            var uploadInfo = document.getElementById('uploadInfo');
            var progressBar = document.getElementById('progressBar');
            var startTime = new Date().getTime();

            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    var percent = Math.round((e.loaded / e.total) * 100);
                    var elapsedTime = (new Date().getTime() - startTime) / 1000; // seconds
                    var speed = (e.loaded / 1024 / elapsedTime).toFixed(2); // KB/s
                    var uploadedMB = (e.loaded / 1024 / 1024).toFixed(2);
                    var totalMB = (e.total / 1024 / 1024).toFixed(2);

                    progressBar.style.width = percent + '%';
                    progressBar.setAttribute('aria-valuenow', percent);
                    progressBar.innerHTML = percent + '%';

                    uploadInfo.style.display = 'block';
                    uploadInfo.innerHTML = `Uploaded: ${uploadedMB} MB / ${totalMB} MB | Speed: ${speed} KB/s`;
                }
            });

            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                    document.querySelector('.progress').style.display = 'none';
                    progressBar.style.width = '0%';
                    progressBar.setAttribute('aria-valuenow', 0);
                    progressBar.innerHTML = '0%';
                    uploadInfo.style.display = 'none';
                }
            };

            xhr.open('POST', '', true);
            xhr.send(formData);

            document.querySelector('.progress').style.display = 'block';
        });
    </script>
</body>

</html>
