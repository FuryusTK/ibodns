<?php
// Redirect to a specific URL
$redirect_url;
    
$jsonData = file_get_contents('theme.json');

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

if ($panalData == "Widget_1") {
$redirect_url = 'combo_1.php';
} elseif ($panalData == "Widget_2") {
$redirect_url = 'combo_2.php';
} elseif ($panalData == "Widget_3") {
$redirect_url = 'combo_3.php';
} elseif ($panalData == "Widget_4") {
$redirect_url = 'combo_4.php';
} elseif ($panalData == "Widget_5") {
$redirect_url = 'combo_5.php';
} elseif ($panalData == "Widget_6") {
$redirect_url = 'combo_6.php';
} else {
$redirect_url = 'combo_2.php';
}

header('Location: ' . $redirect_url);
exit; // Make sure to use 'exit' or 'die' after the header() function to stop further execution
