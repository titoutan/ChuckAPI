<?php 
require_once('functions.php');
$http_method = $_SERVER['REQUEST_METHOD'];
switch ($http_method){
  case 'OPTIONS' : 
    deliver_response(204,"");
  case 'GET' :
    //Récupération des données dans l’URL
    if(isset($_GET['id']))
    {
      $id=htmlspecialchars($_GET['id']);
      $data = getData($id);
      if (empty($data) ) {
        deliver_response(404, 'NOT FOUND');
        die();
      }
    }
    else {
      $data = getData(null);
    }
    deliver_response(200, 'OK', $data);
    break;
  case 'POST' :
    $data = recupererDataDansCorps();
    if ($data = insert($data['phrase'])) {
      deliver_response(200, 'OK',$data);
    } else {
      deliver_response(500, 'Internal Server Error');
    }
    break;
  case 'PATCH': 
    if(isset($_GET['id']))
    {
      $id=htmlspecialchars($_GET['id']);
      $data = recupererDataDansCorps();
      $phrase = getData($id)[0];
      if (empty($phrase)) {
        deliver_response(404, 'Not found');
      }
      else {
        foreach ($phrase as $key => $value) {
          if (isset($data[$key])) {
            $phrase[$key] = $data[$key];
          }
        }
        update($phrase);
        deliver_response(200,'OK',$phrase);
      }
    }
    else {
      deliver_response(400, '[R401 API REST] : paramètre id manquant');
    }
  case 'PUT':
    if(isset($_GET['id']))
    {
      $id=htmlspecialchars($_GET['id']);
      $data = recupererDataDansCorps();
      $phrase = getData($id)[0];
      if (empty($phrase)) {
        deliver_response(404, 'Not found');
      }
      else {
        foreach ($phrase as $key => $value) {
          if (isset($data[$key])) {
            $phrase[$key] = $data[$key];
          }
          else {
            if ($key !='date_ajout' && $key != 'date_modif' && $key !='id') {
              deliver_response(404, "la clé '$key' est manquant pour modification totale");
              die();
            }
          }
        }
        update($phrase);
        deliver_response(200,'OK',$phrase);
      }
    }
    else {
      deliver_response(400, '[R401 API REST] : paramètre id manquant');
    }
  case 'DELETE':
    //Récupération des données dans l’URL
    if(isset($_GET['id']))
    {
      $id=htmlspecialchars($_GET['id']);
      $data = getData($id);
      if (empty($data) ) {
        deliver_response(404, 'NOT FOUND');
        die();
      }
    }
    else {
      deliver_response(400, '[R401 API REST] : paramètre id manquant');
      die();
    }
    supprimer($id);
    deliver_response(200, 'OK');
    break;

}

function recupererDataDansCorps() {
    // Récupération des données dans le corps
    $postedData = file_get_contents('php://input');
    return json_decode($postedData,true); //Reçoit du json et renvoi une adaptation exploitable en php. Le paramètre true impose un tableau en retour et non un objet.
    
}
/// Envoi de la réponse au Client
function deliver_response($status_code, $status_message, $data=null){
/// Paramétrage de l'entête HTTP
  http_response_code($status_code); //Utilise un message standardisé enfonction du code HTTP
  //header("HTTP/1.1 $status_code $status_message"); //Permet depersonnaliser le message associé au code HTTP
  header("Content-Type:application/json; charset=utf-8");//Indique auclient le format de la réponse
  header("Access-Control-Allow-Origin:*");
  header("Access-Control-Allow-Methods:GET,POST,PATCH,PUT,DELETE,OPTIONS");
  header("Access-Control-Allow-Headers:content-type,authorization");
  $response['status_code'] = $status_code;
  $response['status_message'] = $status_message;
  $response['data'] = $data;
  /// Mapping de la réponse au format JSON
  $json_response = json_encode($response);
  if($json_response===false)
    die('json encode ERROR : '.json_last_error_msg());
  /// Affichage de la réponse (Retourné au client)
  echo $json_response;
}