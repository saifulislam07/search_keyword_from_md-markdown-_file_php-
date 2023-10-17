<?php

require_once 'vendor/erusev/parsedown/Parsedown.php';

function searchForSpecificInfo($file, $searchKeyword)
{
    $content = file_get_contents($file);

    if (stripos($content, $searchKeyword) !== false) {
        // If the search keyword is found in the content, return only that part of the content
        return [
            'file' => $file,
            'content' => $content,
            'matching_content' => preg_replace("/($searchKeyword)/i", '<mark>$1</mark>', $content),
        ];
    }

    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadedFile = $_FILES['file']['tmp_name'];
    $searchKeyword = $_POST['keyword'];

    if ($uploadedFile !== '' && file_exists($uploadedFile)) {
        $result = searchForSpecificInfo($uploadedFile, $searchKeyword);

        if ($result !== null) {

            echo "<html><head><title>Search Result</title></head><body>";
            // echo "<h2>File: " . $result['file'] . "</h2>";
            echo "<h3>Matching Content (<b style='background:yellow'>Highlighted</b>):</h3>";

            // Initialize Parsedown
            $parsedown = new Parsedown();

            // Output matching content line by line with Markdown conversion
            $lines = explode("\n", $result['matching_content']);
            foreach ($lines as $line) {
                echo $parsedown->text($line) . "<br>";
            }

            echo "</body></html>";
        } else {
            echo "<html><body>";
            echo "<p>Keyword not found in the uploaded file.</p>";
            echo "</body></html>";
        }
    } else {
        echo "<html><body>";
        echo "<p>Error: File not uploaded.</p>";
        echo "</body></html>";
    }
}
