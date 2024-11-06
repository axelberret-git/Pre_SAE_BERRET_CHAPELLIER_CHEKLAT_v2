<?php
// Paramètres de connexion
$servername = "mariadb";
$username = "wp_user";
$password = "wp_password";
$dbname = "logs_database";

try {
    // Créer une connexion PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupérer les paramètres de filtrage
    $source = $_GET['source'] ?? '';
    $log_type = $_GET['log_type'] ?? '';
    $timestamp = $_GET['timestamp'] ?? '';

    // Construire la requête SQL avec les filtres
    $sql = "SELECT timestamp, source, log_type, message FROM logs WHERE 1=1";
    $params = [];

    if (!empty($source)) {
        $sql .= " AND source = :source";
        $params[':source'] = $source;
    }
    if (!empty($log_type)) {
        $sql .= " AND log_type = :log_type";
        $params[':log_type'] = $log_type;
    }
    if (!empty($timestamp)) {
        $sql .= " AND timestamp >= :timestamp";
        $params[':timestamp'] = $timestamp;
    }

    // Préparer et exécuter la requête
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    // Afficher les résultats dans le tableau HTML
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                    <td>" . htmlspecialchars($row["timestamp"]) . "</td>
                    <td>" . htmlspecialchars($row["source"]) . "</td>
                    <td>" . htmlspecialchars($row["log_type"]) . "</td>
                    <td>" . htmlspecialchars($row["message"]) . "</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>Aucun log trouvé pour les critères de recherche sélectionnés.</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='5'>Erreur de connexion : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}

// Fermer la connexion
$conn = null;
?>
