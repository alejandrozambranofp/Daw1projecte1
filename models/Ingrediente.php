<?php

class Ingrediente implements JsonSerializable{

//atributos privados para que no se puedan cambiar los datos de estos campos por error
    private $id;
    private $nombre;
    private $precio_extra;
    private $activo;


//esta funcion construye el objeto cuando se hace new ingrediente es decir crea el objeto para rellenar
//con el ingrediente que se quiera consultar
    public function __construct($id, $nombre, $precio_extra, $activo){
        $this->id = $id;
        $this->nombre = $nombre;
        $this->precio_extra = $precio_extra;
        $this->activo = $activo;
    }


//Aqui se saca cada campo en publico para que el dao lo pueda utilizar
//esto se hace para que el dao tenga los campos donde rellenar lo que ha recibido en la consulta
//se hace asi para que una vista no cambie sin querer un dato para siempre por ejemplo un precio al aplicar algun descuento por ejemplo
    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getPrecioExtra()
    {
        return $this->precio_extra;
    }

    public function getActivo()
    {
        return $this->activo;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id'           => (int) $this->id,
            'nombre'       => $this->nombre,
            'precio_extra' => (float) $this->precio_extra,
            'activo'       => (int) $this->activo
        ];
    }

}

