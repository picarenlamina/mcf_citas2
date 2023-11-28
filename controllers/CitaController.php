<?php
class CitaController
{
    public $view;
	
	function __construct()
    {
        //Creamos una instancia de nuestro mini motor de plantillas
        $this->view = new View();
    }
 
   
 
    public function listado()
    {
        //Incluye el modelo que corresponde
        require 'models/CitaModel.php';
		
        //Creamos una instancia de nuestro "modelo"
        $cita = new CitaModel();
		
        //Le pedimos al modelo todos los items
		$citas = $cita->getAll();
		
        //Finalmente presentamos nuestra plantilla
        $this->view->show("cita/listadoView.php", [ "citas" => $citas ] );
    }
    
    public function reservar()
    {
        
		require 'models/CitaModel.php';
		require 'libs/libreria.php';
		require 'libs/Mailer.php';

		if( isset( $_REQUEST[ 'cita_id' ]) )
		{
			$citaModel = new CitaModel();
			$cita = $citaModel->getById( $_REQUEST[ 'cita_id' ] );
			$libre = $citaModel->isLibre( $_REQUEST[ 'cita_id' ] );
			
			if ( ! ( $cita && $libre ) )
			{	
				$this->view->show("errorView.php", array( "error" =>"Error Sistema 1", "enlace" => "index.php"));
				exit(0);
			}
			else 
				$citaModel->bloqueo( $_REQUEST['cita_id']);
				$_SESSION[ "cita_id" ] = $_REQUEST['cita_id'];
		}
		elseif( isset( $_SESSION[ "cita_id" ]))
		{
			$citaModel = new CitaModel();
			$cita = $citaModel->getById( $_SESSION[ "cita_id" ]  );
			$libre = $citaModel->isLibre( $_SESSION[ "cita_id" ] );
			if ( ! ( $cita && $libre ) )
			{	
				$this->view->show("errorView.php", array( "error" =>"Error Sistema 2", "enlace" => "index.php"));
				exit(0);
			}
		}
		else // 
		{
			$this->view->show("errorView.php", array( "error" =>"Error Sistema 3", "enlace" => "index.php"));
			exit(0);
		}
		

        if( isset( $_REQUEST["submit"] )&& $_REQUEST["submit"] == "Aceptar" )
        {
            $errores = array();
                
			if(empty($_REQUEST["nombre"])){
					$errores['nombre'] = "* Nombre: Error";
			}
			if(empty($_REQUEST["apellidos"])){
					$errores['apellidos'] = "* Apellidos: Error";
			}
			/*if(empty($_REQUEST["telefono"]) || ! validateTelefono($_REQUEST["telefono"])){
					$errores['telefono'] = "* Telefono: Error";
			}
			if(empty($_REQUEST["email"]) || ! validateEmail($_REQUEST["email"])){
					$errores['email'] = "* Email: Error ";
			}
*/
			if(empty($_REQUEST["telefono"]) ){
				$errores['telefono'] = "* Telefono: Error";
			}
			if(empty($_REQUEST["email"]) ){
					$errores['email'] = "* Email: Error ";
			}
			
				
			if( empty($errores) )
			{     
				require 'models/UsuarioModel.php';
				require 'models/ReservaModel.php';
				
				$usuarioModel = new UsuarioModel();
				$usuario = $usuarioModel->getByNif( $_REQUEST[ 'nif' ] );
				
			

				if( ! $usuario  )
				{
					
					$usuario  = new UsuarioModel();
					
				}	

				$usuario->setNombre( $_REQUEST[ 'nombre' ]);
				$usuario->setApellidos( $_REQUEST[ 'apellidos' ]);
				$usuario->setTelefono( $_REQUEST[ 'telefono' ]);
				$usuario->setEmail( $_REQUEST[ 'email' ]);
				$usuario->setNif( $_REQUEST[ 'nif' ]);
				
				$usuario->save();
				
				$reserva = new ReservaModel();
				$reserva->setCita_id( $_SESSION[ "cita_id"] );
				$reserva->setUsuario_id( $usuario->getUsuario_id() );

				$reserva->save();

				session_unset();
				$this->view->show("msgView.php", array( "msg" =>"Su cita ha sido registrada", "enlace" => "index.php"));
				
				// Envio de correo
				$mailer = new Mailer(  $_REQUEST[ 'email' ], $cita->getFecha(), $cita->getHora());
				$mailer->send();
				
				if(!$mailer->Send()) 
				{
					echo "Erreror: " . $mailer->ErrorInfo;
				}
				else 
				{
					echo "¡Mensaje enviado!";
				}
			}
            else{ 
				$this->view->show("cita/reservaView.php", array( "cita" => $cita,  "errores"=>$errores ));
            }
        }
        else
            $this->view->show("cita/reservaView.php", array( "cita" => $cita ));
        
    }
    
    
}
?>