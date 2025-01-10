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
        <form action="fileManager" method="post" enctype="multipart/form-data" class="mb-4">
            <div class="input-group">
                <input class="form-control" type="file" name="fileToUpload" id="fileToUpload">
                <button type="submit" value="Unggah File" name="submit" class="btn btn-upload">Upload</button>
            </div>
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
                    if (isset($_POST["submit"])) {
                        $targetDir = 'uploads/';
                        $targetFile = $targetDir . basename($_FILES["fileToUpload"]["name"]);
                        $uploadOk = 1;
                        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

                        if (file_exists($targetFile)) {
                            echo "<script>
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'File Already Exists',
                                        text: 'Sorry, the file already exists.'
                                    });
                                  </script>";
                            $uploadOk = 0;
                        }

                        if ($uploadOk == 0) {
                            echo "<script>
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Upload Failed',
                                        text: 'Sorry, the file was not uploaded.'
                                    });
                                  </script>";
                        } else {
                            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
                                echo "<script>
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success!',
                                            text: 'The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.'
                                        });
                                      </script>";
                                file_put_contents("log.txt", "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded to " . $targetDir . "\n", FILE_APPEND | LOCK_EX);
                            } else {
                                echo "<script>
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'Sorry, there was an error uploading your file.'
                                        });
                                      </script>";
                            }
                        }
                    }

                    $folder = 'uploads/';
                    $files = scandir($folder);

                    usort($files, function ($a, $b) {
                        return pathinfo($a, PATHINFO_EXTENSION) <=> pathinfo($b, PATHINFO_EXTENSION);
                    });

                    $currentExt = null;

                    function formatSizeUnits($bytes)
                    {
                        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
                            $bytes /= 1024;
                        }
                        return round($bytes, 2) . ' ' . $units[$i];
                    }

                    foreach ($files as $file) {
                        if ($file !== '.' && $file !== '..') {
                            $ext = pathinfo($file, PATHINFO_EXTENSION);
                            $size = filesize("$folder$file");
                            $formatted_size = formatSizeUnits($size);
                            $last_modified_timestamp = filemtime("$folder$file");
                            $last_modified_date = date("Y-m-d H:i:s", $last_modified_timestamp);

                            if ($ext !== $currentExt) {
                                $currentExt = $ext;
                            }

                            $filePath = "$folder$file";
                            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                                echo "<tr>
                                <td>$file</td>
                                <td>$formatted_size</td>
                                <td>$last_modified_date</td>
                                <td><a href='$filePath' class='btn btn-sm btn-primary'>Preview</a> <a href='$filePath' download class='btn btn-sm btn-success'>Download</a></td>
                            </tr>";
                            } else {
                                echo "<tr>
                                <td>$file</td>
                                <td>$formatted_size</td>
                                <td>$last_modified_date</td>
                                <td><a href='$filePath' download class='btn btn-sm btn-success'>Download</a></td>
                            </tr>";
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
