<?php

$links = [
    'manual' => [
        ['https://www.yaesu.com/Files/DFDF4D53-1B78-7375-E6C7A6E4B192BE13/FT-70DR_DE_OM_ENG_EH051M202_1809R-CY-1.pdf', 'Yaesu FT-70D'],
        ['https://www.yaesu.com/Files/DFDF4D53-1B78-7375-E6C7A6E4B192BE13/FT-70DR_DE_AM_ENG_1907-F.pdf', 'Yaesu FT-70D Advanced'],
        ['https://www.yaesu.com/Files/4CB893D7-1018-01AF-FA97E9E9AD48B50C/FT-857D_OM_ENG_EH007M108.pdf', 'Yaesu FT-857D'],
    ],
    'organisation' => [
        ['https://www.ariss.org', 'ARISS'],
        ['https://www.fhnw.ch', 'FHNW'],
        ['https://www.hb9hd.ch', 'HB9HD'],
        ['https://www.hb9uf.ch', 'HB9UF'],
        ['https://www.uska.ch', 'USKA'],
    ],
    'project' => [
        ['https://3dwarehouse.sketchup.com/model/03abd2a6-aa6a-40a0-a7b6-b0c2bcca92fb/Drawers-for-HDJ80', 'Drawer for Toyota HDJ80'],
        ['https://github.com/craidler/hb9hcr-sattrack', 'SatTrack'],
        ['https://dk7zb.darc.de/PVC-Yagis/4-Ele-2m.htm', 'Yagi VHF 100cm Boom'],
        ['https://dk7zb.darc.de/PVC-Yagis/7-Ele-70cm.htm', 'Yagi UHF 100cm Boom'],
    ],
    'tool' => [
        ['https://hams.at/passes', 'HAMSAT Passes'],
        ['https://www.qrz.com/db/HB9HCR', 'QRZ HB9HCR'],
    ],
];

?>
<html lang="EN">
    <head>
        <title>HB9HCR</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="img/favicon.svg" sizes="any" type="image/svg+xml">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="hb9hcr.css">
    </head>
    <body>
        <div class="container-fluid my-3 text-center">
            <h1>
                HB9
                <img src="img/international-amateur-radio-symbol.svg" class="align-text-bottom" style="height: 50px">
                HCR
            </h1>
            <p class="font-monospace text-muted mt-3">
                member of <a href="https://www.hb9uf.ch" target="_blank">HB9UF</a> - UHF group of <a href="https://uska.ch" target="_blank">USKA</a>
                <br>
                QTH JN47fj
            </p>
        </div>
        <div class="container font-monospace">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <span class="card-title">HB9HCR Tracker</span>
                        </div>
                        <div class="card-body">
                            <a href="https://github.com/craidler/hb9hcr-sattrack" target="_blank">
                                <img src="img/HB9HCR.Sattrack.jpg" alt="HB9HCR Satellite Tracker" class="img-fluid">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <span class="card-title">HB9HCR Feeder</span>
                        </div>
                        <div class="card-body">
                                <img src="img/HB9HCR.Feeder.jpg" alt="HB9HCR Feeder" class="img-fluid">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 d-none">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <span class="card-title">Please adhere</span>
                        </div>
                        <div class="card-body">
                            <a href="https://www.nato.int/en/news-and-events/articles/news/2017/12/21/nato-phonetic-alphabet-codes-and-signals" target="_blank">
                                <img src="https://www.nato.int/content/dam/nato/webready/news/2010-2019/2017/12/21/20180110_alphabet-sign-signal-big2.jpg/jcr:content/renditions/cq5dam.web.1280.1280.jpeg" alt="NATO phonetic alphabet, codes and signals" class="img-fluid">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container font-monospace">
            <div class="row">
                <?php foreach ($links as $label => $urls): ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header">
                                <span class="card-title"><?= ucfirst($label) ?></span>
                            </div>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($urls as $link): ?>
                                    <li class="list-group-item">
                                        <a href="<?= $link[0] ?>" target="_blank"><?= $link[1] ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="card-footer"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </body>
</html>
