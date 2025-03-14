<?php
ini_set('max_execution_time', 600); // 300 detik (10 menit)
require 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['pdf_file']) && !empty($_POST['search_word'])) {
        $pdfFile = $_FILES['pdf_file']['tmp_name'];
        $searchWord = strtolower(trim($_POST['search_word']));

        // Pastikan file diunggah dan adalah PDF
        if (mime_content_type($pdfFile) !== 'application/pdf') {
            die('File harus berupa PDF.');
        }

        $parser = new Parser();
        $pdf = $parser->parseFile($pdfFile);

        $pages = $pdf->getPages();
        $totalOccurrences = 0;
        $foundPages = [];

        foreach ($pages as $pageNumber => $page) {
            $text = strtolower($page->getText());
            $count = substr_count($text, $searchWord);

            if ($count > 0) {
                $totalOccurrences += $count;
                $foundPages[] = $pageNumber + 1; // Halaman dimulai dari 1
            }
        }

        echo "<h2>Hasil Pencarian</h2>";
        echo "<p>Kata '<strong>{$searchWord}</strong>' ditemukan sebanyak <strong>{$totalOccurrences}</strong> kali.</p>";
        if (!empty($foundPages)) {
            echo "<p>Berada di halaman: " . implode(', ', $foundPages) . "</p>";
        } else {
            echo "<p>Kata tidak ditemukan dalam dokumen.</p>";
        }
    } else {
        echo "Harap unggah file PDF dan masukkan kata yang ingin dicari.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pencarian Kata di PDF</title>
</head>
<body>
    <h1>Upload PDF dan Cari Kata</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="pdf_file">Pilih File PDF:</label>
        <input type="file" name="pdf_file" id="pdf_file" accept="application/pdf" required><br><br>

        <label for="search_word">Kata yang Dicari:</label>
        <input type="text" name="search_word" id="search_word" required><br><br>

        <button type="submit">Cari di PDF</button>
    </form>
</body>
</html>
