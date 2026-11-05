<?php

class Bebida extends Producto
{
    private $mililitros;

    public function getMililitros() {
        return $this->mililitros;
    }

    public function setMililitros($mililitros) {
        $this->mililitros = $mililitros;
    }
}