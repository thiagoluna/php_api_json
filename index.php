<?php
  //Se caminho errado, mensagem de erro
  if(!array_key_exists('path', $_GET)){
    echo 'Erro. Path não encontrado.';
    exit;
  }

  //Pegar a url e quebrar na barra
  $path = explode('/', $_GET['path']);

  //echo 'path: '.$path[0].' - ';
  if(count($path)==0 || $path[0] == ""){
    echo 'Erro. Path não encontrado.';
    exit;
  }

  $param1 = "";
  if(count($path)>1){
    $param1 = $path[1];
  }

  $contents = file_get_contents('db.json');

  //usar o true pra trazer um array
  $json = json_decode($contents, true);

  //Verificar o método que está sendo usado
  $method = $_SERVER['REQUEST_METHOD'];

  header('Content-type: application/json');
  $body = file_get_contents('php://input');

  //Funcao pra procurar um elemento no json
  function findById($vector, $param1){
    $encontrado = -1;
    foreach($vector as $key => $obj){          
      if($obj['id'] == $param1){
        $encontrado = $key;
        break;
      }
    }
    return $encontrado;
  }

  //Se o metodo for GET retorna o json
  if($method === 'GET'){
    if($path[0] == 'all'){
      echo json_encode($json);
    //se tem a parte inicial (series)
    }else if($json[$path[0]]){
      //Se não tiver parametro retorna tudo  
      if($param1==""){
          echo json_encode($json[$path[0]]);
        }else{          
          $encontrado = findById($json[$path[0]], $param1);
          //Tem parametro e mostra dados relacionados a ele
          if($encontrado>=0){
            echo json_encode($json[$path[0]][$encontrado]);
          }else{
            echo 'ERRO.';
            exit;
          }
        }
    }else{
      echo '[ ]';
    }
  }

  //Método pra inserir dados
  if($method === 'POST'){
    $jsonBody = json_decode($body, true);
    //Colocar id pra poder referenciar
    $jsonBody['id'] = time();
   
    //Se não existir criar um novo e grava nele
    if(!$json[$path[0]]){
      $json[$path[0]] = [];
    }
    $json[$path[0]][] = $jsonBody;
    echo json_encode($jsonBody);
    file_put_contents('db.json', json_encode($json));
  }

  //Método pra deletar dados no json
  if($method === 'DELETE'){
    if($json[$path[0]]){
      if($param1==""){
        echo 'error';
      }else{
        $encontrado = findById($json[$path[0]], $param1);
        if($encontrado>=0){
          echo json_encode($json[$path[0]][$encontrado]);
          unset($json[$path[0]][$encontrado]);
          //Excluir do arquivo json
          file_put_contents('db.json', json_encode($json));
        }else{
          echo 'ERROR.';
          exit;
        }
      }
    }else{
      echo 'error.';
    }
  }

  if($method === 'PUT'){
    if($json[$path[0]]){
      if($param1==""){
        echo 'error';
      }else{
        $encontrado = findById($json[$path[0]], $param1);
        if($encontrado>=0){
          $jsonBody = json_decode($body, true);
          $jsonBody['id'] = $param1;
          $json[$path[0]][$encontrado] = $jsonBody;
          echo json_encode($json[$path[0]][$encontrado]);
          file_put_contents('db.json', json_encode($json));
        }else{
          echo 'ERROR.';
          exit;
        }
      }
    }else{
      echo 'error.';
    }
  }