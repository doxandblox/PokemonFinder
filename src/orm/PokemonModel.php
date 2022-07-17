<?php

class PokemonModel extends \Tina4\ORM {
    public $tableName = "pokemon";
    public $primaryKey = "id";

    public $id; //primary key because it is first
    public $name; //some additional data
    public $url;
    public $liked;

}