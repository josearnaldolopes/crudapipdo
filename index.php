<?php
$page = $_GET["page"];
require('crud.php');

$crud = new Crud;

date_default_timezone_set('America/Sao_Paulo');

header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');

switch ($page) {
    case 'custom':
        $output = $crud->custom('SELECT link.id,link.link,link.titulo,link.descricao,link.categoria,link.hashtag,link.imagem,link.clicks,link.favorito,link.data FROM link ORDER BY link.id DESC LIMIT 7');
        // $output = $crud->custom('SELECT * FROM busca ORDER BY busca.id DESC LIMIT 4');
        // $output = $crud->custom('SELECT * FROM busca WHERE busca.id = 3');
        // $output = $conection->custom('call getCategoria');
        break;
    case 'select':
        $output = $crud->select('glossario', 'glossario.termo LIKE \'p%\'', '5');
        break;
    case 'insert':
        $output = $crud->insert('nota', array('titulo' => "Aqui vai um titulo", "nota" => "Texto Interno"));
        break;
    case 'update':
        $output = $crud->update('nota', array('titulo' => "Oh", "nota" => "<script>function funcao1(){alert(\Eu sou um alert!\");}</script>"), 'id = 46');
        // $output = $crud->update('nota', array('titulo' => "Título <b>bold</b> e <i>Itálico</i>", "nota" => "<script>function funcao1(){alert(\Eu sou um alert!\");}</script><h1>Titulo H1</h1>"), 'id = 44');
        break;
        case 'delete':
        $output = $crud->delete('nota', 'id = 50');
        break;
    default:
    $output = array('apipdo' => 'Crud em API usando PDO');
}

$json = array('apipdo' => $output, 'total' => $crud->count($output), 'date' => date("Y-m-d H:i:s"), 'return' => true);

echo json_encode($json);
