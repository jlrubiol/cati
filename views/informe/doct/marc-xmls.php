<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<collection xmlns="http://www.loc.gov/MARC21/slim">
    <?php
    foreach ($estudios as $estudio) {
        echo $this->render('_marc-xml', [
            'anyo' => $anyo,
            'estudio' => $estudio,
        ]);
    }
    ?>
</collection>