<?php

defined("POKEMON_API_URL") or define("POKEMON_API_URL", "https://pokeapi.co/api/v2/");

class PokemonAPI
{

    public function getLocalPokemonListing(\Tina4\Response $response, \Tina4\Request $request) : array {
        $pokemonModel = new PokemonModel();
        $pCountObj = $pokemonModel->select("count(*) count")->asObject();
        $pCount = $pCountObj[0]->count;
        $localPokemonListing = $pokemonModel->select("name,url",$pCount)->asObject();
        return $response($localPokemonListing);
    }
    /**
     * Return Single Pokémon based on id
     * @id INT
     * As it's a public api, lets pull in the data and store it locally
     * @throws JsonException
     * @throws Exception
     */
    public function getPokemon(\Tina4\Response $response, \Tina4\Request $request) : array
    {
        if(isset($request->params["id"])){
            $pokemon = (new \Tina4\Api("", ""))->sendRequest(POKEMON_API_URL . "pokemon/".$request->params["id"])["body"] ?? [];
        } else if (isset($request->params["name"])){
            $pokemon = (new \Tina4\Api("", ""))->sendRequest(POKEMON_API_URL . "pokemon/".$request->params["name"])["body"] ?? [];
        } else {
            throw new Exception("Must pass 'id' or 'name' parameter");
        }

        try {
            return $response($pokemon);
        } catch (JsonException $e) {
            return $response($e->getMessage());
        }
    }

    /**
     * Listing of Pokémon
     * API Returns max of 60 results per "page"
     * @throws JsonException
     */
    public function getPokemonListing(\Tina4\Response $response, \Tina4\Request $request) : array
    {

        //Create container for full listing
        $pokemonArr = [];
        //Size of batch splicing
        $bz = 60;

        //No endpoint for Pokémon count, so get first item and count of all data in response

        //Counter
        $cnt = (new \Tina4\Api("", ""))->sendRequest(POKEMON_API_URL . "pokemon")["body"]["count"] ?? 0;
        //Iterator
        $iterator = $cnt / $bz;

        //Check if there's something so iterator over
        if ($iterator > 0 && $iterator < 1) {
            //We have less than $bz, as such we can process the entire batch
            $iterator = 1;
        }

        //Iterate through batch count
        for ($x = 0; $x < $iterator; $x++) {
            //Calculate the batch to collect
            $o = $x * $bz;
            //Fetch the batch
            $pokemon = (new \Tina4\Api("", ""))->sendRequest(POKEMON_API_URL . "pokemon?limit=$bz&offset=$o");
            if (is_array($pokemon)){
                $pokemon = $pokemon["body"]["results"];
            }

            //Push to array for localization
            foreach ($pokemon as $p) {
                $pokemonArr[] = ["name" => $p["name"], "url" => $p["url"]];
            }
        }

        try {
            return $response($pokemonArr);
        } catch (JsonException $e) {
            return $response($e->getMessage());
        }
    }

}
