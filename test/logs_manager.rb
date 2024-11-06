require 'mysql2'
require 'date'

# Configuration de la base de données
DB_HOST = 'mariadb'
DB_PORT = 3306
DB_NAME = 'logs_database'
DB_USER = 'wp_user'
DB_PASSWORD = 'wp_password'

# Fonction pour se connecter à la base de données
def connect_to_db
  Mysql2::Client.new(
    host: DB_HOST,
    port: DB_PORT,
    username: DB_USER,
    password: DB_PASSWORD,
    database: DB_NAME
  )
rescue Mysql2::Error => e
  puts "Erreur de connexion à la base de données : #{e.message}"
  nil
end

# Fonction pour obtenir le type de log Apache à partir du message
def determine_log_type_apache(message)
  if message.include?("exhausted")
    "500 - Memory Exhaustion"
  elsif message.include?("undefined")
    "500 - Undefined function"
  elsif message.include?("404")
    "404 - Page Not Found"
  elsif message.include?("Access denied")
    if message.include?("using password: YES") || message.include?("permission denied to access")
      "401 - Access Denied"
    else
      "403 - Access Denied"
    end
  elsif message.include?("failed to open stream")
    "403 - File Permission Denied"
  else
    "Unknown"
  end
end

# Fonction pour obtenir le type de log Mariadb à partir du message
def determine_log_type_mariadb(message)
  if message.include?("Access denied")
    "1045 - ER_ACCESS_DENIED_ERROR"
  elsif message.include?("connect to MySQL server")
    "2002 - ER_BAD_HOST_ERROR"
  elsif message.include?("Table")
    "1146 - ER_NO_SUCH_TABLE"
  elsif message.include?("syntax")
    "1064 - ER_PARSE_ERROR"
  elsif message.include?("exceeded")
    "1040 - ER_TOO_MANY_CONNECTIONS"
  else
    "Unknown"
  end
end


# Fonction d'insertion des données dans la base
def insert_data(client)
  begin
    # Ouverture du fichier de logs Apache
    File.foreach("./apache_logs.log") do |line|
      # Vérification du format des lignes
      if line =~ /\[(\d{2}-\w{3}-\d{4} \d{2}:\d{2}:\d{2}) UTC\] .*?: (.*)/
        date_str = $1
        # Formatage de la date en format MySQL : 'YYYY-MM-DD HH:MM:SS'
        timestamp = DateTime.strptime(date_str, "%d-%b-%Y %H:%M:%S").strftime("%Y-%m-%d %H:%M:%S")
        source = "Apache (Wordpress)"
        message = $2
        
        # Détermination du type de log
        log_type = determine_log_type_apache(message)

        # Insertion des données dans la base
        query = "INSERT INTO logs (timestamp, source, log_type, message) VALUES (?, ?, ?, ?)"
        client.prepare(query).execute(timestamp, source, log_type, message)
      end
    end
  rescue StandardError => e
    puts "Erreur lors de l'insertion des logs : #{e.message}"
  end

  begin
    # Ouverture du fichier logs de Mariadb
    File.foreach("./mariadb_logs.log") do |line|
      # Vérification du format des lignes
      if line =~ /\[(.*?)\] \[.*?\] (.*)/
        date_str = $1
        # Formatage de la date en format MySQL : 'YYYY-MM-DD HH:MM:SS'
        timestamp = DateTime.strptime(date_str, "%d-%b-%Y %H:%M:%S").strftime("%Y-%m-%d %H:%M:%S")
        source = "Mariadb (Wordpress)"
        message = $2
        
        # Détermination du type de log
        log_type = determine_log_type_mariadb(message)

        # Insertion des données dans la base
        query = "INSERT INTO logs (timestamp, source, log_type, message) VALUES (?, ?, ?, ?)"
        client.prepare(query).execute(timestamp, source, log_type, message)
      end
    end
  rescue StandardError => e
    puts "Erreur lors de l'insertion des logs : #{e.message}"
  end 
end

# Exemple d'exécution
client = connect_to_db
if client
  insert_data(client)
  client.close
  puts "Connexion établie avec la base de données : #{DB_NAME}"
  puts "Logs insérés"
else
  puts "Impossible de se connecter à la base de données."
end
