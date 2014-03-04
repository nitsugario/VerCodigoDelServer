<?php 
/**
*	Autor: Agustin Rios Reyes <nitsugario@gmail.com>
*	Vercion:0.3
*	Fecha de Creacion: 13/09/2011
*	Actualización:
*		Fecha: 11/10/2011
*		Se corrige el error de los comentarios múltiples .
*		Fecha: 08/02/2014
*		Se cambia las funciones estructuradas a un formato de POO, creando la clase SintaxisHJP().
*		Fecha: 09/02/2014
*		Se agregó la Sección de JavaScript, el cual ya se reconoce.
*		Se agregan estilos para las etiquetas por lenguaje.
*/
class SintaxisHJP{
	public $archivo;
	private $codigo;
	/**
	*	Función que lee el archivo y almacena el contenido en una cadena de caracteres.
	*	Se tiene que declarar la variable $this->archivo; con la ruta del archivo que se desea mostrar formateado.
	*/
	private function CargaArchivo(){
		if (($cadenaAr=file_get_contents($this->archivo))===false) {
			return "<span class='erros'>ERROR:<br>No Se pudo abrir el Archivo  \" ".$this->archivo." \"</span>";
		} else {
			return $cadenaAr;
		}
	}
	public function AbrirTexto(){
		return $this->CargaArchivo();
	}
	/**
	*	Función que analiza la estructura del código y le aplica una serie de estilos dependiendo si se trata de una variable, palabra reservada, un comentario o un operador etc.
	*	Devuelve el código del archivo con un formato de colores.
	*/
	private function ParsingString(){
		$this->codigo=$this->CargaArchivo();
		$palabra="";
		$resultado="";
		$iniciaphp= false;
		$iniphp=false;
		$finphp=false;
		$iniciaJS=false;
		$iniJS=false;
		$finJS=false;
		$comillasdoblesJS=false;
		$comillassimplesJS=false;
		$escomillasJS=false;
		$comentariodobleJS=false;
		$comentariosimpleJS=false;
		$comentariosimple=false;
		$comentariodoble=false;
		$comillasdobles=false;
		$comillassimples=false;
		$escomentario=false;
		$escomentariosis=false;
		$esinicioetiqueta=false;
		$esfinetiqueta=false;
		$escomillas=false;
		$html=false;
		for($i=0;$i<strlen($this->codigo);$i++){
			if($iniciaphp){
				if($iniphp){//Si  se trata de la etiqueta de inicio de código PHP.
					if(substr($this->codigo,$i,1)=="\n" || substr($this->codigo,$i,1)=="\s" || substr($this->codigo,$i,1)==" " ){
						$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
						$iniphp=false;
					}else{
						$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
					}
				}else if($finphp){//Si se trata de una etiqueta de fin de PHP la recorre para encontrar un salto de línea o el carácter de cierre >.
					if(substr($this->codigo,$i,1)=="\n" || substr($this->codigo,$i,1)==">"){
						$finphp=false;
						$iniciaphp= false;
						$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
					}else{
						$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
					}
				}else if($comentariosimple){//Si se trata de un comentario Simple se recorre para encontrar un salto de línea.
					if(substr($this->codigo,$i,1)=="\n"){
						$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
						$comentariosimple=false;
					}else{
						$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
					}
				}else if($comentariodoble){//Si se trata de un comentario doble se recorre  hasta encontrar el carácter de Cierre */.
					if(substr($this->codigo,$i,1)=="/"){
						if(substr($this->codigo,$i-1,1)=="*"){
							$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
							$comentariodoble=false;
						}else{
							$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
						}
					}else{
						if(substr($this->codigo,$i,1)=="\n"){
							if(substr($this->codigo,$i-2,2)=="*/"){
								$comentariodoble=true;
								$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
							}else{
								$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
							}
						}else{
							$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
						}
					}
				}else if($comillasdobles){//Si es comillas dobles recorremos la cadena para encontrar la comilla que cierra.
					if(substr($this->codigo,$i,1)=="\""){
						if(substr($this->codigo,$i-1,1)=="\\"){//Ignora las comillas escapadas con \.
							if(substr($this->codigo,$i-2,1)=="\\"){
								$comillasdobles=false;
								$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1)).'</span>';
							}else{
								$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
							}
						}else{
							$comillasdobles=false;
							$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1)).'</span>';
						}
					}else{
						$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
					}
				}else if($comillassimples){//Si es comillas simples recorremos la cadena para encontrar la comilla que cierra.
					if(substr($this->codigo,$i,1)=="'"){
						$comillassimples=false;
						$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1)).'</span>';
					}else{
						$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
					}
				}else if(substr($this->codigo,$i,1)=="/"){//Revisamos si se trata de un comentario simple o doble.
					if(substr($this->codigo,$i+1,1)=="/"){//Es un comentario simple si cumple la condición
						$comentariosimple=true;
						$resultado .= "<span class='comentario'>".$this->Cambiacaracter(substr($this->codigo,$i,1));
					}else if(substr($this->codigo,$i+1,1)=="*"){//Si no es un comentario simple se puede tratar de uno doble por lo cual se busca el *.
						$comentariodoble=true;
						$resultado .= "<span class='comentario'>".$this->Cambiacaracter(substr($this->codigo,$i,1));
					}else if(substr($this->codigo,$i-1,1)=="*"){//Checamos si se trata del cierre del comentario doble */.
						$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
					}else{//Encaso contrario analizamos si la palabra es una variable, una palabra reservada o solo el carácter / y se escribe.
						$resultado .= $this->analisapalabra($palabra,substr($this->codigo,$i,1));
						$palabra ="";
					}
				}else if(substr($this->codigo,$i,1)=="\""){//Determinar si se tratan de comillas dobles.
					$resultado .= "<span class='comillas'>".$this->Cambiacaracter(substr($this->codigo,$i,1));
					$comillasdobles=true;
				}else if(substr($this->codigo,$i,1)=="'"){//Determinar si se tratan de comillas simple.
					$resultado .= "<span class='comillas'>".$this->Cambiacaracter(substr($this->codigo,$i,1));
					$comillassimples=true;
				}else if($this->Separador(substr($this->codigo,$i,1))){//Determinamos si el carácter es un separador de palabras
					if(substr($this->codigo,$i,1)=="?"){//Determinamos si se trata de la etiqueta de Fin de PHP.
						if(substr($this->codigo,$i+1,1)==">"){
							$finphp=true;
							$resultado .= "<span class='etiphp'>". $this->Cambiacaracter(substr($this->codigo,$i,1));
						}else{// Encaso contrario analizamos si la palabra es una variable, una palabra reservada o solo el carácter ? y se escribe.
							$resultado .= $this->analisapalabra($palabra,substr($this->codigo,$i,1));
							$palabra ="";
						}
					}else{
						// Si no cumple con ninguna de las condiciones anteriores solo analizamos si la palabra es una variable,
						// una palabra reservada o solo el carácter separador de palabra y se escribe
						$resultado .= $this->analisapalabra($palabra,substr($this->codigo,$i,1));
						$palabra ="";
					}
				}else{//formamos la palabra
					// Si no se trata de ningún carácter usado para separar palabras, comentario, comillas simples o dobles,
					// inicio y fin de PHP, se van creando las palabras para ser evaluadas como variables o palabras reservadas.
					$palabra .= $this->Cambiacaracter(substr($this->codigo,$i,1));
				}
			}else{//si no es un codigo php.
				if($esinicioetiqueta){//inicio de una etiqueta.
					if($escomillas){
						if(substr($this->codigo,$i,1)=="\""){
							$escomillas=false;
							$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
						}else{
							$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
						}
					}else if(substr($this->codigo,$i,1)==">"){
						if($this->esreservadahtml($palabra)){
							$resultado.= "<span class='reservadahtml'>".$palabra."</span>";
							if(substr($this->codigo,$i,1)=="="){
								$resultado.= "<span class='simbolos'>".$this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
							}else{
								if ($iniciaJS) {
									$resultado .= "<span class='etiquetahtml'>".$this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
								}else{
									$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
								}
							}
							$palabra="";
						}else{
							$resultado.= $palabra;
							if(substr($this->codigo,$i,1)=="="){
								$resultado.= "<span class='simbolos'>".$this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
							}else{
								if ($iniciaJS) {
									$resultado .= "<span class='etiquetahtml'>".$this->Cambiacaracter(substr($this->codigo,$i,1))."</span><span class='etiquetaJS'>";
									//$iniciaJS=false;
								}else{
									$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));

								}
							}
							$palabra="";
						}
						$esinicioetiqueta=false;
					}else if($this->Separadorhtml(substr($this->codigo,$i,1))){
						if($this->esreservadahtml($palabra)){
							$resultado.= "<span class='reservadahtml'>".$palabra."</span>";
							if(substr($this->codigo,$i,1)=="="){
								$resultado.= "<span class='simbolos'>".$this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
							}else{
								$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
							}
							$palabra="";
						}else{
							$resultado.= $palabra;
							if(substr($this->codigo,$i,1)=="="){
								$resultado.= "<span class='simbolos'>".$this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
							}else{
								$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
							}
							$palabra="";
						}
					}else if(substr($this->codigo,$i,1)=="\""){
						$resultado .= "<span class='comillashtml'>".$this->Cambiacaracter(substr($this->codigo,$i,1));
						$escomillas=true;
					}else{
						$palabra .= $this->Cambiacaracter(substr($this->codigo,$i,1));
					}
				}else if($esfinetiqueta){//Si se trata de un cierre de etiqueta, recorremos la cadena hasta encontrar el fin del comentario.2
					if(substr($this->codigo,$i,1)==">"){
						$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
						$esfinetiqueta=false;
					}else{
						if(substr($this->codigo,$i,1)=="\n"){
							$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1))."<br>";
						}else{
							$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
						}
					}
				}else if($escomentariosis){// Si se trata de un comentario de sistema, recorremos la cadena hasta encontrar el fin del comentario.
					if(substr($this->codigo,$i,1)==">"){
						$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
						$escomentariosis=false;
					}else{
						if(substr($this->codigo,$i,1)=="\n"){
							$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1))."<br>";
						}else{
							$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
						}
					}
				}else if($escomentario){// Si se trata de un comentario, recorremos la cadena hasta encontrar el fin del comentario.
					if(substr($this->codigo,$i,1)==">" and substr($this->codigo,$i-1,1)=="-" and substr($this->codigo,$i-2,1)=="-") {
						$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
						$escomentario=false;
					}else{
						if(substr($this->codigo,$i,1)=="\n"){
							$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1))."<br>";
						}else{
							$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
						}
					}
				}else if(substr($this->codigo,$i,1) == "<"){// Encontrando el carácter <, se puede evaluar si se trata de un bloque de código PHP o HTML .
					if(substr($this->codigo,$i+1,1)=="?" and substr($this->codigo,$i+2,1)=="p" and substr($this->codigo,$i+3,1)=="h" and substr($this->codigo,$i+4,1)=="p"){// Analizamos si se trata del inicio de un bloque de código PHP.
						$iniciaphp= true;// Se activa la parte de análisis del bloque PHP.
						$iniphp= true;
						$resultado .= "<span class='etiphp'>".$this->Cambiacaracter(substr($this->codigo,$i,1));
					}else{// Si no es un bloque de código PHP se puede tratar de HTML o JAVASCRIPT.Analizamos la parte de código HTML.
						if(substr($this->codigo,$i+1,1)=="!"){//Revisamos si se trata de un comentario.
							if(substr($this->codigo,$i+2,1)=="-" and substr($this->codigo,$i+3,1)=="-"){//Se determina si es un comentario común
								$escomentario=true;
								$resultado .= "<span class='comentariohtml'>".$this->Cambiacaracter(substr($this->codigo,$i,1)); 
							}else{//De lo contrario pude tratarse de un comentario de sistema..
								$escomentariosis=true;
								$resultado .= "<span class='comentariosis'>".$this->Cambiacaracter(substr($this->codigo,$i,1));
							}
						}else if(substr($this->codigo,$i+1,1)=="s" and substr($this->codigo,$i+2,1)=="c" and substr($this->codigo,$i+3,1)=="r" and substr($this->codigo,$i+4,1)=="i" and substr($this->codigo,$i+5,1)=="p" and substr($this->codigo,$i+6,1)=="t"){//para el codigo JavaScript
							$iniciaJS=true;
							$iniJS=true;
							$esinicioetiqueta=true;
							//$resultado .= "<span class='etiquetaJS'>".$this->Cambiacaracter(substr($this->codigo,$i,1));
							$resultado .= "<span class='etiquetahtml'>".$this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
						}else if(substr($this->codigo,$i+1,1)=="/"){// Determinamos si se trata del fin de la etiqueta.
							$esfinetiqueta=true;
							if ($iniciaJS) {
								$iniciaJS=false;
								$resultado .= "</span><span class='etiquetahtml'>".$this->Cambiacaracter(substr($this->codigo,$i,1));//1818e1
							}else{
								$resultado .= "<span class='etiquetahtml'>".$this->Cambiacaracter(substr($this->codigo,$i,1));//1818e1
							}
							
						}else{
							if(substr($this->codigo,$i+1,1)=="\s" || substr($this->codigo,$i+1,1)==" "){
								if(substr($this->codigo,$i+2,1)=="=" || substr($this->codigo,$i+2,1)==">" || substr($this->codigo,$i+2,1)=="$"
								|| substr($this->codigo,$i+2,1)=="1" || substr($this->codigo,$i+2,1)=="2" || substr($this->codigo,$i+2,1)=="3" || substr($this->codigo,$i+2,1)=="4" || substr($this->codigo,$i+2,1)=="5"
								|| substr($this->codigo,$i+2,1)=="6" || substr($this->codigo,$i+2,1)=="7" || substr($this->codigo,$i+2,1)=="8" || substr($this->codigo,$i+2,1)=="9" || substr($this->codigo,$i+2,1)=="0"){
									$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
								}else{
									$esinicioetiqueta=true;// Si no es ninguna de los anteriores entonces se trata de la etiqueta de inicio.
									$resultado .= "<span class='etiquetahtml'>".$this->Cambiacaracter(substr($this->codigo,$i,1));
								}
							}else{
								if(substr($this->codigo,$i+1,1)=="=" || substr($this->codigo,$i+1,1)==">" || substr($this->codigo,$i+1,1)=="$"
								|| substr($this->codigo,$i+1,1)=="1" || substr($this->codigo,$i+1,1)=="2" || substr($this->codigo,$i+1,1)=="3" || substr($this->codigo,$i+1,1)=="4" || substr($this->codigo,$i+1,1)=="5"
								|| substr($this->codigo,$i+1,1)=="6" || substr($this->codigo,$i+1,1)=="7" || substr($this->codigo,$i+1,1)=="7" || substr($this->codigo,$i+1,1)=="9" || substr($this->codigo,$i+1,1)=="0"){
									$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
								}else{
									$esinicioetiqueta=true;// Si no es ninguna de los anteriores entonces se trata de la etiqueta de inicio.
									$resultado .= "<span class='etiquetahtml'>".$this->Cambiacaracter(substr($this->codigo,$i,1));
								}
							}
						}
					}
				}else{
					if ($iniciaJS) {
						if($comentariosimpleJS){//Si se trata de un comentario Simple se recorre para encontrar un salto de línea.
							if(substr($this->codigo,$i,1)=="\n"){
								$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
								$comentariosimpleJS=false;
							}else{
								$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
							}
						}else if($comentariodobleJS){//Si se trata de un comentario doble se recorre  hasta encontrar el carácter de Cierre */.
							if(substr($this->codigo,$i,1)=="/"){
								if(substr($this->codigo,$i-1,1)=="*"){
									$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
									$comentariodobleJS=false;
								}else{
									$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
								}
							}else{
								if(substr($this->codigo,$i,1)=="\n"){
									if(substr($this->codigo,$i-2,2)=="*/"){
										$comentariodobleJS=true;
										$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
									}else{
										$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
									}
								}else{
									$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
								}
							}
						}else if($comillasdoblesJS){
							if(substr($this->codigo,$i,1)=="\""){
								if(substr($this->codigo,$i-1,1)=="\\"){//Ignora las comillas escapadas con \.
									if(substr($this->codigo,$i-2,1)=="\\"){
										$comillasdoblesJS=false;
										$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1)).'</span>';
									}else{
										$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
									}
								}else{
									$comillasdoblesJS=false;
									$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1)).'</span>';
								}
							}else{
								$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
							}
						}else if($comillassimplesJS){//Si es comillas simples recorremos la cadena para encontrar la comilla que cierra.
							if(substr($this->codigo,$i,1)=="'"){
								$comillassimplesJS=false;
								$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1)).'</span>';
							}else{
								$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
							}
						}else if(substr($this->codigo,$i,1)=="/"){//Revisamos si se trata de un comentario simple o doble.
							if(substr($this->codigo,$i+1,1)=="/"){//Es un comentario simple si cumple la condición
								$comentariosimpleJS=true;
								$resultado .= "<span class='comentariosimpleJs'>".$this->Cambiacaracter(substr($this->codigo,$i,1));
							}else if(substr($this->codigo,$i+1,1)=="*"){//Si no es un comentario simple se puede tratar de uno doble por lo cual se busca el *.
								$comentariodobleJS=true;
								$resultado .= "<span class='comentariosimpleJs'>".$this->Cambiacaracter(substr($this->codigo,$i,1));
							}else if(substr($this->codigo,$i-1,1)=="*"){//Checamos si se trata del cierre del comentario doble */.
								$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
							}else{//Encaso contrario analizamos si la palabra es una variable, una palabra reservada o solo el carácter / y se escribe.
								$resultado .= $this->PalabarsJS($palabra,substr($this->codigo,$i,1));
								$palabra ="";
								//$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
								//$palabra ="";
							}
						}else if(substr($this->codigo,$i,1)=="\""){
							$comillasdoblesJS=true;
							$resultado .= "<span class='comillasJS'>".$this->Cambiacaracter(substr($this->codigo,$i,1));
						}else if(substr($this->codigo,$i,1)=="'"){//Determinar si se tratan de comillas simple.
							$resultado .= "<span class='comillasJS'>".$this->Cambiacaracter(substr($this->codigo,$i,1));
							$comillassimplesJS=true;
						}else if ($this->Separador(substr($this->codigo,$i,1))) {
							$resultado .= $this->PalabarsJS($palabra,substr($this->codigo,$i,1));
							$palabra ="";
						}else{
							$palabra .= $this->Cambiacaracter(substr($this->codigo,$i,1));
						}
						
					}else{
						if ($this->Separador(substr($this->codigo,$i,1))){
							$resultado.= "<span class='simbolos'>".$this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
						}else{
							$resultado.= "<span class='default'>".$this->Cambiacaracter(substr($this->codigo,$i,1))."</span>";
						}
						//$resultado .= $this->Cambiacaracter(substr($this->codigo,$i,1));
					}
					
				}
			}
		}//termina for para recorrer linea.
		return $resultado;
	}
	/**
	*	Función que determina si un carácter es un separador de palabras.
	*/
	private function Separador($caracter){
		$separador= array("\s", "\n", "\r","\t"," ","{","}","(",")","[","]","+",",","-","=","<",">","^","!","¡","|","&","%",":","*","/","?","@",".",";");
		if(in_array($caracter,$separador)){
			return true;
		}else{
			return false;
		}
	}
	/**
	*	Función que determina si un carácter es un separador de palabras para HTML.
	*/
	private function Separadorhtml($caracter){
		$separador= array("\s","\r","\t","="," ");
		if(in_array($caracter,$separador)){
			return true;
		}else{
			return false;
		}
	}
	private function EsReservadaJs($palabra){
		$palabra=strtolower($palabra);
		$palabrasJs=Array("break","delete","if","this","while","case","do","in","throw","with","catch","else","instanceof",
							"try","continue","finally","new","typeof","debugger","for","return","var","default","function",
							"switch","void","document","getelementbyid","focus","length","value","isnan","parseint","window",
							"load","var","navigator","userAgent","Object","Array","prototype","jquery","nodetype","exec","ownerdocument",
							"createelement","parentnode","find","constructor","undefined","call","apply");
		if(in_array($palabra,$palabrasJs)){
			return true;
		}else{
			return false;
		}
	}
	/**
	*	Función que determina si se trata de una palabra reservada de PHP.
	*/
	private function esreservada($palabra){
		$palabra=strtolower($palabra); //Convertimos la cadena en minúsculas para que coincida con las palabras del array.
		$palabras=Array("global", "function", "if", "endif", "else", "elseif", "while", "do", "for", "foreach",
						"break", "continue", "switch", "case", "default", "declare", "return", "include","include_once", "require_once",
						"null","integer","bool","boolean","double","object","unset","binary","define","constant",
						"class","new","extends","private","public","protected","static","const","extends","namespace",
						"__destruct","abstract","interface","implements","parent","as","use","add","clone","echo",
						"print","eval","empty","var","line","die","exit","and","or","xor","throw","try","catch","ltrim","substr","strlen",
						"htmlspecialchars","isset","instanceof","file","fopen","phpinfo","mysql_connect","mysql_query","mysql_select_db",
						"mysql_close","query","mysql_fetch_row","mysql_fetch_array","mysql_fetch_field","mysql_num_row","__construct",
						"mysqli_connect","mysqli","mysqli_close","close","connect_errno","mysqli_connect_errno","connect_error",
						"mysqli_connect_error","sqlstate","mysqli_sqlstate","character_set_name ","mysqli_character_set_name ",
						"real_escape_string","mysqli_real_escape_string","mysqli_query","real_query","mysqli_real_query","multi_query",
						"mysqli_multi_query ","free","mysqli_free_result","free_result","store_result","mysqli_store_result","use_result",
						"mysqli_use_result","data_seek","mysqli_data_seek","ping","mysqli_ping","affected_rows","mysqli_affected_rows",
						"num_rows","mysqli_num_rows","current_field","mysqli_field_tell","fetch_all ","mysqli_fetch_all","fetch_array",
						"mysqli_fetch_array","fetch_assoc","mysqli_fetch_assoc","fetch_field_direct","mysqli_fetch_field_direct","fetch_field",
						"mysqli_fetch_field","fetch_fields","mysqli_fetch_fields","fetch_object","mysqli_fetch_object","fetch_row","mysqli_fetch_row",
						"field_count","mysqli_num_fields","field_seek","mysqli_field_seek","lengths","mysqli_fetch_lengths","commit","mysqli_commit",
						"autocommit","mysqli_autocommit","rollback","mysqli_rollback");//Faltan más palabras reservada y funciones por si no se colorea algún ay que agregarla aquí.
		if(in_array($palabra,$palabras)){
			return true;
		}else{
			return false;
		}
	}

	/**
	*	Función que determina si se trata de una palabra reservada de propiedades.
	*/
	private function esreservadahtml($palabra){
		$palabra=strtolower($palabra); //Convertimos la cadena en minúsculas para que coincida con las palabras del array.
		$palabras=Array("xmlns", "http-equiv", "content", "src", "id", "name", "onchange", "value", "selected","style","type","href","rel",
						"class","rel","rev","size","color","width","height","align","bgcolor","text","vlink","alink","border","alt","ismap",
						"usemap","shape","coords","clear","valign","nowrap","colspan","rowspan","cellspacing","cellpadding","url","target","cols","rows","scrolling",
						"noresize","marginheight","marginwidth","bordercolor","frameborder","framespacing","maxlength","checked","onclick","tabindex","notab","wrap",
						"disabled","multiple","enctype","method","action","hspace","vspace","dynsrc","controls","loop","start","lowsrc","face","function");//Faltan más palabras reservada y funciones por si no se colorea algún ay que agregarla aquí.
		if(in_array($palabra,$palabras)){
			return true;
		}else{
			return false;
		}
	}
	/**
	*	Función que revisa si la palabra es una variable.
	*/
	private function esvariable($palabra){
		$palabra=ltrim($palabra);
		if(substr($palabra,0,1)=="$"){
			return true;
		}else{
			return false;
		}
	}
	/**
	*	Función que agrega el estilo si se trata de una palabra reservada de JavaScript .
	*/
	private function PalabarsJS($palabra,$caracter){
		$resul="";
		if($this->EsReservadaJs($palabra)){// Revisamos se la palabra se trata de una reservada.
			if($caracter=="" || $caracter=="\t" || $caracter=="\n"|| $caracter=="\s"|| $caracter=="\r"){
				if($palabra==''){
					$resul.=$palabra;
				}else{
					$resul.="<span class='reservadaJS'>".$palabra."</span>";
				}
				$resul.= $this->Cambiacaracter($caracter);
				$palabra ="";
				return $resul;
			}else{
				if($palabra==''){
					$resul.=$palabra;
				}else{
					$resul.="<span class='reservadaJS'>".$palabra."</span>";
				}
				if($caracter=="" || $caracter=="\t" || $caracter=="\n"|| $caracter=="\s"|| $caracter=="\r"){
					$resul.= $this->Cambiacaracter($caracter);
				}else{
					$resul.= "<span class='simbolosJS'>".$this->Cambiacaracter($caracter)."</span>";
				}
				$palabra ="";
				return $resul;
			}
		}else{ // Si no es una variable ni una palabra reservada.
			if($caracter=="\t" || $caracter==" " || $caracter=="\s" || $caracter=="\n" || $caracter=="\r"){
				if($palabra==''){
					$resul.=$palabra;
				}else{
					$resul.="<span class='defaultJS'>".$palabra."</span>";
				}
				$resul.= $this->Cambiacaracter($caracter);
				$palabra ="";
				return $resul;
			}else{
				if($palabra==""){
					$resul.=$palabra;
				}else{
					$resul.="<span class='defaultJS'>".$palabra."</span>";
				}
				if($caracter=="" || $caracter=="\t" || $caracter=="\n"|| $caracter=="\s"|| $caracter=="\r"){
					$resul.= $this->Cambiacaracter($caracter);
				}else{
					$resul.= "<span class='simbolosJS'>".$this->Cambiacaracter($caracter)."</span>";
				}
				$caracter="";
				$palabra ="";
				return $resul;
			}
		}
	}
	/**
	*	Función que agrega el estilo si se trata de una variable, palabra reservada o si no es ninguna de las dos anteriores.
	*/
	private function analisapalabra($palabra,$caracter){
		$resul="";
		if($this->esvariable($palabra)){// Revisamos si la palabra es una variable.
			if($caracter=="" || $caracter=="\t" || $caracter=="\n"|| $caracter=="\s"|| $caracter=="\r"){
				if($palabra==''){
					$resul.=$palabra;
				}else{
					$resul.="<span class='default'>".$palabra."</span>";
				}
				$resul.= $this->Cambiacaracter($caracter);
				$palabra ="";
				return $resul;
			}else{
				if($palabra==''){
					$resul.=$palabra;
				}else{
					$resul.="<span class='variable'>".$palabra."</span>";
				}
				if($caracter=="" || $caracter=="\t" || $caracter=="\n"|| $caracter=="\s"|| $caracter=="\r"){
					$resul.= $this->Cambiacaracter($caracter);
				}else{
					$resul.= "<span class='simbolos'>".$this->Cambiacaracter($caracter)."</span>";
				}
				$palabra ="";
				return $resul;
				}
		}else if($this->esreservada($palabra)){// Revisamos se la palabra se trata de una reservada.
			if($caracter=="" || $caracter=="\t" || $caracter=="\n"|| $caracter=="\s"|| $caracter=="\r"){
				if($palabra==''){
					$resul.=$palabra;
				}else{
					$resul.="<span class='reservada'>".$palabra."</span>";
				}
				$resul.= $this->Cambiacaracter($caracter);
				$palabra ="";
				return $resul;
			}else{
				if($palabra==''){
					$resul.=$palabra;
				}else{
					$resul.="<span class='reservada'>".$palabra."</span>";
				}
				if($caracter=="" || $caracter=="\t" || $caracter=="\n"|| $caracter=="\s"|| $caracter=="\r"){
					$resul.= $this->Cambiacaracter($caracter);
				}else{
					$resul.= "<span class='simbolos'>".$this->Cambiacaracter($caracter)."</span>";
				}
				$palabra ="";
				return $resul;
			}
		}else{ // Si no es una variable ni una palabra reservada.
			if($caracter=="\t" || $caracter==" " || $caracter=="\s" || $caracter=="\n" || $caracter=="\r"){
				if($palabra==''){
					$resul.=$palabra;
				}else{
					$resul.="<span class='default'>".$palabra."</span>";
				}
				$resul.= $this->Cambiacaracter($caracter);
				$palabra ="";
				return $resul;
			}else{
				if($palabra==""){
					$resul.=$palabra;
				}else{
					$resul.="<span class='default'>".$palabra."</span>";
				}
				if($caracter=="" || $caracter=="\t" || $caracter=="\n"|| $caracter=="\s"|| $caracter=="\r"){
					$resul.= $this->Cambiacaracter($caracter);
				}else{
					$resul.= "<span class='simbolos'>".$this->Cambiacaracter($caracter)."</span>";
				}
				$caracter="";
				$palabra ="";
				return $resul;
			}
		}
	}
	/**
	*	Función que permite cambiar los caracteres especiales a su correspondiente código HTML.
	*/
	private function Cambiacaracter($caracter){
		if($caracter=="<"){
			return "&lt;";
		}else if($caracter==">"){
			return "&gt;";
		}else if($caracter==" "){
			return "&nbsp;";
		}else if($caracter=="\t"){
			return "&nbsp;&nbsp;&nbsp;";
		}else if($caracter=="\s"){
			return "&nbsp;";
		}else if($caracter=="\n"){
			return "<br/>";
		}else if($caracter=="á"){
			return "&aacute;";
		}else if($caracter=="Á"){
			return "&Aacute;";
		}else if($caracter=="é"){
			return "&eacute;";
		}else if($caracter=="É"){
			return "&Eacute;";
		}else if($caracter=="í"){
			return "&iacute;";
		}else if($caracter=="Í"){
			return "&Iacute;";
		}else if($caracter=="ó"){
			return "&oacute;";
		}else if($caracter=="Ó"){
			return "&Oacute;";
		}else if($caracter=="ú"){
			return "&uacute;";
		}else if($caracter=="Ú"){
			return "&Uacute;";
		}else if($caracter=="ñ"){
			return "&ntilde;";
		}else if($caracter=="Ñ"){
			return "&Ntilde;";
		}else if($caracter=="¡"){
			return "&iexcl;";
		}else if($caracter=="&"){
			return "&amp;";
		}else if($caracter=="®"){
			return "&reg;";
		}else if($caracter=="©"){
			return "&copy;";
		}else if($caracter=="€"){
			return "&euro;";
		}else if($caracter=="ü"){
			return "&uuml;";
		}else if($caracter=="Ü"){
			return "&Uuml;";
		}else if($caracter=="¿"){
			return "&iquest;";
		}else{
			return $caracter;
		}
	}
	public function ValidaExtencions(){
		return substr(strrchr($this->archivo, '.'), 1);
	}
	/**
	*	Función para mostrar el código la sintaxis formateada con los estilos del lenguaje PHP.
	*/
	public function MostrarSintaxisCodigo(){
		return $this->ParsingString();
	}
}

/*$codigo = new SintaxisHJP();
$codigo->archivo="prueba/pruebaPHP.php";
//$codigo->MostrarCodigoPhp();
//$codigo->archivo="prueba/pruebaHTML.html";
echo "<code>".$codigo->MostrarSintaxisCodigo()."</code>";
<link rel="stylesheet" type="text/css" href="css/codigo.css">
*/
 ?>
 