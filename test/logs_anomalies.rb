#!/usr/bin/env ruby


require 'mysql2'

# Configuration de la base de données
DB_HOST = 'mariadb'
DB_PORT = 3306
DB_NAME = 'logs_database'
DB_USER = 'wp_user'
DB_PASSWORD = 'wp_password'

MAX_OCCURRENCES = 5
INTERVALLE = 600 # Intervalle de 10 minutes entre les logs similaires
CPU_USAGE = 80.0

File.open("anomalies_log.txt", "w") do |file|

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
  def detect_anomalies(client, file)
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
    anomalies_detected = []
    anomalies_found = false

    results.each do |log|
      if anomalies_detected.none? { |a| a[:log_type] == log['log_type'] && a[:source] == log['source'] && (log['timestamp'].to_i - a[:timestamp].to_i).abs < INTERVALLE }
        anomalies_detected << { log_type: log['log_type'], source: log['source'], timestamp: log['timestamp'] }
        anomalies_found = true
        file.puts "Anomalie détectée : #{log['count_within_interval']} occurrences de '#{log['log_type']}' en moins de #{INTERVALLE / 60} minutes (Source: #{log['source']}, Heure: #{log['timestamp']})\n\n"
      end
    end

    file.puts "Aucune anomalie détectée." unless anomalies_found
  end

  # Fonction pour détecter les pics CPU
  def detect_cpu_peaks(threshold, file)
    output = `top -b -n 1 | grep 'Cpu(s)'`

    if output =~ /Cpu\(s\):\s+([\d+\.\d+]+)\s+us,\s+([\d+\.\d+]+)\s+sy/
      user_cpu = $1.to_f
      system_cpu = $2.to_f
      total_cpu_usage = user_cpu + system_cpu

      file.puts "-"*50

      if total_cpu_usage > threshold
        file.puts "\nPic CPU détecté : #{total_cpu_usage}% (Utilisateur: #{user_cpu}%, Système: #{system_cpu}%)"
      else
        file.puts "\nUtilisation CPU normale : #{total_cpu_usage}%"
      end
    else
      file.puts "Erreur : Impossible de récupérer les informations CPU."
    end
  end

  # Connexion à la base de données
  client = connect_to_db

  if client
    detect_anomalies(client, file)
    detect_cpu_peaks(CPU_USAGE, file)
  else
    file.puts "Échec de la connexion, vérifiez les informations de connexion."
  end

  client.close if client
end
