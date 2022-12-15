<?php

// Connect to the database
$cnx = new PDO('mysql:host=localhost;dbname=my_database', 'root', 'password');

// GET endpoint to retrieve all users
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['endpoint'] === 'users/all') {
  $stmt = $cnx->prepare('SELECT * FROM users');
  $stmt->execute();
  $rows = $stmt->fetchAll();

  $results = [];
  foreach ($rows as $row) {
    $results[] = [
      'id' => $row['id'],
      'name' => $row['name'],
      'email' => $row['email'],
      'password' => $row['password'],
      'age' => $row['age']
    ];
  }

  echo json_encode($results);
}

// GET endpoint to retrieve a single user by ID
if ($_SERVER['REQUEST_METHOD'] === 'GET' && preg_match('/users\/\d+/', $_GET['endpoint'])) {
  preg_match('/\d+/', $_GET['endpoint'], $matches);
  $user_id = $matches[0];

  $stmt = $cnx->prepare('SELECT * FROM users WHERE id=:id');
  $stmt->bindParam(':id', $user_id);
  $stmt->execute();
  $row = $stmt->fetch();

  $result = [
    'id' => $row['id'],
    'name' => $row['name'],
    'email' => $row['email'],
    'password' => $row['password'],
    'age' => $row['age']
  ];

  echo json_encode($result);
}

// GET endpoint to retrieve all posts by a user
if ($_SERVER['REQUEST_METHOD'] === 'GET' && preg_match('/users\/\d+\/posts/', $_GET['endpoint'])) {
  preg_match('/\d+/', $_GET['endpoint'], $matches);
  $user_id = $matches[0];

  $stmt = $cnx->prepare('SELECT p.id, p.title, p.content FROM users u INNER JOIN posts p ON p.user_id = u.id WHERE u.id = :id');
  $stmt->bindParam(':id', $user_id);
  $stmt->execute();
  $rows = $stmt->fetchAll();

  $results = [];
  foreach ($rows as $row) {
    $results[] = [
      'id' => $row['id'],
      'title' => $row['title'],
      'content' => $row['content']
    ];
  }

  echo json_encode($results);
}

// POST endpoint to insert a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['endpoint'] === 'users/new') {
  // Retrieve the user's name, email, password, and age from the request body
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $age = $_POST['age'];

  // Insert the new user into the database
  $stmt = $cnx->prepare('INSERT INTO users (name, email, password, age) VALUES (:name, :email, :password, :age)');
  $stmt->bindParam(':name', $name);
  $stmt->bindParam(':email', $email);
  $stmt->bindParam(':password', $password);
  $stmt->bindParam(':age', $age);
  $stmt->execute();
  
  // Return the new user's ID to the client
  $user_id = $cnx->lastInsertId();
  echo json_encode(['id' => $user_id]);
}

?>
