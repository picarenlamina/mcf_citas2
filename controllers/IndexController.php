<?php
class IndexController
{
    public $view;
    
    function __construct()
    {
        //Creamos una instancia de nuestro mini motor de plantillas
        $this->view = new View();
    }
 
    public function index()
    {
        header( "Location: index.php?controlador=cita&accion=listado");
    }
   
}
?>