<?php
/**
 * Fills and flattens PDFs, without requiring PDFtK
 */

namespace TemplateifyPdf;

require_once "vendor/autoload.php";

use Exception;
use Zend_Pdf;

/**
 *
 */
class Templateifier {

    /**
     * Adds the .PDF extension to the filepath, if not already given.
     * @param $pdfName string The filepath or file
     * @return string The new filepath or file, with the .PDF extension
     */
    private static function correctPdfName(string $pdfName): string
    {
        $pos = strpos(strtolower($pdfName), ".pdf");
        if($pos === false) {
            $pdfName .= ".pdf";
        }
        return $pdfName;
    }

    /**
     * Takes a template PDF file and data, and produces filled PDF files. Optionally, these PDFs can also
     * be flattened to prevent further edits. The fields array contains key - value pairs, where the key
     * refers to the name of the form field. One caveat is that the template PDF must be of version 1.4
     * (Acrobat 5.x) or lower. For information on how to create and convert PDFs to make them compatible,
     * please see the repository's README file.
     *
     * @param $pdfFile string The path to the template PDF file
     * @param $outputFile string The path where the output PDF will be created
     * @param $flatten bool True, if the outputted document should be flattened
     * @param $fields array The fields and data. The array should be configured as such <pre>array('field' => 'output', ...);</pre>
     * @param $result Exception The result of the function call. If successful, will contain the path to the resulting PDF. If there is an error, the exception is provided.
     */
    public static function templateify(string $pdfFile, string $outputFile, bool $flatten, array $fields, &$result = "") {
        try {
            $pdf = Zend_Pdf::load(Templateifier::correctPdfName($pdfFile));

            foreach ($fields as $key => $value) {
                $pdf->setTextField($key, $value);
                if($flatten) {
                    $pdf->markTextFieldAsReadOnly($key);
                }
            }

            $outputFile = Templateifier::correctPdfName($outputFile);
            $pdf->save($outputFile);
            $result = $outputFile;
        } catch (Exception $e) {
            $result = $e;
        }
    }
}




