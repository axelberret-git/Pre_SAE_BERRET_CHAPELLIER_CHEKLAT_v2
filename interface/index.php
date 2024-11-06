<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion logs</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>APPLICATION GESTION DE LOGS</h1>
    <div class="filter">
        <form method="GET" action="script.php"> <!-- Envoi des données vers script.php -->
            <label for="source">Source:</label>
            <select name="source">
                <option value="">Toutes les sources</option>
                <option value="apache">Apache (WordPress)</option>
                <option value="mariadb">Mariadb (Wordpress)</option>
            </select>

            <label for="log_type">Type de Log:</label>
            <select name="log_type">
                <option value="">Tous les types</option>
                <option value="500">Type 500</option>
                <option value="400">Type 400</option>
                <option value="500_memory">500 - Memory Exhaustion</option>
                <option value="500_undefined">500 - Undefined function</option>
                <option value="404">404 - Page Not Found</option>
                <option value="401">401 - Access Denied</option>
                <option value="403">403 - Access Denied</option>
                <option value="403_permission">403 - File Permission Denied</option>
                <option value="1045">1045 - ER_ACCESS_DENIED_ERROR</option>
                <option value="2002">2002 - ER_BAD_HOST_ERROR</option>
                <option value="1146">1146 - ER_NO_SUCH_TABLE</option>
                <option value="1064">1064 - ER_PARSE_ERROR</option>
                <option value="1040">1040 - ER_TOO_MANY_CONNECTIONS</option>
            </select>

            <label for="timestamp">Depuis:</label>
            <input type="date" name="timestamp">

            <button type="submit">Filtrer</button>
        </form>
    </div>
    <table>
        <tr>
            <th>Timestamp</th>
            <th>Source</th>
            <th>Type</th>
            <th>Message</th>
        </tr>
        <!-- Inclusion des résultats depuis script.php -->
        <?php include 'script.php'; ?>
    </table>
</body>
</html>
