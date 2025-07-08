<?php
class Stickervel
{
    public $ID;
    public $naam;
    public $hor_aantal;
    public $ver_aantal;
    public $hor_afstand;
    public $ver_afstand;
    public $marge_boven;
    public $marge_links;
    public $breedte;
    public $hoogte;

    /**
     * Constructor for Stickervel_model
     * @param array $data Optional associative array to initialize properties
     */
    public function __construct($data = [])
    {
        $this->ID = isset($data['ID']) ? $data['ID'] : null;
        $this->naam = isset($data['naam']) ? $data['naam'] : '';
        $this->hor_aantal = isset($data['hor_aantal']) ? (float) $data['hor_aantal'] : 0.0;
        $this->ver_aantal = isset($data['ver_aantal']) ? (float) $data['ver_aantal'] : 0.0;
        $this->hor_afstand = isset($data['hor_afstand']) ? (float) $data['hor_afstand'] : 0.0;
        $this->ver_afstand = isset($data['ver_afstand']) ? (float) $data['ver_afstand'] : 0.0;
        $this->marge_boven = isset($data['marge_boven']) ? (float) $data['marge_boven'] : 0.0;
        $this->marge_links = isset($data['marge_links']) ? (float) $data['marge_links'] : 0.0;
        $this->breedte = isset($data['breedte']) ? (float) $data['breedte'] : 0.0;
        $this->hoogte = isset($data['hoogte']) ? (float) $data['hoogte'] : 0.0;
    }
}
