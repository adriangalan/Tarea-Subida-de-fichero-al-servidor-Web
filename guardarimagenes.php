<?php
define ('RUTA',"../../imgusers");
//entre los dos no mas de 300  Kbytes lo compruebo con la siguiente funcion:
function comprobarTamañosKb($primerFichero,$segundoFichero,$tamaño){
    $respuesta = 0;
    $tamañoFichero1 =floatval (number_format(($primerFichero / 1000), 1, ',', '.'));
    $tamañoFichero2 =floatval (number_format(($segundoFichero / 1000), 1, ',', '.'));
    $suma= $tamañoFichero1 + $tamañoFichero2;
    if ($suma >= $tamaño) {
        $respuesta=4;
    }
    return $respuesta;
}
//El tamaño máximo de los ficheros no puede superar los 200 Kbytes cada uno lo compruebo con la siguiente funcion:
function comprobarKb($fichero,$tamaño) {
    $respuesta=false;
    $numero=floatval(number_format(($fichero / 1000), 1, ',', '.'));
    if ($numero>=$tamaño) {
        $respuesta=true;
    }
    return $respuesta;
}
//Los ficheros tienes que ser o JPG o PNG no se admiten otros formatos lo compruebo con la siguiente funcion:
function tipoFichero($fichero,$tipos) {
    $respuesta=false;
    $extension=pathinfo($fichero, PATHINFO_EXTENSION);
    if(!in_array($extension, $tipos) ) {
        $respuesta=true;
    }
    return $respuesta;
    
}
// compruebo que existe el fichero con la siguiente funcion:
function existeFichero($fichero) {
    $respuesta=false;
    if (file_exists($fichero)) {
        $respuesta=true;
    }
    return $respuesta;
}
//
function comprobarErrores($name,$size,$directorioSubida) {
    $mensaje=0;
    $ruta=$directorioSubida."/".$name;
    $tipos=['jpg','png'];//tipos de extensiones permitidas
    if (comprobarKb($size, 200)) {  //tamaño minimo sel fichero
        $mensaje=1;
    }elseif (existeFichero($ruta)) { //comprueba si existe el fichero
        $mensaje=2;
    }elseif(tipoFichero($name,$tipos)) {
        $mensaje=3;
    }
    return $mensaje;
}
function mostrarMensageError($error,$texto,$nombre) {
    $respuesta = "Se ha producido el error nº ".$error  ." en el fichero:".$nombre."<br/> <em>". $texto[$error] ."</em> <br/>";
    return $respuesta;
}
function guardarFichero($temporalFichero,$directorioSubida,$nombreFichero) {
    $mensaje="";
    if (move_uploaded_file($temporalFichero,  $directorioSubida .'/'. $nombreFichero) == true) {
        $mensaje .= 'Archivo guardado  <br/>';
    }
    return $mensaje;
}
//proceso y variables
$mensaje="";
$errorFichero=5;
$codigosErrorSubida= [
    0 => 'Subida correcta<br/>',
    1 => 'El tamaño del archivo excede el admitido<br/>',
    2 => 'ERROR: El archivo ya existe<br/>',
    3 => 'ERROR: no es valido el tipo del archivo<br/>',
    4 => 'ERROR: no es valido el el tamaño de los archivos<br/>',
    5 => ''
];

if (count($_FILES)==2) {    
    if (comprobarTamañosKb($_FILES['imagen1']['size'],$_FILES['imagen2']['size'],300 )) {
        $errorFichero=4;        
    }
}
if (count($_FILES)>1) {    
    foreach ($_FILES as $value) {
        if (empty($value['name'])) {
            continue;            
        }
        if ($errorFichero!=4) {
            $errorFichero=comprobarErrores($value['name'],$value['size'],RUTA);
        }        
        if ($errorFichero > 0 ) {//compruebo los errores de la imagen 1
            $mensaje .= mostrarMensageError($errorFichero, $codigosErrorSubida, $value['name']);
        }else{
            $mensaje.=guardarFichero($value['tmp_name'], RUTA, $value['name']);
        }
    }    
}

    var_dump($_FILES);

?>

<html>
<head>
<title>Tarea: Subida de fichero al servidor Web</title>
<meta charset="UTF-8">
</head>
<body>
<h2>Subida y alojamiento de archivo en el servidor</h2>
<form  enctype="multipart/form-data" action="guardarimagenes.php" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="200000" /> <!--  200Kbytes -->

<label>Elija la primera imagen </label> <input name="imagen1" type="file" /> <br/>
<label>Elija la segunda imagen </label> <input name="imagen2" type="file" /> <br/><br/>

<?php 
echo $mensaje;
?>

<input type="submit" value="Subir archivo" />
</form>
</body>
</html>
