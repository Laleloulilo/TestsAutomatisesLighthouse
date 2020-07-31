<?php ob_start();
$dossierRoutine = getcwd();
?>
    <!doctype html>
    <html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="./code/bootstrap.min.css">

        <title>Rapport d'audit</title>
    </head>
    <body>

    <h1>Audit</h1>
    <div class="alert alert-warning" role="alert">
        <h4 class="alert-heading">Attention aux rapports</h4>
        <p>Pour des questions de stockage, les rapports ne sont disponibles que pour la dernière analyse faite.</p>
        <hr>
    </div>


    <table class="table" aria-describedby="tableau de résultat">

        <thead>
        <tr>
            <th scope="col" class="text-center">URL</th>
            <th scope="col" class="text-center">Global</th>
            <th scope="col" class="text-center">Rapport</th>
            <th scope="col" class="text-center">Performance</th>
            <th scope="col" class="text-center">Accessibilité</th>
            <th scope="col" class="text-center">SEO</th>
            <th scope="col" class="text-center">Bonnes Pratiques</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $fichierADecoder = "./report/summary.json";
        $json = file_get_contents($fichierADecoder);
        $json_data = json_decode($json, true);
        $arrayglobal = array();

        function couleurBG($valeur)
        {
            switch ($valeur) {
                case($valeur >= 90):
                    return "bg-success";
                    break;
                case($valeur >= 50):
                    return "bg-warning";
                    break;
                default :
                    return "bg-danger";
            }
        }


        function retournerValeurGlobale($nombre1, $nombre2, $nombre3, $nombre4, $Erreur = "⚠")
        {
            if (is_numeric($nombre1) && is_numeric($nombre2) && is_numeric($nombre3) && is_numeric($nombre4)) {
                return floor(($nombre1 + $nombre2 + $nombre3 + $nombre4) / 4);
            }
            return $Erreur;
        }

        foreach ($json_data as $dataAnalyse) {


            $perf = isset($dataAnalyse["detail"]["performance"]) ? $dataAnalyse["detail"]["performance"] * 100 : "⚠";
            $accessibilite = isset($dataAnalyse["detail"]["accessibility"]) ? $dataAnalyse["detail"]["accessibility"] * 100 : "⚠";
            $bp = isset($dataAnalyse["detail"]["best-practices"]) ? $dataAnalyse["detail"]["best-practices"] * 100 : "⚠";
            $seo = isset($dataAnalyse["detail"]["seo"]) ? $dataAnalyse["detail"]["seo"] * 100 : "⚠";
            $global = retournerValeurGlobale($perf, $accessibilite, $bp, $seo);
            $url = isset($dataAnalyse["url"]) ? $dataAnalyse["url"] : "⚠️";
            $rapport = isset($dataAnalyse["html"]) ? $dataAnalyse["html"] : NULL;
            $ordonnancement = array(
                "rapport" => $rapport,
                "url" => $url,
                "global" => $global,
                "performance" => $perf,
                "accessibility" => $accessibilite,
                "best-practices" => $bp,
                "seo" => $seo);
            array_push($arrayglobal, $ordonnancement);

        }

        //on ordonne tout ça par score global
        $colonne = array_column($arrayglobal, 'global');
        // On ordonne le fichier d'en-tête par date
        array_multisort($colonne, SORT_DESC, $arrayglobal);


        foreach ($arrayglobal as $valeur) {


            $url = $valeur["url"];
            $global = $valeur["global"];
            $couleurGlobal = couleurBG($global);
            $performance = $valeur["performance"];
            $couleurPerformance = couleurBG($performance);
            $accessibilite = $valeur["accessibility"];
            $couleurAccessibilite = couleurBG($accessibilite);
            $bp = $valeur["best-practices"];
            $couleurBp = couleurBG($bp);
            $seo = $valeur["seo"];
            $couleurSeo = couleurBG($seo);
            $rapport = $valeur["rapport"];
            ?>

            <tr>
                <td><a href="<?= $url ?>"><?= $url ?></a></td>
                <th scope="row" class="text-center <?= $couleurGlobal ?>"><?= $global ?></th>
                <td class="text-center"><a href="./report/<?= $rapport ?>">📝</a></td>
                <td class="text-center <?= $couleurPerformance ?>"><?= $performance ?></td>
                <td class="text-center <?= $couleurAccessibilite ?>"><?= $accessibilite ?></td>
                <td class="text-center <?= $couleurSeo ?>"><?= $seo ?></td>
                <td class="text-center <?= $couleurBp ?>"><?= $bp ?></td>
            </tr>
            <?php
        }

        ?>

        </tbody>
    </table>
    </body>
    </html>


<?php $contenu = ob_get_clean();
//on vire le début de l'url
$pieces = explode("://", $_SERVER['argv'][1], 2);
$fichierRapport = fopen($dossierRoutine . '/' . date('Y-m-d-G-i') . "_" . urlencode($pieces[1]) . "_" . "rapport.html", 'w');
fwrite($fichierRapport, $contenu);
fclose($fichierRapport);