<?php
function tiff2pdf($file_tif, $file_pdf){
    // Initialize
    $errors     = array();
    $cmd_ps2pdf = "/usr/bin/ps2pdfwr";
    $file_tif   = escapeshellarg($file_tif);
    $file_pdf   = escapeshellarg($file_pdf);

    // Initial Error handling
    if (!file_exists($file_tif)) $errors[] = "Original TIFF file:".$file_tif." does not exist";
    if (!file_exists($cmd_ps2pdf)) $errors[] = "Ghostscript PostScript to PDF converter not found at: ".$cmd_ps2pdf;
    if (!extension_loaded("imagick")) $errors[] = "Imagick extension not installed or not loaded";
    // to include the imagick extension dynamically use an optional:

    dl('imagick.so');
    // Only continue if there aren't any errors
    if (!count($errors)) {
        // Determine the file base
        $base = $file_pdf;
        if(($ext = strrchr($file_pdf, '.')) !== false) $base = substr($file_pdf, 0, -strlen($ext));

        // Determine the temporary .ps filepath
        $file_ps = $base.".ps";

        // Open the original .tiff
        $document = new Imagick($file_tif);

        // Use Imagick to write multiple pages to 1 .ps file
        if (!$document->writeImages($file_ps, true)) {
            $errors[] = "Unable to use Imagick to write multiple pages to 1  .ps file: ".$file_ps;
        } else {
            $document->clear();
            // Use ghostscript to convert .ps -> .pdf
            exec($cmd_ps2pdf." -sPAPERSIZE=a4 ".$file_ps." ".$file_pdf, $o, $r);

            if ($r) {
                $errors[] = "Unable to use ghostscript to convert .ps(".$file_ps.") -> .pdf(".$file_pdf."). Check rights. ";
            }
        }
    }

    // return array with errors, or true with success.
    if (!count($errors)) {
        return true;
    } else {
        return $errors;
    }
}
?>