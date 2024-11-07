<?php

/***************************** Paramètres de connexion *****************************/

$servername = "mariadb";
$username = "wp_user";
$password = "wp_password";
$dbname = "logs_database";


/***************************** Fonctions d'affichage *****************************/

/**
 * Fonction defautlPrint permettant l affichage par défaut au démarrage de l application
 * @param mixed $conn
 * @return void
 */
function defaultPrint($pConn):void {
    try {
        // Requête SQL pour afficher tous les logs sans filtres, triés par timestamp descendant
        $sql = "SELECT * FROM logs ORDER BY timestamp DESC";
        $stmt = $pConn->prepare($sql);
        $stmt->execute();

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
            echo "<tr><td colspan='4'>Aucun log trouvé.</td></tr>";
        }
    } catch (PDOException $e) {
        echo "<tr><td colspan='5'>Erreur de connexion : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
    }
}


function sourceFilter($pSource) {
    if ($pSource === "Apache (Wordpress)") {
        return "source = :pSource";  // Utilisation d'un paramètre préparé
    } elseif ($pSource === "Mariadb (Wordpress)") {
        return "source = :pSource";  // Utilisation d'un paramètre préparé
    }
    return "";  // Aucun filtre si la source n'est pas définie
}


function typeFilter($pType) {
    if ($pType === "500 - Memory Exhaustion") {
        return "log_type = :pType";
    } elseif ($pType === "500 - Undefined Function") {
        return "log_type = :pType";
    } elseif ($pType === "404 - Page Not Found") {
        return "log_type = :pType";
    } elseif ($pType === "401 - Access Denied") {
        return "log_type = :pType";
    } elseif ($pType === "403 - Access Denied") {
        return "log_type = :pType";
    } elseif ($pType === "403 - File Permission") {
        return "log_type = :pType";
    } elseif ($pType === "1045 - ER_ACCESS_DENIED_ERROR") {
        return "log_type = :pType";
    } elseif ($pType === "2002 - ER_BAD_HOST_ERROR") {
        return "log_type = :pType";
    } elseif ($pType === "1146 - ER_NO_SUCH_TABLE") {
        return "log_type = :pType";
    } elseif ($pType === "1064 - ER_PARSE_ERROR") {
        return "log_type = :pType";
    } elseif ($pType === "1040 - ER_TOO_MANY_CONNECTIONS") {
        return "log_type = :pType";
    } elseif ($pType === "Tous les types") {
        return "";  // Aucun filtre pour "Tous les types"
    }
    return "";
}


function filter($pSource, $pType): string {
    $sql = "SELECT * FROM logs";  // Start with SELECT only, no WHERE by default

    $filters = [];
    
    if (!empty($pSource)) {
        $sourceFilterResult = sourceFilter($pSource);
        if (!empty($sourceFilterResult)) {
            $filters[] = $sourceFilterResult;
        }
    }

    if (!empty($pType)) {
        $typeFilterResult = typeFilter($pType);
        if (!empty($typeFilterResult)) {
            $filters[] = $typeFilterResult;
        }
    }

    if (count($filters) > 0) {
        $sql .= " WHERE " . implode(" AND ", $filters);  // Only add WHERE if filters exist
    }

    // Add the ORDER BY clause
    $sql .= " ORDER BY timestamp DESC";  

    echo "<p>SQL générée: " . htmlspecialchars($sql) . "</p>";
    return $sql;
}




function filterPrint($pConn, $pSource, $pType) {
    try {
        // Générer la requête SQL filtrée
        $sql = filter($pSource, $pType);

        // Vérifier si la requête SQL est vide
        if (empty($sql)) {
            throw new Exception("La requête SQL est vide. Aucun filtre valide n'a été appliqué.");
        }

        // Préparer la requête SQL
        $stmt = $pConn->prepare($sql);

        // Lier les paramètres pour la source et le type si nécessaire
        if (strpos($sql, ':pSource') !== false && !empty($pSource)) {
            $stmt->bindParam(':pSource', $pSource);
        }

        if (strpos($sql, ':pType') !== false && !empty($pType)) {
            $stmt->bindParam(':pType', $pType);
        }

        // Exécuter la requête
        $stmt->execute();

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
            echo "<tr><td colspan='4'>Aucun log trouvé.</td></tr>";
        }   
    } catch (PDOException $e) {
        echo "<tr><td colspan='5'>Erreur de connexion : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
    } catch (Exception $e) {
        echo "<tr><td colspan='5'>Erreur : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
    }
}


/***************************** Main interaction web-interface *****************************/

try {
    // Créer une connexion PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si des filtres ont été appliqués
    $source = $_GET['source'] ?? '';  // Valeur par défaut vide si non définie
    $log_type = $_GET['log_type'] ?? '';  // Valeur par défaut vide si non définie

    if (!empty($source) || !empty($log_type)) {
        // Si des filtres sont définis (source ou log_type), appeler filterPrint
        filterPrint($conn, $source, $log_type);
    } else {
        // Sinon, afficher les logs par défaut
        defaultPrint($conn);
    }

} catch (PDOException $e) {
    echo "<tr><td colspan='5'>Erreur de connexion : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}

// Fermer la connexion
$conn = null;

?>
