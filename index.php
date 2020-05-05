<?php 
session_start();
include('conexao.php');

    if(!isset($_SESSION["login"])){
        header('location: login.html');
        exit();
    }

    $pagina_atual = filter_input(INPUT_GET, 'pagina', FILTER_SANITIZE_NUMBER_INT);
    $pagina = (!empty($pagina_atual)) ? $pagina_atual : 1;
    
    $qtd_result_pag = 40;

    $inicio = ($qtd_result_pag * $pagina) - $qtd_result_pag;
    
    //Query de incidentes
    $query_select_incidentes = "SELECT * FROM t_incidentes ORDER BY id DESC LIMIT $inicio, $qtd_result_pag";
    $select_incidentes = mysqli_query($connect, $query_select_incidentes);

    //Query de funcionários
    $query_select_funcionarios = "SELECT nome, sobrenome FROM t_funcionarios";
    $select_funcionarios = mysqli_query($connect, $query_select_funcionarios);

?>

<!DOCTYPE html>

<html lang="pt-br">
    <head>
        <title>Incidentes</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" 
        integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
        <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC4i2xoql01UUnZ0Gckx5NZa-J1p0DrsUQ&callback=initMap">
        </script>       
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
        <script src="js\main.js"></script>
        

        <script>

            jQuery(document).ready(function(){
                $("#pesquisar").keypress(function(){
                    if($(this).val().length > 3) {
                        var dados = $(this).val();
                        $("#corpoTabela tr").each(function(){
                            if($(this).text().toUpperCase().indexOf(dados.toUpperCase()) < 0) {
                                $(this).fadeOut("slow");
                            }
                        });
                    }
                    if($(this).val().length == 0){
                        $("#corpoTabela tr").each(function(){
                            $(this).fadeIn("slow");
                        })
                    }
                });

                jQuery('#input_busca_endereco').keypress(function() {
                    if (jQuery(this).val().length > 5)  {
                        var dados = {"text": jQuery(this).val()}
                        jQuery.post('https://maps.googleapis.com/maps/api/geocode/json?address='+$(this).val()+'&key=AIzaSyBSzkoViMnQSUwUICcklWEQ884Jb2pljeo',dados, function(data){
                        console.log(data);
                        var dados = data.results[0];
                        // jQuery(this).val(dados.address_components.formatted_address);
                        var latitude = dados.geometry.location.lat;
                        var longitude = dados.geometry.location.lng;
                        var position = {lat:parseFloat(latitude),lng:parseFloat(longitude)}
                        map.setCenter(position);
                        map.setZoom(20);
                        },'json');
                    }
                });
            });

            var map;
            var marker;
            var infowindow;
            var markerExist;
            function initMap(myLat, myLng) {
                var myLatLng = new google.maps.LatLng({lat: parseFloat(myLat), lng: parseFloat(myLng)});
                map = new google.maps.Map(document.getElementById('map'), {
                    center: myLatLng,
                    zoom: 13,
                    zoomControl: false,
                    scaleControl: false,
                    streetViewControl: false,
                    fullscreenControl: false
                });
                markerExist = false;
                
                google.maps.event.addListener(map, 'click', function(event) {
                    placeMarker(map, event.latLng);    
                });


                function placeMarker(map, location) {
                    if(markerExist == false) {
                        marker = new google.maps.Marker({
                            position: location,
                            map: map,
                            draggable: true,
                            editable: true
                        });
                        markerExist = true;
                        EditLocation(map, location);
                        function EditLocation(map, location) {
                            infowindow = new google.maps.InfoWindow({
                                content: 'Latitude: ' + location.lat() +
                                '<br>Longitude: ' + location.lng()
                            });
                            infowindow.open(map,marker);
                            document.getElementById("input_lat").value = marker.getPosition().lat();
                            document.getElementById("input_lng").value = marker.getPosition().lng();
                            
                        }
                        google.maps.event.addListener(marker, 'dragend', function (event) {  
                            EditLocation(map, event.latLng);
                        });
                    }
                }
            }

        </script>

        <!-- jQuery de pesquisa dinâmica na tabela 
        <script> 
            jQuery(document).ready(function(){ 
                jQuery("#tabela_incidentes").DataTable();
            }) 
        </script>
        -->

        <style>
            tbody tr:nth-child(1n):hover {
                background-color: rgb(230, 230, 260);
            }
            #input_busca_endereco {
                position: absolute;
                right: 20px;
                top: 115px;
                z-index: 2;
                width: 175px;
                border-radius: 30px;
                font-family: "FontAwesome";
            }
            #map {
                height: 200px;
                width: 100%;
                margin: 5px;
            }
            #formulario_funcionario input {
                margin-top: 10px;
            }
            .display {
                display: none;
            }
            footer {
                text-align: center;
                color: snow;
                background-color: rgb(0,10,10);
                padding: 25px;
                margin: 75px 0px 0px;
            }

        </style>
    </head>
    <body>
        <header>
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <h1><li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Cadastro de incidentes
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <button class="dropdown-item" data-toggle="modal" data-target="#funcionario_modal">
                                <i class="fas fa-address-card"></i> Cadastrar funcionário</button>
                                <a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Sair</a>
                            </div>
                        </li></h1>
                    </ul>
                    <form action="pesquisar.php" method="GET" class="form-inline">
                        <input name="pesquisar" id="pesquisar" class="form-control mr-sm-2" type="search" placeholder="Pesquisar..." aria-label="Search" required>
                        <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </nav>
        </header>

        <main>
            <div class="container" style="margin: 75px auto">
                <div class="row">
                    <caption><h3><b>Meus incidentes</b></h3></caption>
                    
                    <button style="margin-left: 70%; margin-bottom: 15px; border-radius: 30px;"
                        type="button" class="btn btn-primary" onclick="adicionarIncidente()" data-toggle="modal" data-target="#addmodal" >
                        <i class="fas fa-plus"></i>
                    </button>
                </div>

                <table class="table table-striped" id="tabela_incidentes">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Responsavel</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Descrição</th>
                            <th scope="col">Data</th>
                            <th scope="col">Editar</th>    
                            <th scope="col">Excluir</th>
                        </tr>
                    </thead>
                    <tbody id="corpoTabela">
                    <?php while($array_incidentes = mysqli_fetch_array($select_incidentes)) { ?> 
                        <tr id="<?php echo $array_incidentes["id"]; ?>" >
                            <th data-toggle="modal" data-target="#LeituraModal" onclick="leitura(<?php echo $array_incidentes['id']; ?>)" id="id<?php echo $array_incidentes["id"]; ?>" scope="row"> <?php echo $array_incidentes["id"]; ?> </th>
                            <th data-toggle="modal" data-target="#LeituraModal"  onclick="leitura(<?php echo $array_incidentes['id']; ?>)" id="responsavel<?php echo $array_incidentes["id"]; ?>"> <?php echo $array_incidentes["responsavel"]; ?> </th>
                            <td data-toggle="modal" data-target="#LeituraModal"  onclick="leitura(<?php echo $array_incidentes['id']; ?>)" id="tipo<?php echo $array_incidentes["id"]; ?>"> <?php echo $array_incidentes["tipo"]; ?> </td>
                            <td data-toggle="modal" data-target="#LeituraModal"  onclick="leitura(<?php echo $array_incidentes['id']; ?>)" id="descricao<?php echo $array_incidentes["id"]; ?>"> <?php echo $array_incidentes["descricao"]; ?> </td>
                            <td data-toggle="modal" data-target="#LeituraModal"  onclick="leitura(<?php echo $array_incidentes['id']; ?>)" id="data<?php echo $array_incidentes["id"]; ?>"> <?php echo date("d/m/Y H:i", strtotime($array_incidentes["data_incidente"])); ?> </td>
                            <td> <buttom class="btn btn-outline-dark" onclick="editar(<?=$array_incidentes['id'];?>)" data-toggle="modal" data-target="#addmodal">
                            <i class="fas fa-edit"></i>
                            </buttom> </td>
                            <td> <buttom class="btn btn-outline-dark" onclick="excluir(<?=$array_incidentes['id'];?>)"> 
                            <i class="fas fa-trash-alt"></i>
                            </buttom> </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php
                $result_pg = "SELECT COUNT(id) AS num_result FROM t_incidentes";
                $resultado_pg = mysqli_query($connect, $result_pg);
                $row_pg = mysqli_fetch_assoc($resultado_pg);

                
                $qtd_pag = ceil($row_pg['num_result'] / $qtd_result_pag);
                $max_pg = 1; 
                
                
                echo "Página $pagina de $qtd_pag - Total de " . $row_pg['num_result'] . " incidentes cadastrados.";
                ?>
                    <nav style="margin-top: 10px;" aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item">
                            <a class="page-link" href="index.php?pagina=1" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                            </li>
                            <?php for($pag_ant = $pagina - $max_pg; $pag_ant <= $pagina - 1; $pag_ant++) { 
                                if($pag_ant >= 1) {?>
                            <li class="page-item"><a class="page-link" href="index.php?pagina=<?=$pag_ant ?>"><?=$pag_ant ?></a></li>
                            <?php }
                            }?>
                            <li class="page-item"><a class="page-link" ><?=$pagina ?></a></li>
                            <?php for($pag_pos = $pagina + 1; $pag_pos <= $pagina + $max_pg; $pag_pos++) {
                                if($pag_pos <= $qtd_pag) { ?>
                            <li class="page-item"><a class="page-link" href="index.php?pagina=<?=$pag_pos ?>"><?=$pag_pos ?></a></li>
                            <?php }
                            } ?>
                            <li class="page-item">
                            <a class="page-link" href="index.php?pagina=<?=$qtd_pag ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <!-- Inicio modal cadastro de incidentes -->
            <div class="modal fade" id="addmodal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Cadastrar incidente</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="modalIncidentes">
                            <div class="form-group">
                                <input type="hidden" name="id" id="id">

                                <select name="tipo" id="tipo" class="form-control" style="margin: 5px;" required>
                                    <option id="optipo" value="" disabled selected>Tipo</option>
                                    <option value="Acidente" >Acidente</option>
                                    <option value="Carro quebrou" >Carro quebrou</option>
                                    <option value="Colisao" >Colisão</option>
                                    <option value="Outro">Outro...</option>
                                </select>
                                
                                <select name="responsavel" id="responsavel" class="form-control" style="margin: 5px;" required>
                                    <option id="opresponsavel" value="" disabled selected>Responsável</option>
                                    <?php while($array_funcionarios = mysqli_fetch_array($select_funcionarios)) { ?>
                                    <option value="<?=$array_funcionarios['nome']; ?> <?=$array_funcionarios['sobrenome']; ?>" >
                                        <?=$array_funcionarios['nome']; ?> <?=$array_funcionarios['sobrenome']; ?> 
                                    </option>
                                    <?php } ?>
                                </select>

                                <div>
                                    <input type="search" class="form-control" name="input_busca_endereco" id="input_busca_endereco" placeholder=" Procurar...">
                                    <div name="map" id="map" ></div>
                                    <input type="hidden" name="input_lat" id="input_lat" required>
                                    <input type="hidden" name="input_lng" id="input_lng" required>
                                </div>

                                <textarea name="descricao" id="descricao" class="form-control" style="margin: 5px; height: 140px;" placeholder="Descrição" required></textarea>
                                <input type="reset" id="resetIncidente" style="margin: 5px 5px 5px auto;" class="btn btn-dark">
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" id="salvarIncidente" class="btn btn-primary">Salvar incidente</button>
                                    <button type="button" id="editarIncidente" onclick="procEditarIncidente()" class="btn btn-primary">Editar incidente</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    </div>
                </div>
            </div>
            <!-- Fim modal cadastro incidentes -->

            <!-- Início modal cadastro de funcionários -->
            <div class="modal fade" id="funcionario_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Cadastrar funcionário</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="formulario_funcionario">
                                <div class="form-group">
                                    <input type="text" name="nome" id="nome" class="form-control" placeholder="Nome" required>
                                    <input type="text" name="sobrenome" id="sobrenome" class="form-control" placeholder="Sobrenome" required>
                                    <input type="tel" name="telefone" id="telefone" class="form-control" placeholder="Telefone" required>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                                    <input type="date" style="margin-bottom: 15px" name="data_nascimento" id="data_nascimento" class="form-control" placeholder="Data de Nascimento" required>
                                    <fieldset class="form-group">
                                        <div class="row">
                                            <legend class="col-form-label col-sm-2 pt-0">Sexo</legend>
                                            <div class="col-sm-10">
                                                <div class="form-check">
                                                    <label>
                                                        <input class="form-check-input" type="radio" name="sexo" id="masculino" value="M">
                                                        Masculino
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <label>
                                                        <input class="form-check-input" type="radio" name="sexo" id="feminino" value="F">
                                                        Feminino 
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Cadastrar funcionário</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Fim modal cadastro de funcionarios -->

            <!-- Início modal de leitura incidentes -->
            <div class="modal fade" id="LeituraModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="leituraModalTitle">ID - TIPO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h6 id="nomeLeitura">Nome</h6>
                        <p id="descricaoLeitura"> Descrição... </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    </div>
                    </div>
                </div>
            </div>
            <!-- fim modal de leitura incidente -->
        </main>

        <footer>
            <img src="imagens\global_dotcom_branco.png" style="width: 20%; margin: 20px;" alt="Logo GDC">
            <p><i class="far fa-copyright"></i> Copyright GDC - 2020</p>
        </footer>
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    </body>
</html>