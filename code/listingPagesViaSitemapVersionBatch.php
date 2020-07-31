<?php
var_dump($_SERVER['argv'][0]);
var_dump($_SERVER['argv'][1]);
$dossierRoutine = getcwd();
$sousDossierRoutine = "code";
$adresseSitemap = $_SERVER['argv'][1] . "/sitemap.xml"; // On définit le chemin du fichier à utiliser.
$nomFichierListe = "fichierliste.txt";
$liste = ListeURLSite($adresseSitemap);

function ListeURLSite($adresseSitemap)
{
    echo "\n########## " . $adresseSitemap . " ##########";

    $xml = file_get_contents($adresseSitemap);
    $dom = new DOMDocument;
    $dom->loadXML($xml);
    $urls = $dom->getElementsByTagName('loc');

    $listeCommandes = "";


    foreach ($urls as $url) {
        if ($url->tagName == "loc") {

            $info = new SplFileInfo($url->nodeValue);
            $extension = $info->getExtension();
            if ($extension == "xml") {
                $listeCommandes .= ListeURLSite($url->nodeValue);
            } else {
                $listeCommandes .= $url->nodeValue . "\n";
                echo "\n" . $url->nodeValue;
            }
        }
    }

    return $listeCommandes;

}

$tableau = explode("\n", $liste);
$tableau = array_map('trim', $tableau);
// On supprime les doublons
$tableau = array_unique($tableau);
// On se limite aux 100 premiers items
$tableau = array_slice($tableau, 0, 100);
file_put_contents($dossierRoutine . '/' . $sousDossierRoutine . "/" . $nomFichierListe, implode(PHP_EOL, $tableau));


?>