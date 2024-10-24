<?php

namespace thm\tnt_ec\service\PricingService;

use thm\tnt_ec\service\AbstractResponse;

class PricingResponse extends AbstractResponse
{

    protected function catchRuntimeErrors()
    {

        if (isset($this->simpleXml->error_reason) === true) {
            $this->hasError = true;
            $this->errors[] = $this->simpleXml->error_reason->__toString();

            if (isset($this->simpleXml->error_line) === true) {
                $this->errors[] = "Line: {$this->simpleXml->error_line}";
            }

            if (empty($this->simpleXml->error_srcText->__toString()) === false) {
                $this->errors[] = $this->simpleXml->error_srcText->__toString();
            }
        }
    }

    /**
     * Catch validation errors
     *
     * @return void
     */
    protected function catchValidationErrors()
    {

        if (isset($this->simpleXml->errors->brokenRule) === false) {
            return null;
        }

        $this->hasError = true;

        foreach ($this->simpleXml->errors->brokenRule as $xml) {
            $error['code'] = $xml->code->__toString();
            $error['description'] = $xml->description->__toString();
            $error['messageType'] = $xml->messageType->__toString();

            array_push($this->errors, $error);
        }
    }

    protected function catchConcreteResponseError()
    {
        $this->validateXml();
        $this->catchRuntimeErrors();
        $this->catchValidationErrors();
    }
}