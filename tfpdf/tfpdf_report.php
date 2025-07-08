<?php


require_once('Ttpdf.php');


class VariableStream
{
    var $varname;
    var $position;


    function stream_open($path, $mode, $options, &$opened_path)
    {
        $url = parse_url($path);
        $this->varname = $url['host'];
        if (!isset($GLOBALS[$this->varname])) {
            trigger_error('Global variable ' . $this->varname . ' does not exist', E_USER_WARNING);
            return false;
        }
        $this->position = 0;
        return true;
    }


    function stream_read($count)
    {
        $ret = substr($GLOBALS[$this->varname], $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }


    function stream_eof()
    {
        return $this->position >= strlen($GLOBALS[$this->varname]);
    }


    function stream_tell()
    {
        return $this->position;
    }


    function stream_seek($offset, $whence)
    {
        if ($whence == SEEK_SET) {
            $this->position = $offset;
            return true;
        }
        return false;
    }

    function stream_stat()
    {
        return array();
    }
}


class tfpdf_rapport extends tfpdf
{
    protected $m_nBriefhoofdtype;
    protected $m_dAfbeelding;
    protected $m_sKerkverband;
    protected $m_sPlaats;

    function __construct($orientation = 'P', $unit = 'mm', $format = 'A4')
    {
        parent::__construct($orientation, $unit, $format);
        stream_wrapper_register('var', 'VariableStream'); // Register var stream protocol
    }

    /**
     * 1 => Tekst (kerkverband en plaats)
     * 2 => Lege ruimte (voorbedrukt papier)
     * 3 => Afbeelding
     */

    public function set_briefhoofdtype($p_nBriefhoofdtype)
    {
        $this->m_nBriefhoofdtype = $p_nBriefhoofdtype;
    }

    public function set_afbeelding($p_dAfbeelding)
    {
        $this->m_dAfbeelding = $p_dAfbeelding;
    }

    public function set_kerkverband($p_sKerkverband)
    {
        $this->m_sKerkverband = $p_sKerkverband;
    }

    public function set_plaats($p_sPlaats)
    {
        $this->m_sPlaats = $p_sPlaats;
    }

    public function Header()
    {
        if ($this->m_nBriefhoofdtype == 1) {
            // Tekst
            $this->Setfont('Font', '', 22);
            $this->MultiCell(0, 22, $this->m_sKerkverband, 0);
            $this->MultiCell(0, 0, $this->m_sPlaats, 0);
            $this->MultiCell(0, 8, '', 'B', 'C');
            $this->MultiCell(0, 9, "", 0, '');
        } else
            if ($this->m_nBriefhoofdtype == 2) {
                // Voorbedrukt papier
                $this->Setfont('Font', '', 22);
                $this->MultiCell(0, 22, '', 0);
                $this->MultiCell(0, 0, '', 0);
                $this->MultiCell(0, 8, '');//, 'B', 'C');
                $this->MultiCell(0, 9, "", 0, '');
            } else
                if ($this->m_nBriefhoofdtype == 3) {
                    // Afbeelding
                    $this->MemImage($this->m_dAfbeelding, 0, 0, -600, -600);
                    $this->Ln(getimagesizefromstring($this->m_dAfbeelding)[1] / 600 * 25.4); // Converteer naar mm
                    /*$this->Setfont('Font', '', 22);
                    $this->MultiCell(0, 22,  '' , 0);
                    $this->MultiCell(0, 0, '' , 0);
                    $this->MultiCell(0, 8, '' );//, 'B', 'C');
                    $this->MultiCell(0, 9, "", 0, '');*/
                }
    }

    public function Footer()
    {
        // Experimenteel bepaald maximaal XY(-44,-21)
        $this->Setfont('Font', '', 8);
        $this->SetXY($this->lMargin, -25);
        $this->Write(0, '© www.skribi.nl');
    }

    // Code from http://www.fpdf.org/en/script/script45.php
    // to support memory loaded images
    function MemImage($data, $x = null, $y = null, $w = 0, $h = 0, $link = '')
    {
        //Display the image contained in $data
        $v = 'img' . md5($data);
        $GLOBALS[$v] = $data;
        $a = getimagesize('var://' . $v);
        if (!$a)
            $this->Error('Invalid image data');
        $type = substr(strstr($a['mime'], '/'), 1);
        $this->Image('var://' . $v, $x, $y, $w, $h, $type, $link);
        unset($GLOBALS[$v]);
    }


    function GDImage($im, $x = null, $y = null, $w = 0, $h = 0, $link = '')
    {
        //Display the GD image associated to $im
        ob_start();
        imagepng($im);
        $data = ob_get_clean();
        $this->MemImage($data, $x, $y, $w, $h, $link);
    }
}
