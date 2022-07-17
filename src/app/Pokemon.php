<?php

class Pokemon
{

    public function finder(\Tina4\Response $response): array
    {
        return $response (\Tina4\renderTemplate("finder.twig"), HTTP_OK, TEXT_HTML);
    }

    public function profile(\Tina4\Response $response, \Tina4\Request $request)
    {

        if (isset($request->params["name"])) {
            $name = ucfirst($request->params["name"]);
            $pokemon = [];
            $pokemonApi = (new PokemonAPI)->getPokemon($response, $request)["content"];
            $pokemonApi = json_decode($pokemonApi, true);

            //Extract elements we want for profile
            $pokemon["name"] = $name;
            $pokemon["height"] = $pokemonApi["height"];
            $pokemon["weight"] = $pokemonApi["weight"];
            $pokemon["sprite"] = $pokemonApi["sprites"]["front_default"];

            $pokemon["liked"] = (new PokemonModel)->load("name = '{$name}'")->liked;
            $movesApi = $pokemonApi["moves"];
            //Gather all move names
            foreach($movesApi as $m){
                $pokemon["moves"][] = ucfirst(str_replace("-", " ", $m["move"]["name"]));
            }
        } else {
            \Tina4\redirect("pokemon-finder");
        }

        return $response (\Tina4\renderTemplate("profile.twig", $pokemon), HTTP_OK, TEXT_HTML);
    }

    public function like(\Tina4\Response $response, \Tina4\Request $request)
    {
        if (isset($request->params["name"])) {
            $name = ucfirst($request->params["name"]);
            $pokemon = (new PokemonModel)->load("name = '{$name}'");
            $pokemon->liked++;
            $pokemon->save();
            echo $pokemon->liked;
        } else {
            echo false;
        }
    }
}