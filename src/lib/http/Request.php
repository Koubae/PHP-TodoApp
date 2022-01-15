<?php declare(strict_types=1);

namespace App\Lib\HTTP;

class Request
{
    public $reqMatches;
    public $params;
    public $reqMethod;
    public $contentType;

    public function __construct(?array $params=[], ?array $reqMatches=[])
    {
        $this->params = $params;
        $this->reqMatches = $reqMatches;
        $this->reqMethod = trim($_SERVER['REQUEST_METHOD']);
        $this->contentType = !empty($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : '';
    }

    public function getBody()
    {
        if ($this->reqMethod !== 'POST') {
            return '';
        }

        $body = [];
        foreach ($_POST as $key => $value) {
            $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }
        return $body;
    }

    public function getJSON()
    {
        if (!$this->reqMethod !== 'POST') {
            return [];
        }

        if (strcasecmp($this->contentType, 'application/json') !== 0) {
            return 0;
        }

        // Raw Request post
        $content = trim(file_get_contents('php://input'));
        return json_decode($content);
    }
}
