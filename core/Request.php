<?php

namespace Core;

use Core\Validator;
use JetBrains\PhpStorm\NoReturn;

class Request
{
    protected array $data;
    protected array $server;
    protected array $parameters;

    public function __construct(array $data, array $server, array $parameters = [])
    {
        $this->data = $data + $parameters; // Merge URL parameters with data
        $this->server = $server;
        $this->parameters = $parameters;
    }

    public function all(): array
    {
        return $this->data;
    }

    public function input($key, $default = null)
    {
        return $this->data[$key] ?? $_GET[$key] ?? $_POST[$key] ?? $default;
    }

    public function validate($data): void
    {
        Validator::set_data($this->data);
        $errors = [];
        foreach ($data as $key => $value) {
            try {
                $field = isset($this->data[$key]) ? $this->data[$key] : null;
                if (is_string($field)) {
                    $field = trim($field);
                }
            } catch (\Exception $e) {
                $field = null;
            }

            is_array($value) ?: $value = explode('|', $value);
            $rules = $value;

            foreach ($rules as $rule) {
                $rule = explode(':', $rule);
                $rule_name = $rule[0];
                $rule_value = $rule[1] ?? null;
                if (!empty($rule_value)) {
                    $rule_value = strpos($rule_value, ',') ? explode(',', $rule_value) : $rule_value;
                }
                try {
                    $result = Validator::$rule_name($field, $rule_value);
                } catch (\Exception $e) {
                    $result = $e->getMessage();
                }

                if ($result) {
                    $errors[$key] = "{$result}";
                }
            }
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old', $this->data);
            redirect((new Router)->previousUrl());
        }
        return;
    }

    public function error($field, $error): void
    {
        Session::flash('errors', [$field => [$error]]);
        Session::flash('old', $this->data);
        redirect((new Router)->previousUrl());
    }
}