<?php

namespace App\Model;

class Project
{
    /**
     * @var array
     */
    public $_data;
    
    public function __construct($data)
    {
        $this->_data = $data;
    }

    /**
     * @return int
     */
    public function getId()
    {
        //Its not right to have something like this, we cant be sure that 'id' key exists
        // So we need to have properties defined, with getters and setters
        return (int) $this->_data['id'];
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->_data);
    }
}
