<?php

namespace HB9HCR;



?>
<div class="card-header">
    <h2 class="card-title">Qsl Card</h2>
</div>
<div class="card-body p-4 pb-0">
    <p>Did we have a QSO? Awesome - it should be in my logs then! If so, you may be able to <strong>generate your very own, unique, digital QSL card</strong> with the form below. Simply type your callsign and hit &lt;generate&gt;:</p>
</div>
<iframe src="/frame/qsl.php?v=<?= time() ?>" height="1196px" scrolling="no" sandbox="allow-forms allow-scripts allow-same-origin allow-popups allow-downloads"></iframe>
