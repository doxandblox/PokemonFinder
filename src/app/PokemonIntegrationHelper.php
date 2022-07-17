<?php

class PokemonIntegrationHelper
{
    //Store Full Pokémon element in local database
    /**
     * @throws JsonException
     * @throws SqLiteException
     * @throws Exception
     */
    public function localizePokemonListing(\Tina4\Response $response, \Tina4\Request $request)
    {
        //Gather listing from api
        $pokemonAPIListing = $this->listFromAPI($response, $request);
        $pokemonAPICount = \count($pokemonAPIListing);
        //Get a count of localized data
        $pokemonModel = new PokemonModel();
        $localCountObj = $pokemonModel->select("count(*) as count")->asArray();
        $localCount = $localCountObj[0]["count"];
        //There is nothing localized, process listing
        if (!$localCount) {
            //Iterate through listing and save Pokémon listing
            foreach ($pokemonAPIListing as $p) {
                try {
                    $pokemon = (new PokemonModel());
                    $pokemon->name = ucfirst($p->name);
                    $pokemon->url = $p->url;
                    $pokemon->save();
                } catch (SQLiteException $e) {
                    return $response($e->getMessage());
                }
            }
            return $response("Successfully synced data");
        } else if ($localCount < $pokemonAPICount) {

            $localPokemonListing = $pokemonModel->select("name,url", $localCount)->asObject();
            //We are guaranteed a sorted array from PokeApi hence
            //we can take a unique element and match diff index to PokeAPIListing
            $local = [];
            $api = [];
            foreach ($localPokemonListing as $l) {
                $local[] = $l->name;
            }
            foreach ($pokemonAPIListing as $p) {
                $api[] = $p->name;
            }
            $diff = array_diff($api, $local);
            foreach ($diff as $k => $v) {
                try {
                    $pokemon = (new PokemonModel());
                    $pokemon->name = ucfirst($pokemonAPIListing[$k]->name);
                    $pokemon->url = $pokemonAPIListing[$k]->url;
                    $pokemon->save();
                } catch (SQLiteException $e) {
                    return $response($e->getMessage());
                }
            }
            return $response("Successfully synced data");
        } else {
            return $response("Successfully synced data");
        }
    }

    /**
     * @throws JsonException
     */
    private function listFromAPI(\Tina4\Response $response, \Tina4\Request $request)
    {
        //Instantiate API
        $pokemonApi = new PokemonAPI;
        //Attempt to get a result
        try {
            //If API is available, they return simple string errors
            $pokemonListingObj = $pokemonApi->getPokemonListing($response, $request);
        } catch (JsonException $e) {
            //There has been a problem getting data from API
            return $response($e->getMessage());
        }
        $pokemonListing = json_decode($pokemonListingObj["content"] ?? $pokemonListingObj);
        //Upon failure of decode, return response from API
        if ($pokemonListing === FALSE) {
            $content = "Invalid / Malformed json response (Request From API Failed : " . $pokemonListingObj;
            return $response($content);
        } else {
            return $pokemonListing;
        }
    }

}