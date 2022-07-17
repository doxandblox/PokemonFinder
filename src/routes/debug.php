<?php
\Tina4\Get::add ("/debug/test-raw", function(\Tina4\Response $response, \Tina4\Request $request) {
    DebugHelper::getInstance()->raw((new PokemonModel)->select('*'));
});
\Tina4\Get::add ("/debug/test-simple", function(\Tina4\Response $response, \Tina4\Request $request) {
    DebugHelper::getInstance()->dds((new PokemonModel)->select('*'));
});
\Tina4\Get::add ("/debug/test-verbose", function(\Tina4\Response $response, \Tina4\Request $request) {
    DebugHelper::getInstance(true)->dd((new PokemonModel)->select('*'));
});