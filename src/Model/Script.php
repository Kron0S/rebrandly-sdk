<?php

namespace Rebrandly\Model;

/**
 * Script Model
 *
 * Stores and recalls information about a Rebrandly as described at
 * https://developers.rebrandly.com/docs/branded-script-model
 */
class Script
{
    /**
     * @var string Unique ID, regularly used as a lookup key for a script
     */
    private $id;

    /**
     * @var string script name
     */
    private $name;

    /**
     * @var string value
     */
    private $value;

    /**
     * @var string uri
     */
    private $uri;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return DateTime
     */
    public function getUri()
    {
        return $this->uri;
    }


    public function setValue($value)
    {
        if (!is_string($value)) {
            $type = gettype($value);
            $errorText = printf('Expected value to be string, %s supplied', $type);
            throw new \InvalidArgumentException($errorText);
        }
        $this->value = $value;

        return $this;
    }
    public function setName($name)
    {
        if (!is_string($name)) {
            $type = gettype($name);
            $errorText = printf('Expected name to be string, %s supplied', $type);
            throw new \InvalidArgumentException($errorText);
        }
        $this->name = $name;

        return $this;
    }


    /**
     * Turns a script-like scriptArray into an actual script object
     *
     * @param array $scriptArray An array containing all data to be assigned into the
     *    script
     *
     * @return void
     */
    static function import($scriptArray)
    {
        $script = new Script;

        foreach ((array)$scriptArray as $key => $value) {
            $script->$key = $value;
        }

        return $script;
    }

    /**
     * Turns a script into a script-like array
     *
     * @return array $script An array with all properties from the script
     */
    public function export()
    {
        $exportFields = [
            'id', 'name', 'value', 'uri'
        ];

        $scriptArray= [];
        foreach ($exportFields as $field) {
            $value = $this->$field;
            if ($value) {
                $scriptArray[$field] = $value;
            }
        }
        return $scriptArray;
    }
}
