<?php
use app\models\InformePublicado;
use yii\helpers\Url;
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<collection xmlns="http://www.loc.gov/MARC21/slim">
  <record>

    <datafield tag="037" ind1=" " ind2=" ">
        <subfield code="a">CALTITU_INFCALDOCT-<?php echo $anyo + 1; ?></subfield>
    </datafield>

    <datafield tag="041" ind1=" " ind2=" ">
        <subfield code="a">spa</subfield>
    </datafield>

    <datafield tag="245" ind1="0" ind2="0">
        <subfield code="a">Informe de la calidad de los Estudios de Doctorado curso <?php echo $anyo; ?>/<?php echo $anyo + 1; ?></subfield>
    </datafield>

    <datafield tag="260" ind1=" " ind2=" ">
        <subfield code="a">Zaragoza</subfield>
        <subfield code="b">Universidad de Zaragoza</subfield>
        <subfield code="c"><?php echo $anyo; ?>-<?php echo $anyo + 1; ?></subfield>
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

    <datafield tag="FFT" ind1=" " ind2=" ">
        <subfield code="a"><?php echo Url::home(true); ?>pdf/informes/<?php echo $anyo; ?>/iced-es-v<?php echo InformePublicado::MAX_VERSION_INFORME_ICED; ?>.pdf</subfield>
        <subfield code="d">Informe de la calidad de los Estudios de Doctorado curso <?php echo $anyo; ?>/<?php echo $anyo + 1; ?></subfield>
    </datafield>

    <datafield tag="970" ind1=" " ind2=" ">
        <subfield code="a">CALTITU_INFCALDOCT-<?php echo $anyo + 1; ?></subfield>
    </datafield>

    <datafield tag="980" ind1=" " ind2=" ">
        <subfield code="a">CALTITU_INFCALDOCT</subfield>
    </datafield>

  </record>
</collection>
