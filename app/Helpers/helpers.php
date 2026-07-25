<?php

use NumberToWords\NumberToWords;

if (!function_exists('numberToWordsFr')) {
    function numberToWordsFr($number)
    {
        $numberToWords = new NumberToWords();
        // On demande le convertisseur pour la langue française
        $numberTransformer = $numberToWords->getNumberTransformer('fr');

        // On retourne le nombre en toutes lettres
        return ucfirst($numberTransformer->toWords($number));
    }
}
