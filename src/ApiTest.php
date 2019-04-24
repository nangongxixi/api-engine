<?php

namespace j\apiSimple;

use Exception;
use FilesystemIterator;

use j\log\Log;
use j\net\http\Client;
use j\tool\ArrayUtils;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use SplFileInfo;

/**
 * Class ApiTest
 * @package wxapp\lib
 */
class ApiTest
{

    /**
     * @var string
     */
    protected $apiGatewayUrl;
    protected $testConfPath;
    protected $apis;
    protected $charset = 'utf-8';

    /**
     * ApiTest constructor.
     * @param string $apiGatewayUrl example: 'http://your_host/api.php?api=%s&charset=%s&'
     * @param $testConfPath
     */
    public function __construct($testConfPath, $apiGatewayUrl)
    {
        $this->apiGatewayUrl = $apiGatewayUrl;
        $this->testConfPath = $testConfPath;
    }


    /**
     * @param $path
     * @param string $pattern The regular expression to match.
     * @param bool $recursion
     * @return RegexIterator|SplFileInfo[]
     */
    function getFiles($path, $pattern, $recursion = false)
    {
        if ($recursion) {
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        } else {
            $it = new FilesystemIterator($path);
        }
        return new RegexIterator($it, $pattern);
    }

    /**
     * @return array
     */
    function getApis()
    {
        if (isset($this->apis)) {
            return $this->apis;
        }

        $data = [];
        $files = $this->getFiles($this->testConfPath, '/\.php$/');
        foreach ($files as $file) {
            $define = include($file);
            if(isset($define['multi'])){
                $data += $define['api'];
            } else {
                $key = $file->getBasename('.php');
                $data[$key] = $define;
            }
        }

        krsort($data);
        return $this->apis = $data;
    }

    function getDoc($api)
    {
        $apis = $this->getApis();
        if (!isset($apis[$api])) {
            return '无';
        }
        $conf = $apis[$api];
        return isset($conf['doc']) ? var_export($conf['doc'], true) : '无';
    }

    /**
     * @param $api
     * @return string
     * @throws Exception
     */
    function callApi($api)
    {
        $apis = $this->getApis();
        if (!isset($apis[$api])) {
            throw new Exception("Not found api({$api})");
        }

        $conf = $apis[$api];
        $query = ArrayUtils::gav($conf, 'query');

        $http = new Client;
        $http->setLogger(new Log());

        $apiUrl = sprintf($this->apiGatewayUrl, $api, $this->charset);
        if ($data = ArrayUtils::gav($conf, 'post')) {
            $isPost = true;
            $rs = $http->post($apiUrl, $data);
        } else {
            $isPost = false;
            $rs = $http->get($apiUrl, $query);
        }

        echo ($isPost ? "POST" : 'GET') . "\n";
        echo $apis[$api]['name'] . " - {$api} - 参数:\n";
        echo $this->getDoc($api);
        echo "\n\n";

        if ($callback = ArrayUtils::gav($conf, 'test')) {
            echo "运行测试用例:\n";
            $data = json_decode($rs, true);
            $test = call_user_func($callback, $data['code'], $data['code'] == 200 ? $data['data'] : [], $rs);
            if ($test !== true) {
                echo "测试失败\n";
            } else {
                echo "测试通过\n";
            }
            echo "\n";
        }

        return $this->jsonIndent($rs);
    }

    /**
     * @param $json
     * @return string
     */
    function jsonIndent($json)
    {
        if (is_object($json)) {
            $json = json_encode($json);
        }

        $result = '';
        $pos = 0;
        $strLen = strlen($json);
        $newLine = "\r\n";
        $indentStr = '  ';
        $prevChar = '';
        $outOfQuotes = true;

        for ($i = 0; $i < $strLen; $i++) {
            // Grab the next character in the string.
            $char = substr($json, $i, 1);
            // Are we inside a quoted string?
            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;
                // If this character is the end of an element,
                // output a new line and indent the next line.
            } else if (($char == '}' || $char == ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos--;
                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }
            // Add the character to the result string.
            $result .= $char;
            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos++;
                }
                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }
            $prevChar = $char;
        }

        return $result;
    }
}