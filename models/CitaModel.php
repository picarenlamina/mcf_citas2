<?php
class CitaModel
{
    protected $db;

    private $cita_id;
    private $fecha;
    private $hora;

    public function __construct()
    {
        //Traemos la única instancia de PDO
        $this->db = SPDO::singleton();
    }

    public function getCita_id()
    {
        return $this->cita_id;
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    public function getHora()
    {
        return $this->hora;
    }

    public function getById($codigo)
    {

        $consulta = $this->db->prepare('SELECT * FROM citas_citas where cita_id = ?');
        $consulta->bindParam(1, $codigo);
        $consulta->setFetchMode(PDO::FETCH_CLASS, "CitaModel");
        $consulta->execute();
        $resultado = $consulta->fetch();

        return $resultado;
    }

    public function getAll()
    {

        $consulta = $this->db->prepare('select * from citas_citas where bloqueo <> true and  cita_id not in ( select cita_id from citas_reservas )');

        $consulta->setFetchMode(PDO::FETCH_CLASS, "CitaModel");
        $consulta->execute();
        $resultado = $consulta->fetchAll();

        return $resultado;
    }

    public function isLibre($codigo)
    {

        $consulta = $this->db->prepare('select cita_id from citas_reservas where cita_id = ?');
        $consulta->bindParam(1, $codigo);
        $consulta->execute();
        $consulta->setFetchMode(PDO::FETCH_CLASS, "CitaModel");
      
        $resultado = $consulta->fetch();
       
        if ($resultado) {
            return false;
        } else {
            return true;
        }

    }

    public function bloqueo( $codigo )
    {
        try{
            $this->db->beginTransaction();

            
            //$consulta = $this->db->prepare('LOCK TABLES citas_citas WRITE');
            $consulta = $this->db->prepare('select * from citas_citas where cita_id = ? for update');
            $consulta->bindParam(1, $codigo);
            $consulta->execute();

            $consulta = $this->db->prepare('update citas_citas set bloqueo = true where cita_id = ? ');
            $consulta->bindParam(1, $codigo);
            $consulta->execute();
            
            $consulta = $this->db->prepare('update citas_citas set log = ? where cita_id = ? ');

            $time = microtime(true);
            $consulta->bindParam(1, $time );
            $consulta->bindParam(2, $codigo);
            $consulta->execute();
           
           
            $this->db->commit();
        }
        catch( Exception $e) {
            echo $e->getMessage();
            $this->db->rollBack();
        }

    }


    public function desbloqueo( $codigo )
    {
        try{
            $this->db->beginTransaction();
            
     
            
            $consulta = $consulta = $this->db->prepare('select * from citas_citas  where bloqueo = true and cita_id not in in ( select cita_id from citas_reservas ) for update);
            $consulta->execute();
            $rows = $consulta->fechAll();

            $consulta = $this->db->prepare('update citas_citas set bloqueo = true , log = null where cita_id = ? ');
            $consulta->bindParam(1, $codigo);
            $consulta->execute();
           
            $this->db->commit();
        }
        catch( Exception $e) {
            echo $e->getMessage();
            $this->db->rollBack();
        }

    }
}
