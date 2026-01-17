<?php

require_once "app/model/autores.php";
$autorObj = new Autores();

//obtener id del autor desde la url
$id = $_GET["idAutor"] ?? null;

// Datos estáticos del autor
$autor = $autorObj->setUpFichaAutor($id, $conexion);


if (!$autor["biografia"] || !$autor["fecha_nacimiento"] || !$autor["lugar_nacimiento"]){

  
if ($autorObj->guardarDatosAutorAPI($autor["nombre"], $id, $conexion)){
    $autor = $autorObj->setUpFichaAutor($id, $conexion);
}  


}

// Simulación de libros del autor
$libros = $autorObj->obtenerLibrosAutor($id, $conexion);

// Simulación del ranking de autores
$rankingAutores = $autorObj->obtenerRanking($conexion);
$ranking = $rankingAutores[$id];