<?php
// importar todo lo descargado con composer (el framework)
require_once "vendor/autoload.php";

//instancia del objeto slim
$app = new \Slim\Slim();
//conexio a la base de dades
$db= new mysqli("localhost","root","","curso_angular4");

//configuracio cabeceras http
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}


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

//devolver un producto
$app->get('/producto/:id', function($id) use ($db, $app){
	//consulta
	$sql= 'SELECT * FROM productos WHERE id ='.$id;
	//ejecutar consulta
	$query= $db ->query($sql);

	$result =array(
		'status' => 'error',
		'code' => 404,
		'message'=> 'Producto no disponible'
	);

	//comprobar que solo devuelve 1 
	if($query->num_rows == 1){
		//recoger resultado
		$producto = $query->fetch_assoc();
		$result= array(
			'status' => 'success',
			'code' => 200,
			'data' => $producto
		);

	}

	echo json_encode($result);
	
});

//eliminar un producto
$app->get('/delete-producto/:id', function($id) use ($app, $db){
	$sql= "DELETE FROM productos WHERE id =".$id;
	$query=$db->query($sql);

	if($query){
		$result=array(
			'status' => 'success',
			'code' => 200,
			'message'=> 'Producto eliminado'
		);
	}else{
		$result= array(
			'status' => 'error',
			'code' => 404,
			'message'=> 'Producto no eliminado'
		);

	}

	echo json_encode($result);

});

//actualizar modificar producto
$app->post('/update-producto/:id', function ($id) use ($app, $db){
	//agarre datos del formulari
	$json= $app->request->post('json');

	//decodifique json en array
	$data = json_decode($json, true);

	//sql i execució
	$sql= "UPDATE productos SET ".
			"nombre = '{$data["nombre"]}',".
			"descripcion = '{$data["descripcion"]}',".
			"precio = '{$data["precio"]}'";

	if (isset($data['imagen'])) {
		$sql .= ", imagen = '{$data["imagen"]}'";
	}

	$sql .=	" WHERE id = {$id}";

	$query=$db->query($sql);

	if($query){
		$result=array(
			'status' => 'success',
			'code' => 200,
			'message'=> 'Producto actualizado'
		);
	}else{
		$result= array(
			'status' => 'error',
			'code' => 404,
			'message'=> 'Producto no actualizado'
		);

	}

	echo json_encode($result);

});

//subir imagen al producto
$app->post('/upload-file', function () use ($db, $app){
	$result= array(
		'status' => 'error',
		'code' => 404,
		'message'=> 'La imagen no ha podido subirse'
	);

	//_FILES es la variable global que te php per a captar els fitxers del formulari
	//uploads es com es dirá el input 
	if(isset($_FILES['uploads'])){
		//llibreria per a pujar imatges a la carpeta
		$piramideUploader = new PiramideUploader();

		//nos  permite subir el fichero a nuestra carpeta
		//upload(prefijo imagen, el name de _FILES, on es guardara, extensions que permet)
		$upload = $piramideUploader->upload('image', 'uploads','uploads', array('image/jpeg','image/png', 'image/gif'));

		//informacio del fitxer que hem pujat
		$file = $piramideUploader->getInfoFile();
		$file_name = $file['complete_name'];

	 	//si existeiix upload pero no  s'ha pujat be uploaded	false
		if(isset($upload) && $upload["uploaded"] == false){
			$result= array(
				'status' => 'error',
				'code' => 404,
				'message'=> 'La imagen no ha podido subirse'
			);

		}else{
			$result= array(
				'status' => 'success',
				'code' => 200,
				'message'=> 'La imagen se ha subido',
				'filename' => $file_name
			);
		}

	}

	echo json_encode($result);

});

//crea la ruta productos i la funcion insertar
$app->post('/producto', function () use ($app, $db){
	//recoge los datos del form en formato json
	$json= $app->request->post('json');
	//descodifica json y lo transforma en array
	$data=  json_decode($json, true);

	if(!isset($data['nombre'])){
		$data['nombre']= null;
	}

	if(!isset($data['descripcion'])){
		$data['descripcion']= null;
	}

	if(!isset($data['precio'])){
		$data['precio']= null;
	}

	if(!isset($data['imagen'])){
		$data['imagen']= null;
	}
	
	$query="INSERT INTO productos VALUES (NULL,".
		"'{$data['nombre']}',".
		"'{$data['descripcion']}',".
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