<?php
session_start();
// Verificar se o usuário está autenticado e é um administrador
if (!isset($_SESSION['id']) || !$_SESSION['admin']) {
    header("Location: login.php");
    exit();
}
include "includes/header.php";
?>
<style>
    .custom-button {
        padding: 10px 20px;
    }
    #url-form {
        display: none;
    }
    .custom-input {
        color: blue;
    }
    .preview-image {
        width: 100%;
        height: auto;
        border-radius: 20px;
        display: block;
        margin: 0 auto;
    }
    @media (min-width: 1200px) {
        .preview-image {
            width: 50%;
        }
    }
    @media (min-width: 768px) and (max-width: 1199px) {
        .preview-image {
            width: 70%;
        }
    }
    @media (max-width: 767px) {
        .preview-image {
            width: 100%;
        }
    }
    .container-fluid {
        text-align: center;
    }
</style>
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-1 text-gray-800">Image Background</h1>
    <!-- Custom codes -->
    <div class="card border-left-primary shadow h-100 card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-cogs"></i> Choose</h6>
        </div>
        <div class="card-body">
            <?php
						
						$jsonFilex = './img/fundo/image_filenames.json';
        
                        // Read the JSON file contents
                         $jsonDatax = file_get_contents($jsonFilex);
                            
                        // Decode the JSON data
                        $imageDatax = json_decode($jsonDatax, true);
                            
                        // Extract the filename
                        $filenamex = $imageDatax[0]['ImageName'];
                        $uploadmethord = $imageDatax[0]['Upload_type'];
                        
                        if ($uploadmethord == "by_file") {
                            $string = $filenamex;
                            $firstLetterRemoved = substr($string, 1);
                            $imageFilex = "$firstLetterRemoved";
                            $methord = "Upload Method";
                        } elseif ($uploadmethord == "by_url") {
                            $imageFilex = "$filenamex";
                            $methord = "URL Method";
                        } else {
                            $imageFilex = "https://c4.wallpaperflare.com/wallpaper/159/71/731/errors-minimalism-typography-red-wallpaper-preview.jpg";
                            $methord = "";
                        }
						
						echo '<h3>Currently in use:' . $methord . '</h3>';
						echo '<input type="radio" name="upload-type" id="upload-radio" checked> Set Background Using File &nbsp&nbsp';
                        echo '<input type="radio" name="upload-type" id="url-radio"> Set Background Using URL';
                        echo '<br>';
                        echo '<img class="preview-image" src="' . $imageFilex . '" alt="Uploaded Image">';
                        echo '<br><br>';
                        
                        if (isset($_POST['upload'])) {
                            // Handle the uploaded file
                            // Check if the form was submitted
                        
                                $selectedFiles = ['logo.png', 'index.php', 'iimg.json', 'filenames.json', 'binding_dark.webp', 'bg.jpg', 'api.php', 'favicon.ico', 'logo_ne.png', '.htaccess']; // Example array of selected files
                                $folderPath = './img_custom/Img/'; // Replace with the actual folder path
                                
                                $files = scandir($folderPath);
                                
                                foreach ($files as $file) {
                                    if ($file !== '.' && $file !== '..') {
                                        $filePath = $folderPath . $file;
                                
                                        // Check if the file is selected
                                        if (in_array($file, $selectedFiles)) {
                                            // File is selected, do nothing
                                        } else {
                                            // Delete the file
                                            unlink($filePath);
                                        }
                                    }
                                }
                                
                            if (isset($_FILES['image'])) {
                                $file = $_FILES['image'];
                                $fileType = $file['type'];
                                $fileTemp = $file['tmp_name'];
                        
                                // Validate the file type
                                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                                if (in_array($fileType, $allowedTypes)) {
                                    // Define the path to store the uploaded image
                                    $uploadPath = './img_custom/Img/';
                                    $fileName = uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
                                    $destination = $uploadPath . $fileName;
                        
                                    // Move the uploaded file to the destination
                                    if (move_uploaded_file($fileTemp, $destination)) {
                                        echo "<script>window.location.href='Image.php';</script>";
                                        
                                        $jsonFilePath = './img/fundo/image_filenames.json';
                                        $jsonData = json_encode([["ImageName" => "../img_custom/Img/" . $fileName, 'Upload_type' => 'by_file']]);
                                        file_put_contents($jsonFilePath, $jsonData);
                                    } else {
                                        echo 'Failed to move the uploaded file.';
                                    }
                                } else {
                                    echo 'Invalid file type. Only JPEG, PNG, and GIF images are allowed.';
                                }
                            }
                        }
                        // Image by URL handling
                            if (isset($_POST['url-submit'])) {
                                $imageUrl = $_POST['image-url'];
                            
                                // Validate the URL
                                if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                                    $jsonFilePath = './img/fundo/image_filenames.json';
                            
                                    // Prepare the data to be added to the JSON file
                                    $newImageData = [
                                        'ImageName' => $imageUrl,
                                        'Upload_type' => 'by_url'
                                    ];
                            
                                    // Read the existing JSON data
                                    $jsonData = file_get_contents($jsonFilePath);
                            
                                    // Decode the JSON data
                                    $imageData = json_decode($jsonData, true);
                            
                                    // Add the new image data to the existing array
                                    $imageData[0] = $newImageData;
                            
                                    // Encode the updated data as JSON
                                    $jsonData = json_encode($imageData);
                            
                                    // Save the updated JSON data to the file
                                    if (file_put_contents($jsonFilePath, $jsonData)) {
                                        echo "<script>window.location.href='Image.php';</script>";
                                    } else {
                                        echo 'Failed to save the image data to the JSON file.';
                                    }
                                } else {
                                    echo 'Invalid URL.';
                                }
                            }

                        ?>
                        
                        <form method="post" enctype="multipart/form-data" id="upload-form">
                            <label for="image">Select an Image to upload:</label>
                            <input class="custom-button" type="file" name="image" id="image" accept="image/jpeg, image/png, image/gif">
                            <button class="custom-button btn btn-success btn-icon-split" type="submit" name="upload">Upload</button>
                        </form>
                        
                        <form method="post" id="url-form">
                            <label for="image-url">Select an Image URL:</label>
                            <input class="custom-button" type="text" name="image-url" id="image-url" placeholder="https://example.com/image.jpg">
                            <button class="custom-button btn btn-success btn-icon-split" type="submit" name="url-submit">Submit URL</button>
                        </form>
                        
                        <script>
                            const uploadRadio = document.getElementById('upload-radio');
                            const urlRadio = document.getElementById('url-radio');
                            const uploadForm = document.getElementById('upload-form');
                            const urlForm = document.getElementById('url-form');
                        
                            uploadRadio.addEventListener('change', () => {
                                uploadForm.style.display = 'block';
                                urlForm.style.display = 'none';
                            });
                        
                            urlRadio.addEventListener('change', () => {
                                uploadForm.style.display = 'none';
                                urlForm.style.display = 'block';
                            });
                        </script>

        </div>
    </div>
</div>
<?php
include "includes/footer.php";
?>
</body>
</html>
