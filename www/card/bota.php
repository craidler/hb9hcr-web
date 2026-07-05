<?php

namespace HB9HCR;

Bota::$config = Util::mergeJson(__DIR__ . '/../../config/default.json', __DIR__ . '/../../config/local.json')->bota;

$stats = (object)[
    'activator' => Bota::read(['endpoint' => '/stats/activations/activator']),
    'hunter' => Bota::read(['endpoint' => '/stats/activations/hunter']),
];
?>
<div class="card-header">
    <h2 class="card-title">Bunkers On The Air</h2>
</div>
<div class="card-body">
    <div class="row">
        <div class="col-6">
            <figure class="dropshadow">
                <picture>
                    <?= file_get_contents(__DIR__ . '/../image/HB9HCR-Bota.White.svg') ?>
                </picture>
            </figure>
        </div>
        <div class="col-6">
            <p class="mt-3">Activating Swiss bunkers for <a href="https://www.wwbota.net" target="_blank">WWBOTA</a> and <a href="https://wwbota.net/hbbota/" target="_blank">HBBOTA</a> has become one of my <strong>favorite radio activities</strong>.</p>
            <figure class="dropshadow text-center my-4">
                <picture>
                <?= file_get_contents(__DIR__ . '/../image/HB9HCR-Sargeant.White.svg') ?>
                </picture>
            </figure>
            <p>The few years where I served for the Swiss Armed Forces gave me a deep appreciation for these historical sites.</p>
        </div>
    </div>
</div>
<div class="card-body">
    <div class="callout mt-0">I will <strong>gladly QSL all hunters</strong> in a pile-up and am always hunting for <strong>bunker-to-bunker contacts</strong> to boost my own score too. In <strong>CW please allow me some farnsworth</strong>, as I am a beginner.</div>
    <h5 class="text-end">Regional Historic Background</h5>
    <p>Within a <strong>10 km radius</strong> of where I live, there are about <strong>180 relevant objects</strong> alone as it lays in a natural choke point. The region is called <strong>Sperrstelle Urdorf</strong> and was the most important installation of a bigger, nationally significant constellation called <strong>Limmatstellung</strong>.</p>
    <div class="row">
        <div class="col-6 pt-3">
            <p>Its purpose was to <strong>prevent enemy ground advances</strong> towards the <strong>Reusstal</strong> and ultimately to the <strong>Gotthard Massiv</strong> in which direction the swiss population would have retreat to in case of an invasion.</p>
            <p>The <strong>Sperrstelle Urdorf</strong> consists of a 3km wide tank obstacle crossing our backyards, many machine gun nests, observation,  infantry cannon stands and bunkers.</p>
        </div>
        <div class="col-6">
            <figure class="polaroid grayscale-5">
                <picture>
                    <img src="/image/HB9HCR-Urdorf.jpg" alt="Sperrstelle Urdorf">
                </picture>
                <figcaption>
                    Sperrstelle Urdorf
                </figcaption>
            </figure>
        </div>
    </div>
    <p class="pt-3">During cold war times, additional <strong>15 nuclear shelters</strong> had been added to the region.</p>
</div>
<div class="card-body">
    <h5>My Stats</h3>
    <div class="row">
        <div class="col-6">
            <table class="table caption-top">
                <caption>Activator</caption>
                <thead>
                    <tr>
                        <th class="shrink">Month</th>
                        <th>Bunkers</th>
                        <th>Hunters</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats->activator->monthly_activations as $y => $months): ?>
                        <?php foreach ($months as $m => $s): ?>
                            <tr>
                                <td><?= sprintf('%d %s', $y, str_pad($m, 2, '0', STR_PAD_LEFT)) ?></td>
                                <td class="text-end"><?= $s->bunkers ?></td>
                                <td class="text-end"><?= $s->hunters ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td></td>
                        <td class="text-end"><?= $stats->activator->lifetime_activations->bunkers ?></td>
                        <td class="text-end"><?= $stats->activator->lifetime_activations->hunters ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="col-6">
            <table class="table caption-top">
                <caption>Hunter</caption>
                <thead>
                    <tr>
                        <th class="shrink">Month</th>
                        <th>Bunkers</th>
                        <th>Activators</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats->hunter->monthly_activations as $y => $months): ?>
                        <?php foreach ($months as $m => $s): ?>
                            <tr>
                                <td><?= sprintf('%d %s', $y, str_pad($m, 2, '0', STR_PAD_LEFT)) ?></td>
                                <td class="text-end"><?= $s->bunkers ?></td>
                                <td class="text-end"><?= $s->activators ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td></td>
                        <td class="text-end"><?= $stats->hunter->lifetime_activations->bunkers ?></td>
                        <td class="text-end"><?= $stats->hunter->lifetime_activations->activators ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>