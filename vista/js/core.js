$(function(){
	$("#scrolldecaja").scroll();
	$("#MenuArbol").scroll();
	cargaArbolinicio();
	$("#cargarDir").on("click",function(){
		var dir=$("#buscar").val();
		CargaArbol(dir);
	});
});
function CargaArbol(directorio){
	var datosd= "dir="+directorio;
	$.ajax({
		dataType: "html",
		type: "POST",
		data:datosd,
		url: "Controlador/LeeDirectorio.php",
		beforeSend:inicioEnvio(directorio),
		success: function(datos){
			$("#MenuArbol").html(datos);
			$(".documento").on("click",function(){
				CargaCodigo($(this).attr("dir"),$(this).attr("archi"));
			})
		}
	});
}
function CargaCodigo(directorio,archivo){
	var datosd= "dir="+directorio+"&archi="+archivo;
	$.ajax({
		dataType: "html",
		type: "POST",
		data:datosd,
		url: "Controlador/CargaCodigo.php",
		beforeSend:inicioCargaCodigo(archivo),
		success: function(datos){
			$("#codigo").html(datos);
		}
	});
}
function cargaArbolinicio(){
	//$("#MenuArbol").html('<h5>Indique el directorio a mostrar..</h5>');
	CargaArbol("inicio");//Indicar el argumento como inicio mostrara el directorio root del server.
}function inicioCargaCodigo(archi){
  $("#codigo").html('<h5>Cargando código del archivo: '+archi+'</h5><img src="vista/imagenes/cargando1.gif">');
}
function inicioEnvio(dir){
  $("#MenuArbol").html('<h5>Cargando directorio: '+dir+'</h5><img src="vista/imagenes/cargando1.gif">');
}