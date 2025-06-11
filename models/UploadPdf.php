<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadPdf extends Model
{
    /**
     * @var UploadedFile
     */
    public $pdfFile;

    public $uploadErrorMessages = [
        0 => 'There is no error, the file uploaded with success.',
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
        3 => 'The uploaded file was only partially uploaded.',
        4 => 'No file was uploaded.',
        6 => 'Missing a temporary folder.',
        7 => 'Failed to write file to disk.',
        8 => 'A PHP extension stopped the file upload.',
    ];

    public function getErrorMessage()
    {
        return $this->uploadErrorMessages[$this->pdfFile->error];
    }

    private function returnBytes($val)
    {
        assert('1 === preg_match("/^\d+([kmg])?$/i", $val)');
        static $map = ['k' => 1024, 'm' => 1048576, 'g' => 1073741824];
        return (int)$val * @($map[strtolower(substr($val, -1))] ?: 1);
    }

    public function rules()
    {
        return [
            [
                ['pdfFile'],
                'file',
                'skipOnEmpty' => false,
                'extensions' => 'pdf',
                'mimeTypes' => 'application/pdf',
                'maxSize' => $this->returnBytes(ini_get('upload_max_filesize')),
            ],
        ];
    }

    public function upload($directorio, $nombre)
    {
        if ($this->validate()) {
            $this->pdfFile->saveAs("pdf/{$directorio}/{$nombre}");

            return true;
        }

        return false;
    }

    public function attributeLabels()
    {
        return [
            'pdfFile' => Yii::t('models', 'Fichero PDF'),
        ];
    }

    public function getSlug()
    {
        // Remove temporaly the file extension (all dots will be removed)
        $slug = pathinfo($this->pdfFile->name, PATHINFO_FILENAME);

        // Convert to lowercase
        $slug = mb_strtolower($slug);

        // Replace letters with accents by their non-accented counterparts.
        // I do not consider non-latin alphabets, which should be transliterated.
        $accents = '/&([a-z]{1,2})(grave|acute|circ|tilde|uml|ring|lig|cedil|slash|caron);/';
        $slug = preg_replace($accents, '$1', htmlentities($slug));
        $slug = strtr($slug, ['ž' => 'z', 'þ' => 'th', 'ð' => 'dh']);

        // Replace spaces by dashes
        $slug = strtr($slug, [' ' => '-']);

        // Remove non alphanumeric, hyphen, or underscore characters
        $slug = preg_replace('/[^a-z0-9\-\_]/', '', $slug);

        // Replace multiple dashes by a single one
        $slug = preg_replace('/[\-]+/', '-', $slug);

        return "$slug.pdf";
    }
}
