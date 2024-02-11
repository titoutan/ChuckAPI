<?php
require_once('ConnexionDB.php');

function getData($id) {
  $linkpdo = ConnexionDB::getInstance()->getPdo();
  if (isset($id)) {
    $data = $linkpdo->prepare('SELECT * FROM chuckn_facts WHERE id=:id');
    $data->execute(array('id' => $id));
    if (!$data) {
      return false;
    }
  }
  else {
    $data = $linkpdo->query('SELECT * FROM chuckn_facts');
  }
  return $data->fetchAll(PDO::FETCH_ASSOC);
}

function insert($phrase) {
  $linkpdo = ConnexionDB::getInstance()->getPdo();
  $query = $linkpdo->prepare('INSERT INTO chuckn_facts(phrase,date_ajout) VALUES (:phrase, NOW())');
  $linkpdo->beginTransaction();
  $query->execute(array('phrase'=>$phrase));
  $id = $linkpdo->lastInsertId();
  $linkpdo->commit();
  return getData($id);
}

function update($phrase) {
  $linkpdo = ConnexionDB::getInstance()->getPdo();
  $query = $linkpdo->prepare('UPDATE chuckn_facts SET phrase = :phrase, date_modif = NOW(), vote = :vote, signalement = :signalement, faute = :faute WHERE id = :id');
  $linkpdo->beginTransaction();
  $query->execute(
    array(
      'id' => $phrase['id'],
      'phrase' => $phrase['phrase'],
      'vote' => $phrase['vote'],
      'signalement' => $phrase['signalement'],
      'faute' => $phrase['faute'],
    )
    );
  $id = $linkpdo->lastInsertId();
  $linkpdo->commit();
  return getData($id);
}

function supprimer($id) {
  $linkpdo = ConnexionDB::getInstance()->getPdo();
  $query = $linkpdo->prepare('DELETE FROM chuckn_facts WHERE id = :id');
  return $query->execute(array('id' => $id));
}