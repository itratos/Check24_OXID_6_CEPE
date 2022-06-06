<?php


namespace Itratos\Check24Connector\Application\Model;

class OpentransSimplexml extends \SimpleXMLElement
{

    /**
     * Returns field value as string
     *
     * @return string
     */
    public function as_string($field, $mandatory_field = true)
    {
        return $this->encode((string)trim($this->get_field($field, $mandatory_field)));
    }

    /**
     * Returns field value as html_entity_decode string
     *
     * @return string
     */
    public function as_decoded_string($field, $mandatory_field = true)
    {
        return html_entity_decode(htmlspecialchars_decode($this->as_string($field, $mandatory_field)), ENT_COMPAT, RS_CHARSET);
    }

    /**
     * Returns field value as integer
     *
     * @return integer
     */
    public function as_int($field, $mandatory_field = true)
    {
        return (int)trim($this->get_field($field, $mandatory_field));
    }

    /**
     * Returns field value as float
     *
     * @return float
     */
    public function as_float($field, $mandatory_field = true)
    {
        return (float)$this->get_field($field, $mandatory_field);
    }

    /**
     * Returns field value as boolean
     *
     * @return boolean
     */
    public function as_bool($field, $mandatory_field = true)
    {
        return (bool)$this->get_field($field, $mandatory_field);
    }

    /**
     * Returns data as html_entity_decode string
     *
     * @param string $data
     * @return string
     */
    public function encode($data)
    {

        $data = html_entity_decode(htmlspecialchars_decode($data), ENT_COMPAT/*, RS_CHARSET*/);

        return $data;
    }

    /**
     * Returns field
     *
     * @return mixed
     */
    public function get_field($field, $mandatory_field = true)
    {
        if ($this->$field) {
            return $this->$field;
        } elseif ($this->{$field}) {
            return $this->{$field};
        } elseif ($this[$field]) {
            return $this[$field];
        } elseif (!$mandatory_field) {
            return NULL;
        } else {
            throw new OpentransException('Required field "' . $field . '" not found in sourcefile.');
        }
    }

    /**
     * Returns attribute vaklue of field
     *
     * @param object $field
     * @param string $attribute
     * @return mixed
     */
    public function get_field_attribute($field, $attribute)
    {
        if ($this->{$field}) {
            $attributes = $this->{$field}->attributes();
        } else if ($this[$field]) {
            $attributes = $this[$field]->attributes();
        } else {
            return NULL;
        }

        if (count($attributes) > 0) {

            if (isset($attributes[$attribute])) {
                return (string)$attributes[$attribute][0];
            } else {
                return NULL;
            }

        } else {
            return NULL;
        }
    }

    /**
     * Verify existence of field
     *
     * @param string $field
     * @return boolean
     */
    public function field_exists($field)
    {
        return array_key_exists($field, get_object_vars($this));
    }

}