<?php
namespace Expection;
class JsonValidator
{
    public function __construct($jsonToValidate)
    {

        $callback = "<br/> Decoding: " . $jsonToValidate;

        $callback = "<br>Error: ";

        json_decode($jsonToValidate);

        switch (json_last_error()) 
        {
            case JSON_ERROR_NONE:
                $callback = $callback . " - No errors";
                break;
                case JSON_ERROR_DEPTH:
                $callback = $callback . " - Maximum stack depth exceeded";
                break;
                case JSON_ERROR_STATE_MISMATCH:
                $callback = $callback . " - Underflow or the modes mismatch";
                break;
                case JSON_ERROR_CTRL_CHAR:
                $callback = $callback . " - Unexpected control character found";
                break;
                case JSON_ERROR_SYNTAX:
                $callback = $callback . " - Syntax error, malformed JSON";
                break;
                case JSON_ERROR_UTF8:
                $callback = $callback . " - Malformed UTF-8 characters, possibly incorrectly encoded";
                break;
                default:
                $callback = $callback . " - Unknown error";
                break;
        }
        return $callback;
    }
}
?>