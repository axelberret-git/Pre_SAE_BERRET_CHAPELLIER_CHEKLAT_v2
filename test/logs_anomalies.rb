require 'mysql2'

# Configuration de la base de données
DB_HOST = 'mariadb'
DB_PORT = 3306
DB_NAME = 'logs_database'
DB_USER = 'wp_user'
DB_PASSWORD = 'wp_password'

# Règles pour les anomalies
ANOMALIES = {
  'Apache' => {
    '401 - Access Denied' => { max_occurrences: 3, intervalle: 600 },
    '500 - Memory Exhaustion' => { max_occurrences: 2, intervalle: 300 },
    '404 - Page Not Found' => { max_occurrences: 5, intervalle: 600 }
  },
  'MariaDB' => {
    '1045 - ER_ACCESS_DENIED_ERROR' => { max_occurrences: 3, intervalle: 600 },
    '1146 - ER_NO_SUCH_TABLE' => { max_occurrences: 2, intervalle: 600 },
    '1064 - ER_PARSE_ERROR' => { max_occurrences: 2, intervalle: 600 }
  }
}

# Fonction de connexion à la base de données
def connect_to_db
  Mysql2::Client.new(
    host: DB_HOST,
    port: DB_PORT,
    username: DB_USER,
    password: DB_PASSWORD,
    database: DB_NAME
  )
end

# Fonction de détection d'anomalies
def detect_anomalies(client, source, log_type, max_occurrences, intervalle)
  query = %{
    SELECT timestamp, COUNT(*) as count
    FROM logs
    GROUP BY DATE(timestamp), HOUR(timestamp), MINUTE(timestamp)
    HAVING count >= #{max_occurrences}
  }

  results = client.query(query)

  results.each do |log|
    puts "Anomalie détectée dans #{source} : #{log['count']} occurrences de '#{log_type}' en moins de #{intervalle / 60} minutes !"
  end
end

# Connexion et détection d'anomalies
begin
  client = connect_to_db
  puts "Connexion à la base de données réussie !"

  # Parcours des règles et détection d'anomalies
  ANOMALIES.each do |source, anomalies|
    anomalies.each do |log_type, params|
      detect_anomalies(client, source, log_type, params[:max_occurrences], params[:intervalle])
    end
  end

rescue Mysql2::Error => e
  puts "Erreur lors de la connexion : #{e.message}"
ensure
  client.close if client
end
