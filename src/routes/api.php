<?php

//Return local listing of Pokémon
\Tina4\Get::add("api/get-local-pokemon-listing", ["PokemonApi", "getLocalPokemonListing"]);

//Return single Pokémon from PokeAPI
\Tina4\Get::add("api/get-pokemon", ["PokemonAPI", "getPokemon"]);
//Return Full listing of Pokémon from PokeAPI
\Tina4\Get::add("api/get-pokemon-listing", ["PokemonAPI", "getPokemonListing"]);