<?php
require_once __DIR__ . '/vendor/autoload.php';

$client = new MongoDB\Client('mongodb://localhost:27017');
$collection = $client->mongotest->task;

// Traiter le formulaire d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... (comme précédemment)
}

// Récupérer les tâches depuis MongoDB
$data = $collection->find();
$tasks = iterator_to_array($data);

// Récupérer les tâches depuis la collection 'taska'
$collectionTaska = $client->mongotest->taska;
$dataTaska = $collectionTaska->find();
$tasksTaska = iterator_to_array($dataTaska);

// Traiter le formulaire d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = isset($_POST['nom']) ? $_POST['nom'] : '';
    $activite = isset($_POST['activite']) ? $_POST['activite'] : '';
    $task = isset($_POST['task']) ? $_POST['task'] : '';
    $dateDebut = isset($_POST['dateDebut']) ? $_POST['dateDebut'] : '';
    $dateFin = isset($_POST['dateFin']) ? $_POST['dateFin'] : '';

    $collection->insertOne([
        'nom' => $nom,
        'desription' => $activite,
        'date_debut' => $dateDebut,
        'date_fin' => $dateFin,
        'statut' => $task,
    ]);
}

// Récupérer les tâches depuis MongoDB
$data = $collection->find();
$tasks = iterator_to_array($data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MongoDB CRUD App</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }

        button {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .task-cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            border-color: rgb(74, 73, 73);
        }

        .task-card {
            flex: 0 0 calc(25% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            margin-bottom: 10px;
            border-radius: 20px;
        }

        .task-card:nth-child(odd) {
            background-color: #f0f0f0;
        }

        .task-card:nth-child(even) {
            background-color: #FFD700;
        }
        #elie{
            position: relative;
            left:320px

        }
    </style>
</head>
<body>
    <div class="container">
        <h1>MongoDB CRUD App</h1>

        <form method="post">
            <label for="nom">Nom:</label>
            <input type="text" name="nom" required>

            <label for="activite">Activité:</label>
            <input type="text" name="activite" required>

            <label for="task">Task:</label>
            <input type="text" name="task" required>

            <label for="dateDebut">Date Début:</label>
            <input type="text" name="dateDebut" placeholder="YYYY-MM-DD" required>

            <label for="dateFin">Date Fin:</label>
            <input type="text" name="dateFin" placeholder="YYYY-MM-DD" required>

            <button type="submit">Ajouter</button>
        </form>

        <div class="task-cards-container">
            <?php foreach ($tasks as $task): ?>
                <div class="task-card <?= $task['statut'] === 'terminé' ? 'finished' : '' ?>">
                    <form method="post" class="task-form">
                        <strong><?= $task['nom'] ?></strong> -
                        <?= $task['desription'] ?? '' ?> -
                        <?= $task['date_debut'] ?? '' ?> à <?= $task['date_fin'] ?? '' ?> -
                        <?= $task['statut'] ?? '' ?>
                        <input type="hidden" name="deleteTaskId" value="<?= $task['_id'] ?>">
                        <button type="submit" name="terminer" class="terminer-btn">achever</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
            <?php
            // 
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['terminer'])) {
                $taskId = $_POST['deleteTaskId'];

                // Retrieve task details from 'task' collection
                $taskDetails = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($taskId)]);

                // Check if task details are not null before processing
                if ($taskDetails !== null) {
                    
                    $collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($taskId)]);

                    // Insert the task into 'taska' collection
                    $client->mongotest->taska->insertOne([
                        'nom' => $taskDetails['nom'],
                        'desription' => $taskDetails['desription'],
                        'date_debut' => $taskDetails['date_debut'],
                        'date_fin' => $taskDetails['date_fin'],
                        'statut' => $taskDetails['statut'],
                    ]);
                }
            }
            ?>
        </div>
    </div>
    <hr>
    <hr>
       <h1 id="elie">LISTE DES TOUS LES TACHE TERMINER</h1>
    <div class="task-cards-container">
            <?php foreach ($tasksTaska as $task): ?>
                <div class="task-card">
                    <!-- Afficher les détails de la tâche depuis la collection 'taska' -->
                    <strong><?= $task['nom'] ?></strong> -
                    <?= $task['desription'] ?? '' ?> -
                    <?= $task['date_debut'] ?? '' ?> à <?= $task['date_fin'] ?? '' ?> -
                    <?= $task['statut'] ?? '' ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <hr>
    <hr>
</body>
</html>
