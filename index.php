<?php
// importar todo lo descargado con composer (el framework)
require_once "vendor/autoload.php";

//instancia del objeto slim
$app = new \Slim\Slim();
//conexio a la base de dades
$db= new mysqli("localhost","root","","curso_angular4");

//el metodo get nos permite crear una ruta por get(crea la ruta /pruebas http://localhost/curso-angular4-backend/index.php/pruebas)
//el metodo use nos permite usar dentro del metodo variables que hay fuera, en este caso, la vaariable app
$app->get("/pruebas", function() use ($app){
	echo "Hola mundo desde Slim PHP";
});

//listar todos los productos
$app->get("/productos", function() use ($db, $app){
	$sql= "SELECT * FROM productos ORDER BY id DESC;";
	$query= $db->query($sql);

	//crear una array de obejtos a partir del resultado de la query
	$productos= array();
	while ($producto =$query->fetch_assoc()) {
		$productos[]=$producto;
	}

	$result = array(
		'status'=>'success',
		'code' => 200,
		'data' => $productos
	);	

	echo json_encode($result);

});

//crea la ruta productos i la funcion insertar
$app->post('/productos', function () use ($app, $db){
	//recoge los datos del form en formato json
	$json= $app->request->post('json');
	//descodifica json y lo transforma en array
	$data=  json_decode($json, true);


	if(!isset($data['nombre'])){
		$data['nombre']= null;
	}

	if(!isset($data['description'])){
		$data['description']= null;
	}

	if(!isset($data['precio'])){
		$data['precio']= null;
	}

	if(!isset($data['imagen'])){
		$data['imagen']= null;
	}
	
	$query="INSERT INTO productos VALUES (NULL,".
		"'{$data['nombre']}',".
		"'{$data['description']}',".
		"'{$data['precio']}',".
		"'{$data['imagen']}'".
		");";

	$insert= $db->query($query);

	$result = array(
			'status'=>'error',
			'code' => 404,
			'message' => 'producto NO se ha creado'
		);
	
	if($insert){
		$result = array(
			'status'=>'success',
			'code' => 200,
			'message' => 'producto creado correctamente'
		);
	}

	//lo codifica a json para poderlo usar en javascript
	echo json_encode($result);

});

//carga todo para que funcionen las rutas
$app->run();