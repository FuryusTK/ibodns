<?php
include ('includes/header.php');
?>
<style>
  .custom-button {
        padding: 10px 20px;
    }
    .image-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        margin-bottom: 20px;
    }
    .image-container {
        flex: 1 1 300px; /* Flex-grow, flex-shrink, and base width */
        max-width: 300px;
        margin: 10px;
        text-align: center;
        background-color: #FFF;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        padding: 10px;
    }
    .image-container img {
        width: 100%;
        height: auto;
        border-radius: 10px;
    }
    .horizontal-space {
        margin-right: 20px;
    }
    label, select, input {
        background: #F8F9FC;
        padding: 10px 20px 10px 20px;
        margin-left: 10px;
        border: none;
        border-radius: 10px;
        box-shadow: 5px 5px 5px 0 rgba(0,0,0,0.35);
    }
    form {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
        
        
  </style>


		
                            
                            
<?php


    $jsonData = file_get_contents('./api/theme.json');

    // Convert JSON data to PHP array
    $dataArray = json_decode($jsonData, true);
    
    // Function to extract and return "PanalData"
    function getPanalData($dataArray) {
        if (isset($dataArray[0]["PanalData"])) {
            return $dataArray[0]["PanalData"];
        } else {
            return null; // Return null if "PanalData" key not found
        }
    }
    // Get and print the "PanalData"
    $panalData = getPanalData($dataArray);
    
    $modelo = "";
    switch ($panalData) {
        case 'Widget_1': $modelo='Modelo 1';
        break;
        
        case 'Widget_2': $modelo = 'Modelo 2';
        break;
        
        case 'Widget_3': $modelo = 'Modelo 3';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $selectedOption = $_POST['options'];
        //echo " You new selected: " . $selectedOption;

        $jsonData = file_get_contents('./api/theme.json');
        $data = json_decode($jsonData, true);
        $data[0]["PanalData"] = $selectedOption;

        $jsonData = json_encode($data, JSON_PRETTY_PRINT);

        file_put_contents('./api/theme.json', $jsonData);
        
        echo "<script>window.location.href='UI_Setting.php';</script>";
    }
    
    
    ?>
    
    <div class="container-fluid">
        <div class="card border-left-primary shadow h-100 card shadow mb-4">
            <div class="col-md-6 mx-auto">
            	<div class="modal fade" id="how2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"></div>
        			<div class="card-body">
        				<center>
        				    <h2>
        				        <i class="fa fa-wrench"></i> Estilo dos anúncios</h2>
    					</center>
    				</div>
        			<div class="card-body">
        			    <span><center>O modelo atual é: <?= $modelo;?></center></span>
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <select name="options" width="300" id="options">
                            <option value="Widget_1">Modelo 1: Video</option>
                            <option value="Widget_2">Modelo 2: Ads</option>
                            <option value="Widget_3">Modelo 3: Esporte</option>
                            <!-- Add more options here if needed -->
                        </select>
                        <br>
                        <br>
                        <input type="submit" class="btn btn-primary btn-icon-split custom-button" value="Escolher">
                        </form>
                        <br>
                        <br>
                        <div class="image-row">
                            <div class="image-container">
                                <p>Modelo 1: Video</p>
                                <img src="./img/1.png" width="200" height="130" alt="Widget_1">
                            </div>
                        
                            <div class="image-container">
                                <p>Modelo 2: Ads</p>
                                <img src="./img/2.png" width="200" height="130" alt="Widget_2">
                            </div>
                        
                            <div class="image-container">
                                <p>Modelo 3: Esporte</p>
                                <img src="./img/3.png" width="200" height="130" alt="Widget_3">
                            </div>
                        </div>
    
                                
    				</div>
    			</div>
    		</div>
	    </div>
    </div>
</div>

<?php include ('includes/footer.php');?>

</body>
</html>