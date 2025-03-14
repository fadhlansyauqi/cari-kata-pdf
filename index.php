<?php
ini_set('max_execution_time', 1200); // 1200 detik (20 menit)
require 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

$keywords = [
    'Artificial intelligence', 'Business intelligence', 'image understanding', 'investment decision aid systems', 'intelligent data analysis', 'intelligent robotics', 'machine learning', 'deep learning', 'semantic search', 'biometrics', 'face recognition', 'speech recognition', 'authentication', 'autonomous driving', 'natural language processing',
    'Digital currency', 'smart contracts', 'distributed computing', 'decentralization', 'Bitcoin', 'alliance chain', 'differential privacy technology', 'consensus mechanism',
    'Memory computing', 'cloud computing', 'stream computing', 'graph computing', 'Internet of Things', 'multi-party secure computing', 'brain-like computing', 'green computing', 'cognitive computing', 'Fusion architecture', '100 million level concurrency', 'EB level storage', 'information physical systems',
    'Big data', 'data mining', 'text mining', 'data visualization', 'heterogeneous data', 'credit information', 'augmented reality', 'mixed reality', 'virtual reality',
    'Mobile Internet', 'Industrial Internet', 'mobile Internet', 'Internet medical', 'e-commerce', 'mobile payment', 'third-party payment', 'NFC Payment', 'B2B', 'B2C', 'C2B', 'C2C', 'O2O', 'Internet connection', 'smart wear', 'smart agriculture', 'smart transportation', 'smart medical', 'smart customer service', 'smart home', 'smart investment', 'smart travel', 'smart environmental protection', 'smart grid', 'smart energy', 'smart marketing', 'digital marketing', 'unmanned retail', 'Internet finance', 'digital finance', 'Fintech', 'financial technology', 'Quantitative finance', 'open banking'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['pdf_file'])) {
        $pdfFile = $_FILES['pdf_file']['tmp_name'];

        // Pastikan file diunggah dan adalah PDF
        if (mime_content_type($pdfFile) !== 'application/pdf') {
            die('File harus berupa PDF.');
        }

        $parser = new Parser();
        $pdf = $parser->parseFile($pdfFile);

        $pages = $pdf->getPages();
        $results = [];

        foreach ($pages as $pageNumber => $page) {
            $text = strtolower($page->getText());

            foreach ($keywords as $keyword) {
                $keywordLower = strtolower($keyword);
                $count = substr_count($text, $keywordLower);

                if ($count > 0) {
                    if (!isset($results[$keyword])) {
                        $results[$keyword] = [
                            'count' => 0,
                            'pages' => []
                        ];
                    }
                    $results[$keyword]['count'] += $count;
                    $results[$keyword]['pages'][] = $pageNumber + 1; // Halaman dimulai dari 1
                }
            }
        }

        echo "<h2>Hasil Pencarian</h2>";

        if (empty($results)) {
            echo "<p>Tidak ada kata kunci yang ditemukan dalam dokumen.</p>";
        } else {
            foreach ($results as $word => $data) {
                echo "<p>Kata '<strong>{$word}</strong>' ditemukan sebanyak <strong>{$data['count']}</strong> kali di halaman: " . implode(', ', array_unique($data['pages'])) . "</p>";
            }
        }
    } else {
        echo "Harap unggah file PDF.";
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

        <button type="submit">Cari di PDF</button>
    </form>
</body>
</html>