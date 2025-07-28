<?php
require_once 'tfpdf/tfpdf.php';

require_once 'stickervel.php';
require_once 'generator.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    // Construct Stickervel_model from form values
    $stickervel = new Stickervel($_POST);

    $file = $_FILES['csv_file']['tmp_name'];
    $rows = [];
    $separator = isset($_POST['csv_separator']) ? $_POST['csv_separator'] : ',';
    if ($separator === '\\t') {
        $separator = "\t";
    }
    if (($handle = fopen($file, 'r')) !== false) {
        while (($data = fgetcsv($handle, 1000, $separator)) !== false) {
            $rows[] = $data;
        }
        fclose($handle);
    }

    generate($stickervel, $rows);
    exit;
    // Generate PDF using FPDF
    $pdf = new TFPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);

    foreach ($rows as $row) {
        $pdf->Cell(0, 10, implode(', ', $row), 0, 1);
    }

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="output.pdf"');
    $pdf->Output('D', 'output.pdf');
    exit;
}
?>

<!DOCTYPE html>
<html>


<head>
    <title>Upload CSV</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <form method="post" enctype="multipart/form-data">
        <div class="form-container">
            <div class="form-group">
                <label for="preset" class="form-label">Voorinstelling:</label>
                <select id="preset" class="form-input">
                    <option value="">-- Kies een voorinstelling --</option>
                    <option value="hema525">Hema 525</option>
                </select>
            </div>
            <div class="file-upload">
                <label for="csv_file" class="form-label">Select CSV file:</label>
                <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
            </div>
            <div class="form-group">
                <label for="csv_separator" class="form-label">CSV scheidingsteken:</label>
                <select id="csv_separator" name="csv_separator" class="form-input" required>
                    <option value=",">Komma (,)</option>
                    <option value=";">Puntkomma (;)</option>
                    <option value="\t">Tab</option>
                </select>
            </div>
            <div class="form-group">
                <label for="naam" class="form-label">Naam:</label>
                <input type="text" id="naam" name="naam" required class="form-input">
            </div>
            <div class="form-group">
                <label for="hor_aantal" class="form-label">Hor_aantal:</label>
                <input type="number" id="hor_aantal" name="hor_aantal" required class="form-input">
            </div>
            <div class="form-group">
                <label for="ver_aantal" class="form-label">Ver_aantal:</label>
                <input type="number" id="ver_aantal" name="ver_aantal" required class="form-input">
            </div>
            <div class="form-group">
                <label for="hor_afstand" class="form-label">Hor_afstand:</label>
                <input type="number" step="any" id="hor_afstand" name="hor_afstand" required class="form-input">
            </div>
            <div class="form-group">
                <label for="ver_afstand" class="form-label">Ver_afstand:</label>
                <input type="number" step="any" id="ver_afstand" name="ver_afstand" required class="form-input">
            </div>
            <div class="form-group">
                <label for="marge_boven" class="form-label">Marge_boven:</label>
                <input type="number" step="any" id="marge_boven" name="marge_boven" required class="form-input">
            </div>
            <div class="form-group">
                <label for="marge_links" class="form-label">Marge_links:</label>
                <input type="number" step="any" id="marge_links" name="marge_links" required class="form-input">
            </div>
            <div class="form-group">
                <label for="breedte" class="form-label">Breedte:</label>
                <input type="number" step="any" id="breedte" name="breedte" required class="form-input">
            </div>
            <div class="form-group">
                <label for="hoogte" class="form-label">Hoogte:</label>
                <input type="number" step="any" id="hoogte" name="hoogte" required class="form-input">
            </div>
            <button type="submit">Upload and Generate PDF</button>
        </div>
    </form>

    <script>
        // Preset values for the select box
        const presets = {
            hema525: {
                naam: 'Hema 525',
                hor_aantal: 3,
                ver_aantal: 7,
                hor_afstand: 2.5,
                ver_afstand: 0,
                marge_boven: 15,
                marge_links: 7,
                breedte: 63.5,
                hoogte: 38.1
            },
            niceday: {
                naam: 'Niceday 63.5x38.1',
                hor_aantal: 3,
                ver_aantal: 7,
                hor_afstand: 3,
                ver_afstand: 0,
                marge_boven: 15.5,
                marge_links: 8,
                breedte: 63.5,
                hoogte: 38.1
            }
        };

        document.getElementById('preset').addEventListener('change', function () {
            const val = this.value;
            if (presets[val]) {
                for (const key in presets[val]) {
                    const input = document.getElementsByName(key)[0];
                    if (input) {
                        input.value = presets[val][key];
                    }
                }
            }
        });
    </script>
</body>

</html>