<?php
use app\models\PlanPublicado;
use yii\helpers\Url;

?>
<record>

    <datafield tag="037" ind1=" " ind2=" ">
        <subfield code="a">CALTITU_PLANMEJORA-<?php echo $anyo + 1; ?>-<?php echo $estudio->id_nk; ?></subfield>
    </datafield>

    <datafield tag="041" ind1=" " ind2=" ">
        <subfield code="a">spa</subfield>
    </datafield>

    <datafield tag="245" ind1="0" ind2="0">
        <subfield code="9"><?php echo $estudio->id_nk; ?></subfield>
        <subfield code="a">Plan Anual de Innovación y Mejora del
            <?php echo $estudio->nombre; ?> (curso <?php echo $anyo; ?>-<?php echo $anyo + 1; ?>)</subfield>
    </datafield>

    <datafield tag="260" ind1=" " ind2=" ">
        <subfield code="a">Zaragoza</subfield>
        <subfield code="b">Universidad de Zaragoza</subfield>
        <subfield code="c"><?php echo $anyo; ?>-<?php echo $anyo + 1; ?></subfield>
    </datafield>

    <datafield tag="521" ind1=" " ind2=" ">
        <subfield code="9"><?php echo $estudio->id_nk; ?></subfield>
        <subfield code="a"><?php echo $estudio->nombre; ?></subfield>
    </datafield>

    <datafield tag="540" ind1=" " ind2=" ">
        <subfield code="a">by-nc-sa</subfield>
        <subfield code="b">Creative Commons</subfield>
        <subfield code="c">3.0</subfield>
        <subfield code="u">http://creativecommons.org/licenses/by-nc-sa/3.0/</subfield>
    </datafield>

    <datafield tag="710" ind1=" " ind2=" ">
        <subfield code="a">Universidad de Zaragoza</subfield>
    </datafield>

    <?php
    foreach ($estudio->plans as $plan) {
        ?>
        <datafield tag="830" ind1=" " ind2=" ">
            <subfield code="9"><?php echo $plan->id_nk; ?></subfield>
        </datafield>
        <?php
    }
    ?>

    <datafield tag="FFT" ind1=" " ind2=" ">
        <subfield code="a"><?php echo Url::home(true); ?>pdf/planes-mejora/<?php echo $anyo; ?>/plan-es-<?php echo $estudio->id_nk; ?>-v<?php echo PlanPublicado::MAX_VERSION_PLAN; ?>.pdf</subfield>
        <subfield code="d">Plan Anual de Innovación y Mejora</subfield>
    </datafield>

    <datafield tag="970" ind1=" " ind2=" ">
        <subfield code="a">CALTITU_PLANMEJORA-<?php echo $anyo + 1; ?>-<?php echo $estudio->id_nk; ?></subfield>
    </datafield>

    <?php
    foreach ($estudio->getCentros() as $centro) {
        ?>
        <datafield tag="980" ind1=" " ind2=" ">
            <subfield code="a">CALTITU_PLANMEJORA</subfield>
            <subfield code="b"><?php echo $estudio->rama->nombre; ?></subfield>
            <subfield code="c"><?php echo $centro->id; ?></subfield>
        </datafield>
        <?php
    }
    ?>

</record>
