<?php
// importar todo lo descargado con composer (el framework)
require_once "vendor/autoload.php";

//instancia del objeto slim
$app = new \Slim\Slim();

//el metodo get nos permite crear una ruta por get(crea la ruta /pruebas http://localhost/curso-angular4-backend/index.php/pruebas)
//el metodo use nos permite usar dentro del metodo variables que hay fuera, en este caso, la vaariable app
$app->get("/pruebas", function() use ($app){
	echo "Hola mundo desde Slim PHP";
});

//carga todo para que funcionen las rutas
$app->run();