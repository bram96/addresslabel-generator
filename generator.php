<?php

require_once 'tfpdf/tfpdf.php';

/**
 * NOTE
 * Hieronder exportfuncties voor diverse bestandsformaten
 */


function generate(Stickervel $stickervel, $data)
{

    $fpdf = new tFPDF();

    define('FPDF_FONTPATH', 'tfpdf/font/');
    $fpdf->AddFont('Sans', '', 'DejaVuSans.ttf', true);


    $fpdf->AddPage();
    $fpdf->SetFont('Sans', '', 10);
    $fpdf->SetMargins(0, 0);
    $fpdf->SetAutoPageBreak(false);


    // Eigenschappen stickervel
    $l_nRows = $stickervel->ver_aantal;
    $l_nCols = $stickervel->hor_aantal;


    // Tellervariabelen
    $l_nx = 1; // Kolom
    $l_ny = 1; // Rij

    foreach ($data as $key => $row) {

        // Maak een string van alle velden in $row, gescheiden door een nieuwe regel
        $labelText = implode("\n", array_map(function ($value) {
            return (string) $value;
        }, (array) $row));

        _pdf_add_label($fpdf, $stickervel, $l_nx, $l_ny, $labelText);



        $l_nx++;
        // Regel vol?
        if ($l_nx > $l_nCols) {
            $l_nx = 1;
            $l_ny++;
            // Pagina vol?
            if ($l_ny > $l_nRows) {
                $l_ny = 1;
                $fpdf->AddPage();
            }
        }
    }


    // Output pdf
    $fpdf->Output('adresstickers.pdf', 'D');

}


/**
 * _pdf_add_label Voeg een label toe aan het pdf-bestand
 * @access private
 * @param object $p_oPdf
 * @param Stickervel $p_aVel Dimensies van het stickervel
 * @param int $p_nx
 * @param int $p_ny
 * @param string $p_sData De tekst die in het label moet komen
 * @return void
 */
function _pdf_add_label(&$p_oPdf, Stickervel &$p_aVel, $p_nx, $p_ny, $p_sData)
{
    // Prints to an Avery 5160 label sheet which is a label
    // 2 5/8" wide by 1" tall, they are 3 accross on a page
    // and 10 rows per page. (30 per page). The upper left
    // corner is label(1,1) The X co-ord goes horizontally
    // accross the page and Y goes vertically down the page
    // Left/Right page margins are 4.2 MM (1/6 inch)
    // Top/Botton page margines are 12.7 MM (.5 inch)
    // Horizontal gap between labels is 4.2 MM (1/6 inch)
    // There is no vertial gap between labels
    // Labels are 66.6 MM (2 5/8") Wide
    // Labels are 25.4 MM (1" ) Tall


    // Create Co-Ords of Upper left of the Label
    $l_nAbsX = $p_aVel->marge_links + (($p_aVel->breedte + $p_aVel->hor_afstand) * ($p_nx - 1));
    $l_nAbsY = $p_aVel->marge_boven + (($p_aVel->hoogte + $p_aVel->ver_afstand) * ($p_ny - 1));


    // 4 mm marge (uitlijningsfouten vermijden)
    $p_oPdf->SetXY($l_nAbsX + 4, $l_nAbsY + 4);


    // Breedte cel:      breedte - 2 * 4: breedte min tweemaal de marge
    // Hoogte per regel: 4.5
    // Data:             de tekst
    // Border:           0: geen border
    // Align:            'L': align left en niet justification
    $p_oPdf->MultiCell($p_aVel->breedte - 2 * 4, 4.5, $p_sData, 0, 'L');
}
?>