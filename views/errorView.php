<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>MVC - Modelo, Vista, Controlador - Jourmoly</title>
</head>
<body>

Se ha producido un error.


<?php 
if( isset( $error ) ) 
    echo $error;
?>
<?php 
if( isset( $enlace ) ) 
?>
    <a href="<?php echo $enlace?>">Continuar</a>

</body>
</html>