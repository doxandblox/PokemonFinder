<?php
//Finder
\Tina4\Get::add("pokemon-finder", ["Pokemon", "finder"]);
//Profile
\Tina4\Get::add("pokemon-profile", ["Pokemon", "profile"]);
//Like
\Tina4\Get::add("pokemon-like", ["Pokemon", "like"]);

//Manage local Pokémon Listing

\Tina4\Get::add("/app/pokemon/landing", function (\Tina4\Response $response){
    return $response (\Tina4\renderTemplate("/app/pokemon/grid.twig"), HTTP_OK, TEXT_HTML);
});
        
/**
 * CRUD Prototype PokemonModel Modify as needed
 * Creates  GET @ /path, /path/{id}, - fetch,form for whole or for single
            POST @ /path, /path/{id} - create & update
            DELETE @ /path/{id} - delete for single
 */
\Tina4\Crud::route ("/app/pokemon", new PokemonModel(), function ($action, PokemonModel $pokemonmodel, $filter, \Tina4\Request $request) {
    switch ($action) {
       case "form":
       case "fetch":
            //Return back a form to be submitted to the create
             
            if ($action == "form") {
                $title = "Add PokemonModel";
                $savePath =  TINA4_SUB_FOLDER . "/app/pokemon/";
                $content = \Tina4\renderTemplate("/app/pokemon/form.twig", []);
            } else {
                $title = "Edit PokemonModel";
                $savePath =  TINA4_SUB_FOLDER . "/app/pokemon/".$pokemonmodel->id;
                $content = \Tina4\renderTemplate("/app/pokemon/form.twig", ["data" => $pokemonmodel]);
            }

            return \Tina4\renderTemplate("components/modalForm.twig", ["title" => $title, "onclick" => "if ( $('#pokemonmodelForm').valid() ) { saveForm('pokemonmodelForm', '" .$savePath."', 'message'); $('#formModal').modal('hide');}", "content" => $content]);
       break;
       case "read":
            //Return a dataset to be consumed by the grid with a filter
            $where = "";
            if (!empty($filter["where"])) {
                $where = "{$filter["where"]}";
            }
        
            return   $pokemonmodel->select ("*", $filter["length"], $filter["start"])
                ->where("{$where}")
                ->orderBy($filter["orderBy"])
                ->asResult();
        break;
        case "create":
            //Manipulate the $object here
        break;
        case "afterCreate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>pokemonmodelGrid.ajax.reload(null, false); showMessage ('PokemonModel Created');</script>"];
        break;
        case "update":
            //Manipulate the $object here
        break;    
        case "afterUpdate":
           //return needed 
           return (object)["httpCode" => 200, "message" => "<script>pokemonmodelGrid.ajax.reload(null, false); showMessage ('PokemonModel Updated');</script>"];
        break;   
        case "delete":
            //Manipulate the $object here
        break;
        case "afterDelete":
            //return needed 
            return (object)["httpCode" => 200, "message" => "<script>pokemonmodelGrid.ajax.reload(null, false); showMessage ('PokemonModel Deleted');</script>"];
        break;
    }
});