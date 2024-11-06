require 'mysql2'

DB_HOST = 'mariadb'
DB_PORT = 3306
DB_NAME = 'logs_database'
DB_USER = 'wp_user'
DB_PASSWORD = 'wp_password'

begin
  client = Mysql2::Client.new(
    host: DB_HOST,
    port: DB_PORT,
    username: DB_USER,
    password: DB_PASSWORD,
    database: DB_NAME
  )
  puts "Connexion établie avec la base de données : #{DB_NAME}"
rescue Mysql2::Error => e
  puts "Erreur de connexion à la base de données : #{e.message}"
ensure
  client&.close
end
