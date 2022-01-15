<?php declare(strict_types=1);

namespace App\Lib\HTTP;

class Response
{
    private $status = 200;
    private static $ResStatus = 200;
    public static $rootURL = WEB_HOST;

    public function __construct() {}

    public static function setStatus(int $code)
    {
        self::$ResStatus = $code;
        return true;
    }
    public static function getStatus()
    {
        return self::$ResStatus;
    }


    public function status(int $code)
    {
        $this->status = $code;
        return $this;
    }

    public function showStatus()
    {
        return $this->status;
    }

    public function toJSON($data=[])
    {
        http_response_code($this->status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /***
     * Redirect to the given root/slug url location
     * @param string|null $root  Root url default to the server host domain, example https://google.com
     * @param string $slug      Additional slug url: defaults to '/' example https://google.com/maps
     * @return string   Returns a Javascript script with window.location.href of the target url
     */
    public static function redirect(?string $root = null, string $slug = '/'): string
    {
        if (!$root) {
            $root = self::$rootURL;
        }
        if (!str_starts_with($slug, '/')) {
            $slug = '/' . $slug;
        }
        return <<<REDIRECT
        <script type="text/javascript">
            window.location.href = '$root$slug' ;
        </script>
        REDIRECT;
    }
}