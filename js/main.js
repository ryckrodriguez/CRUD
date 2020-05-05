$(document).ready(function(){
	logar();
	adicionarFuncionario();
	cadastrarUsuario()
});

function editar(id) {
	$.ajax({
		url: "select.php?id=" + id,
		type: "get",
		success: function(dados){
			var retorno = JSON.parse(dados);
			console.log(retorno);
			if(!retorno.error){
				initMap(retorno.lat, retorno.lng);
				toastr['success'](retorno.msg);
				var tipo = `<option value="${retorno.tipo}" selected> ${retorno.tipo} </option>`;
				var responsavel = `<option value="${retorno.responsavel}" selected> ${retorno.responsavel} </option>`;
				$("#tipo").prepend(tipo);
				$("#responsavel").prepend(responsavel);
				$("#id").val(id);
				$("#tipo").val(retorno.tipo);
				$("#responsavel").val(retorno.responsavel);
				$("#descricao").val(retorno.descricao);
				$("#input_lat").val(retorno.lat);
				$("#input_lng").val(retorno.lng);
				$("#resetIncidente").val("Limpar formulário");	
				$("#editarIncidente, #resetIncidente").css("display", "block");
				$("#salvarIncidente, #opresponsavel, #optipo").css("display", "none");
				console.log(retorno);
			} else {
				console.log(retorno);
				toastr['error'](retorno.msg);
			}
		}
	});
}

function excluir(id) {
    var confirmar = confirm('ID: ' + id + '\nDeseja mesmo excluir este incidente?');
    if(confirmar == false) {
        toastr["info"]('Cancelado!');
    } else {
        $.ajax({
			url: "excluir.php?id=" + id,
			type: "get",
			success: function (result) {
				let retorno = JSON.parse(result);
				if(!retorno.error) {
					removeLinha(id, retorno.msg);
				} else {
					event.preventDefault();
					toastr["error"](retorno.msg);
				}
			}
		});
    }
}

function removeLinha(id, msg) {
	event.preventDefault();
	var linha = $("#" + id);
	linha.fadeOut(1000);
	setTimeout(function(){
		toastr["success"](msg);
		linha.remove();
	}, 1000);
}

function cadastrarUsuario() {
	$("#cad_usuario").submit(function(event){
		event.preventDefault();
		var usuario = $("#usuario").val();
		var telefone = $("#telefone").val();
		var email = $("#email").val();
		var senha = $("#senha").val();
		$.ajax({
			url: "cad_usuario.php",
			type: "POST",
			data: {usuario: usuario, telefone: telefone,
			email: email, senha: senha},
			success: function (result) {
				let retorno = JSON.parse(result);
				console.log(typeof(retorno));
				console.log(retorno);
				if(!retorno.error){
					toastr["success"](retorno.msg);
					setTimeout(function(){
						location.href= 'login.html';
					}, 1000);
				} else {
					$("#cad_usuario").each(function(){
						this.reset();
					});
					toastr["error"](retorno.msg);
				}
			}
		});
	});
}

function logar() {
	$('#formLogin').submit(function(event){ 	//Ao submeter formulário
		event.preventDefault();
		var login=$('#login').val();	//Pega valor do campo email
		var senha=$('#senha').val();	//Pega valor do campo senha
		$.ajax({			//Função AJAX
			url:"login.php",			//Arquivo php
			type:"post",				//Método de envio
			data: "login="+login+"&senha="+senha,	//Dados
			   success: function (result){			//Sucesso no AJAX
				let retorno = JSON.parse(result);
				if(!retorno.error){			
					toastr["success"](retorno.msg);
					setTimeout(function(){
						location.href='index.php'	//Redireciona
					}, 1000);
				}else{
					toastr["error"](retorno.msg);		//Informa o erro
					$("#formLogin input").val(""); // Limpa os campos
					$("#formLogin input[type=submit").val("Enviar"); // Identifica o botão como enviar
				}
			}
		});
	});
}

function adicionarIncidente() {
	$("#editarIncidente, #resetIncidente").css("display", "none");
	$("#salvarIncidente").css("display", "block");
	$("#modalIncidentes").each(function(){
		this.reset();
	});
	initMap(-23.550667428085106, -46.632885610975634);
	$("#modalIncidentes").submit(function(event) {
		event.preventDefault();
		var tipo = $("#tipo").val();
		var responsavel = $("#responsavel").val();
		var lat = $("#input_lat").val();
		var lng = $("#input_lng").val();
		var descricao = $("#descricao").val();
		$.ajax({
			url: "formulario.php",
			type: "get",
			data: "tipo="+tipo+
			"&responsavel="+responsavel+
			"&input_lat="+lat+"&input_lng="+lng+
			"&descricao="+descricao,
			success: function (result) {
				let retorno = JSON.parse(result);
				if(!retorno.error) {
					console.log(retorno);
					let linha = `<tr id="${retorno.id}" >
									<th scope="row"> ${retorno.id} </th>
									<th> ${responsavel} </th>
									<td> ${tipo}</td>
									<td> ${descricao}</td>
									<td> ${retorno.data}</td>
									<td> <buttom class="btn btn-outline-dark" onclick="editar(${retorno.id})" data-toggle="modal" data-target="#addmodal">
									<i class="fas fa-edit"></i>
									</buttom> </td>
									<td> <buttom class="btn btn-outline-dark" onclick="excluir(${retorno.id})"> 
									<i class="fas fa-trash-alt"></i>
									</buttom> </td>
								</tr>`
					$("#corpoTabela").prepend(linha);
					$("#modalIncidentes").each(function(){
						this.reset();
					});
					initMap(-23.550667428085106, -46.632885610975634);
					toastr["success"](retorno.msg);
				} else {
					toastr["error"](retorno.msg);
				}
			}
		});
	});
}

function adicionarFuncionario() {
	$("#formulario_funcionario").submit(function(event){
		event.preventDefault();
		var nome = $("#nome").val();
		var sobrenome = $("#sobrenome").val();
		var telefone = $("#telefone").val();
		var email = $("#email").val();
		var data_nascimento = $("#data_nascimento").val();
		var sexo = $("input[name='sexo']:checked").val();
		$.ajax({
			url: "cad_funcionario.php",
			type: "get", 
			data: "nome="+nome+"&sobrenome="+sobrenome+
			"&telefone="+telefone+"&email="+email+
			"&data_nascimento="+data_nascimento+"&sexo="+sexo,
			success: function (result) {
				let retorno = JSON.parse(result);
				if(!retorno.error){
					let option = `<option value="${nome} ${sobrenome}" >${nome} ${sobrenome}</option>` 
					$("#responsavel").append(option);
					toastr["success"](retorno.msg);
					$("#formulario_funcionario").each(function(){
						this.reset();
					});
					console.log(retorno);
				} else {
					toastr["error"](retorno.msg);
					console.log(retorno);
				}
			}
		});
	});
}

function procEditarIncidente() {
	var id = $("#id").val();
	var tipo = $("#tipo").val();
	var responsavel = $("#responsavel").val();
	var lat = $("#input_lat").val();
	var lng = $("#input_lng").val();
	var descricao = $("#descricao").val();
	$.ajax({
		url: "proc_editar.php",
		type: "get",
		data: "id=" + id + "&tipo=" + tipo + "&responsavel=" + responsavel +
		"&input_lat=" + lat + "&input_lng=" + lng + 
		"&descricao=" + descricao,
		success: function (result) {
			let resultado = JSON.parse(result);
			console.log(resultado);
			if(!resultado.error) {
				$("#tipo"+id).text(tipo);
				$("#responsavel"+id).text(responsavel);
				$("#descricao"+id).text(descricao);
				$("#modalIncidentes").each(function(){
					this.reset();
				});
				initMap(-23.550667428085106, -46.632885610975634);
				toastr["success"]('Incidente editado com sucesso!');
			} else {
				toastr["error"]('Não foi possível editar o Incidente!');
			}
		}
	});
}

function leitura(id) {
	$.ajax({
		url: "select.php?id="+id,
		type: "get",
		success: function(dados){
			let retorno = JSON.parse(dados);
			console.log(retorno);
			if(!retorno.error){
				toastr["success"](retorno.msg);
				$("#leituraModalTitle").text("ID: " + id + " - Tipo: " + retorno.tipo);
				$("#nomeLeitura").text(retorno.responsavel);
				$("#descricaoLeitura").text(retorno.descricao);
			} else {
				toastr["error"](retorno.msg);
			}
		}
	});
}