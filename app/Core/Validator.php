<?php

namespace App\Core;

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = trim($data[$field] ?? '');
            $ruleList = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;

            foreach ($ruleList as $rule) {
                if ($rule === 'required' && $value === '') {
                    $this->addError($field, "Field {$field} wajib diisi.");
                } elseif ($rule === 'email' && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "Format email pada {$field} tidak valid.");
                } elseif (str_starts_with($rule, 'min:')) {
                    $min = (int)substr($rule, 4);
                    if (mb_strlen($value) < $min) {
                        $this->addError($field, "Field {$field} minimal {$min} karakter.");
                    }
                } elseif (str_starts_with($rule, 'max:')) {
                    $max = (int)substr($rule, 4);
                    if (mb_strlen($value) > $max) {
                        $this->addError($field, "Field {$field} maksimal {$max} karakter.");
                    }
                } elseif ($rule === 'phone') {
                    if ($value !== '' && !preg_match('/^(\+?62|0)8[1-9][0-9]{6,11}$/', $value)) {
                        $this->addError($field, "Format nomor WhatsApp tidak valid.");
                    }
                }
            }
        }

        return empty($this->errors);
    }

    public function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFirstErrors(): array
    {
        $first = [];
        foreach ($this->errors as $field => $msgs) {
            $first[$field] = $msgs[0] ?? '';
        }
        return $first;
    }
}
