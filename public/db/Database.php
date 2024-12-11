<?php
class Database
{
    private static ?PDO $instance = null;


    // Constructor privado para evitar instancias
    private function __construct()
    {

    }

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $host = 'localhost';
            $dbname = 'pasteleria';
            $user = 'root';
            $password = 'Joframan123.';

            try {
                self::$instance = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
?>