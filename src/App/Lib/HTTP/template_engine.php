<?php declare(strict_types = 1);

namespace App\Lib\HTTP;
/**
 *
 * @var string VIEWS /resources/views
 */
function render($contentFile, $variables = array())
{
    $fileRender = VIEWS . "/" . $contentFile;

    // Create Variables in Scope
    if (count($variables) > 0) {
        foreach ($variables as $key => $value) {
            if (strlen($key) > 0) {
                ${$key} = $value;
            }
        }
    }
    // TODO: make some const
//    ob_start();
    echo "Ciao";
    echo "Ciao";
    echo "Ciao";
    echo var_dump(VIEWS); echo  '<br/>';
    echo var_dump(WEB_HOST); echo  '<br/>';
    exit;
    require_once(VIEWS . "/header.php");
    require_once(VIEWS . "/navbar.php");


    echo '<div id="layout">';

    if (file_exists($fileRender)) {
        require_once($fileRender);
    } else {
        /*
            If the file isn't found the error can be handled in lots of ways.
            In this case we will just include an error template.
        */
        $error_code = 404;
        require_once(VIEWS . "/errors/404.php");
    }


    // close container div
    echo "</div>\n";
    require_once(VIEWS . "/footer.php");
    echo "</body> </html>";
//    ob_end_flush();
    exit;
}