<?php 
/**
*	Autor: Agustin Rios Reyes <nitsugario@gmail.com>
*	Vercion:0.2
*	Fecha de Creacion: 15/02/2014
*	Actualización:
*/
class DirectorioIO{
	public $directorio;
	public $archivo;
	private $resul="";
	private $cont;
	public function LeerDirectorio(){
		return $this->EscanearDirectorio($this->directorio)."\n\t</ul>\n\t</li>\n</ul>\n<br><br>";
	}
	public function DescargarArchivo(){
		$this->DescargarArchivos($this->directorio.'\\',$this->archivo);
	}
	public function EliminarArchivo(){
		return $this->BorrarArchivos($this->directorio.'\\'.$this->archivo);
	}
	public function EliminarDirectorioR(){
		return $this->BorrarDirectorioR($this->directorio);
	}
	public function MostrarArchivo(){}
	public function SubirArchivo(){}
	public function ValidaExtencion(){
		return $this->ValidaExtencions();
	}
	private function BorrarDirectorioR($directorio){
		if(file_exists($directorio)){
			if (is_dir($directorio)){
				$objetosDir = scandir($directorio);
				foreach ($objetosDir as $objetoDir) {
					if ($objetoDir != "." && $objetoDir != ".."){
						if (filetype($directorio."/".$objetoDir) == "dir"){
							$this->BorrarDirectorioR($directorio."/".$objetoDir);
						}else{
							if(!unlink($directorio."/".$objetoDir)){
								return "Ocurri un Error al Borrar el Archivo  ".$directorio."/".$objetoDir;
							}
						}
					}
				}
				if(rmdir($directorio)){
					return "El Directorio se Borro: $directorio";
				}else{
					return "El Directorio ' $directorio ' No se Pudo Borrar.";
				}
			}else{
				return "El nombre ' $directorio ' no es un Directorio.";
			}
		}else{
			return "El Directorio ' $directorio ' No Existe.";
		}
	}//termina la funcion.
	private function BorrarArchivos($archivo){
		if(file_exists($archivo)){
			if(is_file($archivo)){
				if(unlink($archivo)){
					return "El Archivo $archivo se Borro.";
				}else{
					return "El Archvio $archivo No se pudo Borrar.";
				}
			}else{
				return "El Nombre $archivo No es un archivo.";
			}
		}else{
			return "El Archvio $archivo No Existe.";
		}
	}
	private function EscanearDirectorio($directorio,$nivel=0){
		if(file_exists($directorio)){
			if(is_dir($directorio)){
				if(is_readable($directorio)){
					$contenido = array_diff( scandir( $directorio ), Array( ".", ".." ) );//quitamos los directorios "." y ".." del array de scandir.para evitar un bucle infinito.
					if($nivel==0){
						$sangria="\n\t";//para el codigo HTML generado.
						$resul = "\n<ul id='CarpetaTree'>";
						//$resul = $resul . "$sangria<li class='subcarpetas'>".$directorio."\\$sangria<ul>"; //Directorio principal.
						$resul = $resul . "$sangria<li class='subcarpetas'>".$directorio."/$sangria<ul>"; //Directorio principal.
					$cont=0;
					}else{
						$sangria="\n\t";
						for ($i=0; $i < $nivel; $i++) { 
							$sangria=$sangria."\t";
						}
					}
					foreach($contenido as $archivo){
						//if(is_dir($directorio."/".$archivo)){
						if(is_dir($directorio."/".$archivo)){
							$sangria="\n\t";
							for ($i=0; $i < $nivel; $i++) { 
								$sangria=$sangria."\t";
							}
							//$resul = $resul . "$sangria<li class='subcarpetas'>".$directorio."\\".$archivo."\\$sangria<ul class='subcarpeta'>";//subDirectorios.
							$resul = $resul . "$sangria<li class='subcarpetas'>".$directorio."/".$archivo."/$sangria<ul class='subcarpeta'>";//subDirectorios.
							//$resul = $resul . $this->EscanearDirectorio($directorio."\\".$archivo,$nivel+1)."$sangria</ul>$sangria</li>";
							$resul = $resul . $this->EscanearDirectorio($directorio."/".$archivo,$nivel+1)."$sangria</ul>$sangria</li>";
						}else{
							//$resul = $resul . "$sangria<li class='documento' dir='".$directorio."\\' Archi='".$archivo."'>".$archivo."</li>";
							$resul = $resul . "$sangria<li class='documento' dir='".$directorio."/' Archi='".$archivo."'>".$archivo."</li>";
						}
						$cont = $cont + 1;
						if ($cont == count($contenido)) {
							return $resul;
						}
					}
				}else{
					return "<span class='Errors'> El Directorio \" $directorio \" No se pude Leer.</span><br>";
				}
			}else{
				return "<span class='Errors'> El Nombre \" $directorio \" No Es un Directorio.</span><br>";
			}
		}else{
			return "<span class='Errors'> El Nombre del Directorio \" $directorio \" No Existe.</span><br>";
		}   
	}//termina funcion
	private function DescargarArchivos($directorio='descargas/',$archivo){
		$type ='';
		if($archivo=='error'){
			header('HTTP/1.0 404 No encontrado');
			echo "<br><img src=\"../imagenes/warning.png\"  id=\"warning\"><h1><span style=\"color: #DD0000\"><strong> Error 404. Archivo No Encontrado.</strong></h1></span>";
		}else{
			$dirarchivo=$directorio.$archivo;
			if(file_exists($dirarchivo)){
				if(is_file($dirarchivo)){
					$size=filesize($dirarchivo);
					$fileName = basename($dirarchivo);
					/*if (function_exists('mime_content_type')) {
						$type = mime_content_type($dirarchivo);
					} else if (function_exists('finfo_file')) {
						$info = finfo_open(FILEINFO_MIME);
						$type = finfo_file($info, $dirarchivo);
						finfo_close($info);  
					}
					if ($type == '') {
						$type = "application/force-download";
					}*/
					$type =$this->ValidaMimetype($directorio,$archivo);
					// se agregan los encabesados.
					header("Cache-Control: private");
					header("Content-Type: $type");
					header("Content-Disposition: attachment; filename=".$fileName);
					header("Content-Transfer-Encoding: binary");
					header("Content-Length: " . $size);
					// Envia el archive al buffer de salida. 
					readfile($dirarchivo);
					exit();
				}else{
					echo "<span style=\"color: #DD0000\">¡Ocurrio un Error! <br> El $dirarchivo NO Es un Archivo Valido.</span><br>"; 
				}
			}else{
				echo "<span style=\"color: #DD0000\">¡Ocurrio un Error! <br> El $dirarchivo NO Existe</span><br>";
			}
		}
	}
	private function ValidaMimetype($directorio,$Archivo){
		$type ='';
		$exteOffice = array('doc','dot','docx','dotx','docm','dotm','xls','xlt','xla','xlsx','xltx','xlsm','xltm','xlam','xlsb','ppt','pot','pps','ppa','pptx','potx','ppsx','ppam','pptm','potm','ppsm');
		$MimeTypeOffice = array('doc' => 'application/msword','dot' => 'application/msword','docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document','dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template','docm' => 'application/vnd.ms-word.document.macroEnabled.12','dotm' => 'application/vnd.ms-word.template.macroEnabled.12','xls' => 'application/vnd.ms-excel','xlt' => 'application/vnd.ms-excel','xla' => 'application/vnd.ms-excel','xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template','xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12','xltm' => 'application/vnd.ms-excel.template.macroEnabled.12','xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12','xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12','ppt' => 'application/vnd.ms-powerpoint','pot' => 'application/vnd.ms-powerpoint','pps' => 'application/vnd.ms-powerpoint','ppa' => 'application/vnd.ms-powerpoint','pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation','potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template','ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow','ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12','pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12','potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12','ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12');
		$MimeTypesToAddArray = array("docm"=>"application/vnd.ms-word.document.macroEnabled.12","docx"=>"application/vnd.openxmlformats-officedocument.wordprocessingml.document","dotm"=>"application/vnd.ms-word.template.macroEnabled.12","dotx"=>"application/vnd.openxmlformats-officedocument.wordprocessingml.template","potm"=>"application/vnd.ms-powerpoint.template.macroEnabled.12","potx"=>"application/vnd.openxmlformats-officedocument.presentationml.template","ppam"=>"application/vnd.ms-powerpoint.addin.macroEnabled.12","ppsm"=>"application/vnd.ms-powerpoint.slideshow.macroEnabled.12","ppsx"=>"application/vnd.openxmlformats-officedocument.presentationml.slideshow","pptm"=>"application/vnd.ms-powerpoint.presentation.macroEnabled.12","pptx"=>"application/vnd.openxmlformats-officedocument.presentationml.presentation","xlam"=>"application/vnd.ms-excel.addin.macroEnabled.12","xlsb"=>"application/vnd.ms-excel.sheet.binary.macroEnabled.12","xlsm"=>"application/vnd.ms-excel.sheet.macroEnabled.12","xlsx"=>"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet","xltm"=>"application/vnd.ms-excel.template.macroEnabled.12","xltx"=>"application/vnd.openxmlformats-officedocument.spreadsheetml.template");
		//obtenemos la extencion.
		$extencion = $this->ValidaExtencions($Archivo);
		//validamos si es un archivo de Office.
		if(in_array($extencion, $exteOffice)){
			$type = $MimeTypeOffice[$extencion];
		}else{
			$dirarchivo=$directorio.$Archivo;
			if (function_exists('mime_content_type')) {
				$type = mime_content_type($dirarchivo);
			} else if (function_exists('finfo_file')) {
				$info = finfo_open(FILEINFO_MIME);
				$type = finfo_file($info, $dirarchivo);
				finfo_close($info);  
			}
			if ($type == '') {
				$type = "application/force-download";
			}
		}
		return $type;
	}
	private function ValidaExtencions(){
		return substr(strrchr($this->archivo, '.'), 1);
	}
}//fin clase
//error_reporting(false);
//$dir = new DirectorioIO();
//$dir->directorio="C:\wamp\www\VerCodigoDirectorio";
//echo $dir->LeerDirectorio();
//$dir->archivo="MapadeServicios.doc";
//$dir->DescargarArchivo();

//$dir->archivo="principal1.html";
//echo $dir->EliminarArchivo();

//$dir->directorio="C:\wamp\www\archivodirectorio\criptografo\\";
//echo $dir->EliminarDirectorioR();

//$dir->archivo="principal1.html";
//echo $dir->ValidaExtencion();
 ?>


