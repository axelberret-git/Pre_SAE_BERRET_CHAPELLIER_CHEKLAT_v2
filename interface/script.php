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


function sourceFilter($pSource):string {
    $ourceFilter = "";
    if ($pSource === "Apache (Wordpress)") {
        $sourceFilter = ":source LIKE 'Apache (Wordpress)'";
    } else if ($pSource === "Mariadb (Wordpress)") {
        $sourceFilter = "source LIKE 'Mariadb (Wordpress)'";
    }
    return $sourceFilter;
}


function typeFilter($pType):string {
    $typeFilter = "";
    if ($pType === "500 - Memory Exhaustion") {
        $typeFilter = "log_type LIKE '500 - Memory Exhaustion'";
    } else if ($pType === "500 - Undefined Function") {
        $typeFilter = "log_type LIKE '500 - Undefined Function'";
    } else if ($pType === "404 - Page Not Found") {
        $typeFilter = "log_type LIKE '404 - Page Not Found'";
    } else if ($pType === "401 - Access Denied") {
        $typeFilter = "log_type LIKE '401 - Access Denied'";
    } else if ($pType === "403 - Access Denied") {
        $typeFilter = "log_type LIKE '403 - Access Denied'";
    } else if ($pType === "403 - File Permission") {
        $typeFilter = "log_type LIKE '403 - File Permission'";
    } else if ($pType === "1045 - ER_ACCESS_DENIED_ERROR") {
        $typeFilter = "log_type LIKE '1045 - ER_ACCESS_DENIED_ERROR'";
    } else if ($pType === "2002 - ER_BAD_HOST_ERROR") {
        $typeFilter = "log_type LIKE '2002 - ER_BAD_HOST_ERROR'";
    } else if ($pType === "1146 - ER_NO_SUCH_TABLE") {
        $typeFilter = "log_type LIKE '1146 - ER_NO_SUCH_TABLE'";
    } else if ($pType === "1064 - ER_PARSE_ERROR") {
        $typeFilter = "log_type LIKE '1064 - ER_PARSE_ERROR'";
    } else if ($pType === "1040 - ER_TOO_MANY_CONNECTIONS") {
        $typeFilter = "log_type LIKE '1040 - ER_TOO_MANY_CONNECTIONS'";
    }
    return $typeFilter;
}


function filter($pSource, $pType):string {
    $sql = "";
    if ($pSource === "Apache (Wordpress)") {
        if ($pType === "500 - Memory Exhaustion") {
            $sql = "SELECT * FROM logs WHERE ".sourceFilter("Apache (Wordpress)")." AND ".typeFilter("500 - Memory Exhaustion")." ORDER BY timestamp DESC";
        } else if ($pType === "500 - Undefined Function") {
            $sql = "SELECT * FROM logs WHERE ".sourceFilter("Apache (Wordpress)")." AND ".typeFilter("500 - Undefined Function")." ORDER BY timestamp DESC";
        } else if ($pType === "404 - Page Not Found") {
            $sql = "SELECT * FROM logs WHERE ".sourceFilter("Apache (Wordpress)")." AND ".typeFilter("404 - Page Not Found")." ORDER BY timestamp DESC";
        } else if ($pType === "401 - Access Denied") {
            $sql = "SELECT * FROM logs WHERE ".sourceFilter("Apache (Wordpress)")." AND ".typeFilter("401 - Access Denied")." ORDER BY timestamp DESC";
        } else if ($pType === "403 - Access Denied") {
            $sql = "SELECT * FROM logs WHERE ".sourceFilter("Apache (Wordpress)")." AND ".typeFilter("403 - Access Denied")." ORDER BY timestamp DESC";
        } else if ($pType === "403 - File Permission") {
            $sql = "SELECT * FROM logs WHERE ".sourceFilter("Apache (Wordpress)")." AND ".typeFilter("403 - File Permission")." ORDER BY timestamp DESC";
        } else if ($pType === "Type 500") {
            $sql = "SELECT * FROM logs WHERE ".sourceFilter("Apache (Wordpress)")." AND log_type LIKE '5__' ORDER BY timestamp DESC";
        } else if ($pType === "Type 400") {
            $sql = "SELECT * FROM logs WHERE ".sourceFilter("Apache (Wordpress)")." AND log_type LIKE '4__' ORDER BY timestamp DESC";
        } else if ($pType === "Tous les types") {
            $sql = "SELECT * FROM logs WHERE ".sourceFilter("Apache (Wordpress)")." ORDER BY timestamp DESC";
        }
    } else if ($pSource === "") {
        if ($pType === "1045 - ER_ACCESS_DENIED_ERROR") {
            $sql = "SELECT * FROM logs WHERE ".sourceFilter("Mariadb (Wordpress)")." AND ".typeFilter("1045 - ER_ACCESS_DENIED_ERROR")." ORDER BY timestamp DESC";
        } else if ($pType === "2002 - ER_BAD_HOST_ERROR") {
            $sql = "SELECT * FROM logs WHERE ".sourceFilter("Mariadb (Wordpress)")." AND ".typeFilter("2002 - ER_BAD_HOST_ERROR")." ORDER BY timestamp DESC";
        } else if ($pType === "1146 - ER_NO_SUCH_TABLE") {
            $sql = "SELECT * FROM logs WHERE ".sourceFilter("Mariadb (Wordpress)")." AND ".typeFilter("1146 - ER_NO_SUCH_TABLE")." ORDER BY timestamp DESC";
        } else if ($pType === "1064 - ER_PARSE_ERROR") {
            $sql = "SELECT * FROM logs WHERE ".sourceFilter("Mariadb (Wordpress)")." AND ".typeFilter("1064 - ER_PARSE_ERROR")." ORDER BY timestamp DESC";
        } else if ($pType === "1040 - ER_TOO_MANY_CONNECTIONS") {
            $sql = "SELECT * FROM logs WHERE ".sourceFilter("Mariadb (Wordpress)")." AND ".typeFilter("1040 - ER_TOO_MANY_CONNECTIONS")." ORDER BY timestamp DESC";
        } else if ($pType === "Tous les types") {
            $sql = "SELECT * FROM logs WHERE ".sourceFilter("Mariadb (Wordpress)")." ORDER BY timestamp DESC";
        }
    } else if ($pSource === "Toutes les sources") {
        if ($pType === "500 - Memory Exhaustion") {
            $sql = "SELECT * FROM logs WHERE ".typeFilter("500 - Memory Exhaustion")." ORDER BY timestamp DESC";
        } else if ($pType === "500 - Undefined Function") {
            $sql = "SELECT * FROM logs WHERE ".typeFilter("500 - Undefined Function")." ORDER BY timestamp DESC";
        } else if ($pType === "404 - Page Not Found") {
            $sql = "SELECT * FROM logs WHERE ".typeFilter("404 - Page Not Found")." ORDER BY timestamp DESC";
        } else if ($pType === "401 - Access Denied") {
            $sql = "SELECT * FROM logs WHERE ".typeFilter("401 - Access Denied")." ORDER BY timestamp DESC";
        } else if ($pType === "403 - Access Denied") {
            $sql = "SELECT * FROM logs WHERE ".typeFilter("403 - Access Denied")." ORDER BY timestamp DESC";
        } else if ($pType === "403 - File Permission") {
            $sql = "SELECT * FROM logs WHERE ".typeFilter("403 - File Permission")." ORDER BY timestamp DESC";
        } else if ($pType === "Type 500") {
            $sql = "SELECT * FROM logs WHERE log_type LIKE '5__' ORDER BY timestamp DESC";
        } else if ($pType === "Type 400") {
            $sql = "SELECT * FROM logs WHERE log_type LIKE '4__' ORDER BY timestamp DESC";
        } else if ($pType === "1045 - ER_ACCESS_DENIED_ERROR") {
            $sql = "SELECT * FROM logs WHERE ".typeFilter("1045 - ER_ACCESS_DENIED_ERROR")." ORDER BY timestamp DESC";
        } else if ($pType === "2002 - ER_BAD_HOST_ERROR") {
            $sql = "SELECT * FROM logs WHERE ".typeFilter("2002 - ER_BAD_HOST_ERROR")." ORDER BY timestamp DESC";
        } else if ($pType === "1146 - ER_NO_SUCH_TABLE") {
            $sql = "SELECT * FROM logs WHERE ".typeFilter("1146 - ER_NO_SUCH_TABLE")." ORDER BY timestamp DESC";
        } else if ($pType === "1064 - ER_PARSE_ERROR") {
            $sql = "SELECT * FROM logs WHERE ".typeFilter("1064 - ER_PARSE_ERROR")." ORDER BY timestamp DESC";
        } else if ($pType === "1040 - ER_TOO_MANY_CONNECTIONS") {
            $sql = "SELECT * FROM logs WHERE ".typeFilter("1040 - ER_TOO_MANY_CONNECTIONS")." ORDER BY timestamp DESC";
        } else if ("Tous les types") {
            $sql = "SELECT * FROM logs ORDER BY timestamp DESC";
        }
    }
    return $sql;
}


function filterPrint($pConn, $pSource, $pType) {
    try {
        // Requête SQL pour afficher les logs filtrés
        $sql = filter($pSource, $pType);

        // Vérifier si la requête SQL est vide
        if (empty($sql)) {
            throw new Exception("La requête SQL est vide. Aucun filtre valide n'a été appliqué.");
        }

        // Préparer et exécuter la requête
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
