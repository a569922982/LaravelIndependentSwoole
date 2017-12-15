<?php
namespace Model;

use Prop\Exception\AuthFieldsException;

class Model extends \Illuminate\Database\Eloquent\Model
{

    public $timestamps = false;

    public function getTable()
    {
        if (isset($this->table)) return $this->table;

        return str_replace('\\', '', snake_case(class_basename($this)));
    }

    public function authFields($fields)
    {
        foreach ($fields as $key => $value) {
            if ($this->$key != $value) {
                throw new AuthFieldsException("field:{$this->$key} !=value:$value");
            }
        }
        return $this;
    }

    public function authUser($userId)
    {
        return $this->authFields(['user_id' => $userId]);
    }

    public function authAgent($agentId)
    {
        return $this->authFields(['agent_id' => $agentId]);
    }
}