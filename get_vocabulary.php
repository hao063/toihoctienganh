<?php

if (isset($_GET['lesson']) && isset($_GET['studyMode'])) {
    $lesson    = $_GET['lesson'];
    $studyMode = $_GET['studyMode'];
    $jsonFile  = 'data/'.$lesson.'.json';
    if (file_exists($jsonFile)) {
        $jsonData = file_get_contents($jsonFile);
        echo $jsonData;
    } else {
        echo json_encode([]);
    }
}
?>
