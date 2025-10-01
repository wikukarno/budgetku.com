<?php

namespace App\Http\Requests\Traits;

trait InputSanitizerTrait
{
    /**
     * Prepare the data for validation.
     * This method is called automatically by Laravel before validation
     */
    protected function prepareForValidation()
    {
        $sanitizedData = [];

        foreach ($this->all() as $key => $value) {
            $sanitizedData[$key] = $this->sanitizeInput($value);
        }

        $this->replace($sanitizedData);
    }

    /**
     * Sanitize input data
     *
     * @param mixed $value
     * @return mixed
     */
    private function sanitizeInput($value)
    {
        if (is_string($value)) {
            // Remove unnecessary whitespace
            $value = trim($value);
            
            // Remove multiple consecutive spaces
            $value = preg_replace('/\s+/', ' ', $value);
            
            // Remove potentially dangerous characters but keep normal punctuation
            $value = preg_replace('/[<>]/', '', $value);
            
            // Convert empty strings to null for proper validation
            return $value === '' ? null : $value;
        }

        if (is_array($value)) {
            return array_map([$this, 'sanitizeInput'], $value);
        }

        return $value;
    }

}