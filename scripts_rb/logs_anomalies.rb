require 'mysql2'

# Configuration de la base de données
DB_HOST = 'mariadb'
DB_PORT = 3306
DB_NAME = 'logs_database'
DB_USER = 'wp_user'
DB_PASSWORD = 'wp_password'

# Paramètres pour les détections d'anomalies
MAX_OCCURRENCES = 5
INTERVALLE = 600 # Intervalle de 10 minutes entre les logs similaires

# Fonction de connexion à la base de données
def connect_to_db
  begin
    client = Mysql2::Client.new(
      host: DB_HOST,
      port: DB_PORT,
      username: DB_USER,
      password: DB_PASSWORD,
      database: DB_NAME
    )
    puts "Connexion à la base de données réussie !"
    return client
  rescue Mysql2::Error => e
    puts "Erreur de connexion : #{e.message}"
    return nil
  end
end

# Fonction de détection des anomalies basée sur l'intervalle entre les logs similaires
def detect_anomalies(client)
  # Requête SQL pour récupérer les logs avec l'intervalle entre les logs similaires
  query = %{
    SELECT l1.id, l1.log_type, l1.source, l1.timestamp, 
           COUNT(l2.id) AS count_within_interval
    FROM logs l1
    LEFT JOIN logs l2
      ON l1.log_type = l2.log_type 
      AND l1.source = l2.source
      AND l2.timestamp BETWEEN l1.timestamp AND DATE_ADD(l1.timestamp, INTERVAL #{INTERVALLE} SECOND)
    GROUP BY l1.id
    HAVING count_within_interval >= #{MAX_OCCURRENCES}
    ORDER BY l1.timestamp
  }

  results = client.query(query)

  # Utilisation d'un tableau pour garder la trace des anomalies déjà détectées
  anomalies_detected = []

  # Analyse des résultats pour détecter les anomalies
  anomalies_found = false
  results.each do |log|
    # Créer une clé d'anomalie unique basée sur log_type, source, et timestamp.
    anomaly_key = "#{log['log_type']}_#{log['source']}_#{log['timestamp']}"

    # Vérifie si une anomalie similaire a déjà été détectée (en tenant compte d'une petite marge de temps)
    if anomalies_detected.none? { |a| a[:log_type] == log['log_type'] && a[:source] == log['source'] && (log['timestamp'].to_i - a[:timestamp].to_i).abs < INTERVALLE }
      anomalies_detected << { log_type: log['log_type'], source: log['source'], timestamp: log['timestamp'] }
      anomalies_found = true
      puts "Anomalie détectée : #{log['count_within_interval']} occurrences de '#{log['log_type']}' en moins de #{INTERVALLE / 60} minutes (Source: #{log['source']}, Heure: #{log['timestamp']}) !"
    end
  end

  puts "Aucune anomalie détectée." unless anomalies_found
end

# Connexion à la base de données
client = connect_to_db

if client
  # Détection des anomalies
  detect_anomalies(client)
else
  puts "Échec de la connexion, vérifiez les informations de connexion."
end

# Fermer la connexion
client.close if client
