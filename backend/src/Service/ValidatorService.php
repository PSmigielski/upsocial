<?php
namespace App\Service;

use App\Exception\ApiException;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Validator;


class ValidatorService
{
    public function validateSchema(string $pathToSchema, object $data)
    {
        $schema = file_get_contents(__DIR__ . $pathToSchema);
        $validator = new Validator();
        $result = $validator->validate($data, $schema);
        if (!$result->isValid()) {
            $formatter = new ErrorFormatter();
            $error = $formatter->formatOutput($result->error(), "basic");
            if(!empty($result->error()->subErrors())){
                $data = $this->getFieldAndKeyword($error["errors"][1]);
                [$field, $keyword] = $data;
                $this->getErrorMessage($field, $keyword);
            }else{
                switch($result->error()->keyword()){
                    case "required":
                        $this->getErrorMessage($result->error()->keyword(), $result->error()->args()["missing"][0],);
                    break;
                    case "minProperties":
                        $this->getErrorMessage($result->error()->keyword(), $result->error()->args()["min"]);
                    break;
                    default:
                        $this->getErrorMessage($result->error()->keyword());
                    break;
                }
            }
        }
    }
    private function getFieldAndKeyword(array $result){
        preg_match( "/#\/properties\/(?P<field>\w+)\/(?P<keyword>\w+)/", $result["keywordLocation"], $match, PREG_OFFSET_CAPTURE);
        $field = $match["field"][0];
        $keyword = $match["keyword"][0];
        return ["field" => $field,"keyword" => $keyword];
    }
    private function getErrorMessage(string $keyword, string $field = null)
    {
        switch ($keyword) {
            case "pattern":
                throw new ApiException([
                    "message" => "invalid ".$field." given",
                    "field" => $field,
                    "code" => "fieldInvalidPattern"
                ], 400);
                break;
            case "enum":
                throw new ApiException([
                    "message" => "expected male or female",
                    "field" => $field,
                    "code" => "fieldInvalidGender"
                ], 400);
                break;
            case "format":
                throw new ApiException([
                    "message" =>"invalid ".$field." format",
                    "field" => $field,
                    "code" => "fieldInvalidFormat"
                ], 400);
                break;
            case "type":
                throw new ApiException([
                    "message" => $field." has invalid type",
                    "field" => $field,
                    "code" => "fieldInvalidType"
                ], 400);
                break;
            case "maxLength":
                throw new ApiException([
                    "message" => $field." is too long",
                    "field" => $field,
                    "code" => "fieldTooLong"
                ], 400);
                break;
            case "minLength":
                throw new ApiException([
                    "message" => $field." is too short",
                    "field" => $field,
                    "code" => "fieldTooShort"
                ], 400);
                break;
            case "required":
                throw new ApiException([
                    "message" => $field." is missing",
                    "field" => $field,
                    "code" => "fieldRequired"
                ], 400);
                break;
            case "minProperties":
                throw new ApiException([
                    "message" => "you have to pass minimum ".$field." propertiy/es",
                    "code" => "notEnoughProperties"
                ], 400);
                break;
            case "additionalProperties":
                throw new ApiException([
                    "message" => "no additional properties allowed",
                    "code" => "forbiddenProperties"
                ], 400);
                break;
        }
    }
}