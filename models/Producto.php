<?php

abstract class Producto
{
    // Los nombres deben coincidir con las columnas de la tabla (camisetas)
    protected $id;
    protected $categoria_id;
    protected $nombre;
    protected $descripcion;
    protected $precio;
    protected $imagen;
    protected $activo;

    /**
     * fetch_object() rellena las propiedades ANTES de llamar al constructor.
     * Por eso no hay que pisarlas con valores vacíos cuando se instancia sin argumentos.
     */
    public function __construct($id = null, $categoria_id = null, $nombre = null, $descripcion = null, $precio = null, $imagen = null, $activo = null)
    {
        if($id !== null){
            $this->id = $id;
        }
        if ($categoria_id !== null) {
            $this->categoria_id = $categoria_id;
        }
        if ($nombre !== null) {
            $this->nombre = $nombre;
        }
        if ($descripcion !== null) {
            $this->descripcion = $descripcion;
        }
        if ($imagen !== null) {
            $this->imagen = $imagen;
        }
        if ($precio !== null) {
            $this->precio = $precio;
        }
        if ($activo !== null) {
            $this->activo = $activo;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getCategoriaId()
    {
        return $this->categoria_id;
    }

    public function setCategoriaId($categoria_id)
    {
        $this->categoria_id = $categoria_id;
        return $this;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getPrecio()
    {
        return $this->precio;
    }

    public function setPrecio($precio)
    {
        $this->precio = $precio;
        return $this;
    }

    public function getImagen()
    {
        return $this->imagen;
    }

    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
        return $this;
    }

    public function getActivo()
    {
        return $this->activo;
    }

    public function setActivo($activo)
    {
        $this->activo = $activo;
        return $this;
    }
}
