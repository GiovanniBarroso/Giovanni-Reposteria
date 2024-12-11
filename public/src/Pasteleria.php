<?php
require_once 'Dulce.php';
require_once 'Cliente.php';
require_once 'Bollo.php';
require_once 'Chocolate.php';
require_once 'Tarta.php';
require_once '../util/DulceNoEncontradoException.php';
require_once '../util/ClienteNoEncontradoException.php';
require_once __DIR__ . '/../db/Database.php';

class Pasteleria
{
    private array $productos;
    private array $clientes;
    private $db;

    public function __construct()
    {
        $this->productos = [];
        $this->clientes = [];
        $this->db = Database::getConnection();
    }


    /* USUARIO Y CREDENCIALES */

    public function registrarUsuario($nombre, $usuario, $password, $rol = 'cliente')
    {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO clientes (nombre, usuario, password, rol) VALUES (:nombre, :usuario, :password, :rol)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':nombre', $nombre);
            $stmt->bindValue(':usuario', $usuario);
            $stmt->bindValue(':password', $hashedPassword);
            $stmt->bindValue(':rol', $rol);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al registrar usuario: " . $e->getMessage());
        }
    }



    public function buscarUsuarioPorNombre($usuario)
    {
        $query = "SELECT * FROM clientes WHERE usuario = :usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':usuario', $usuario);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



    public function validarCredenciales($usuario, $password)
    {
        $user = $this->buscarUsuarioPorNombre($usuario);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }






    /* CLIENTES */


    public function buscarCliente(string $nombre): ?Cliente
    {
        try {
            foreach ($this->clientes as $cliente) {
                if ($cliente->getNombre() === $nombre) {
                    return $cliente;
                }
            }
            throw new ClienteNoEncontradoException("Cliente no encontrado: {$nombre}");
        } catch (ClienteNoEncontradoException $e) {
            echo $e->getMessage();
            return null;
        }
    }


    public function obtenerClientes(): array
    {
        $db = Database::getConnection();
        $query = "SELECT id, nombre, usuario, rol FROM clientes";
        $stmt = $db->query($query);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function actualizarCliente(int $id, string $nombre, string $usuario, string $rol): bool
    {
        $db = Database::getConnection();
        $query = "UPDATE clientes SET nombre = :nombre, usuario = :usuario, rol = :rol WHERE id = :id";
        $stmt = $db->prepare($query);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':usuario', $usuario);
        $stmt->bindValue(':rol', $rol);

        return $stmt->execute();
    }



    public function eliminarCliente(int $id): bool
    {
        try {
            $db = Database::getConnection();

            // Eliminar valoraciones relacionadas
            $stmtValoraciones = $db->prepare("DELETE FROM valoraciones WHERE cliente_id = :id");
            $stmtValoraciones->bindValue(':id', $id, PDO::PARAM_INT);
            $stmtValoraciones->execute();

            // Eliminar detalles de pedidos relacionados
            $stmtDetalles = $db->prepare("DELETE FROM detalle_pedidos WHERE pedido_id IN (SELECT id FROM pedidos WHERE cliente_id = :id)");
            $stmtDetalles->bindValue(':id', $id, PDO::PARAM_INT);
            $stmtDetalles->execute();

            // Eliminar pedidos del cliente
            $stmtPedidos = $db->prepare("DELETE FROM pedidos WHERE cliente_id = :id");
            $stmtPedidos->bindValue(':id', $id, PDO::PARAM_INT);
            $stmtPedidos->execute();

            // Finalmente, eliminar el cliente
            $stmtCliente = $db->prepare("DELETE FROM clientes WHERE id = :id");
            $stmtCliente->bindValue(':id', $id, PDO::PARAM_INT);
            $stmtCliente->execute();

            return true;
        } catch (PDOException $e) {
            // Registrar el error
            error_log("Error al eliminar cliente: " . $e->getMessage());
            $_SESSION['error'] = "Error al eliminar cliente: " . $e->getMessage();
            return false;
        }
    }



    public function buscarClientePorId(int $id): ?array
    {
        $db = Database::getConnection();
        $query = "SELECT id, nombre, usuario, rol, password FROM clientes WHERE id = :id";
        $stmt = $db->prepare($query);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
        return $cliente ?: null;
    }



    public function actualizarClienteConPassword(int $id, string $nombre, string $usuario, string $password): bool
    {
        try {
            $db = Database::getConnection();
            $query = "UPDATE clientes SET nombre = :nombre, usuario = :usuario, password = :password WHERE id = :id";
            $stmt = $db->prepare($query);

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':nombre', $nombre);
            $stmt->bindValue(':usuario', $usuario);
            $stmt->bindValue(':password', $password);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar cliente: " . $e->getMessage());
            return false;
        }
    }






    /* VALORACIONES */


    public function puedeValorar(int $clienteId, int $productoId): bool
    {
        $db = Database::getConnection();
        $query = "SELECT COUNT(*) FROM detalle_pedidos dp
                  JOIN pedidos p ON dp.pedido_id = p.id
                  WHERE dp.producto_id = :producto_id AND p.cliente_id = :cliente_id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':producto_id', $productoId, PDO::PARAM_INT);
        $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }



    public function guardarValoracion(int $productoId, int $clienteId, string $valoracion, int $puntuacion): bool
    {
        $db = Database::getConnection();
        $query = "INSERT INTO valoraciones (producto_id, cliente_id, valoracion, puntuacion) VALUES (:producto_id, :cliente_id, :valoracion, :puntuacion)";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':producto_id', $productoId, PDO::PARAM_INT);
        $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
        $stmt->bindValue(':valoracion', $valoracion);
        $stmt->bindValue(':puntuacion', $puntuacion, PDO::PARAM_INT);
        return $stmt->execute();
    }



    public function obtenerValoraciones(int $productoId): array
    {
        $db = Database::getConnection();
        $query = "SELECT v.valoracion, v.puntuacion, c.nombre, v.fecha 
                  FROM valoraciones v
                  JOIN clientes c ON v.cliente_id = c.id
                  WHERE v.producto_id = :producto_id
                  ORDER BY v.fecha DESC";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':producto_id', $productoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }









    /* PRODUCTOS */

    public function actualizarProducto(int $id, Dulce $d): bool
    {
        $db = Database::getConnection();
        $query = "UPDATE productos 
              SET nombre = :nombre, 
                  precio = :precio, 
                  categoria = :categoria, 
                  descripcion = :descripcion, 
                  porcentajeCacao = :porcentajeCacao, 
                  peso = :peso, 
                  rellenos = :rellenos, 
                  numPisos = :numPisos, 
                  minComensales = :minComensales, 
                  maxComensales = :maxComensales 
              WHERE id = :id";

        $stmt = $db->prepare($query);

        // Valores comunes
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $d->getNombre());
        $stmt->bindValue(':precio', $d->getPrecio());
        $stmt->bindValue(':categoria', $d->getCategoria());
        $stmt->bindValue(':descripcion', $d->getDescripcion());

        // Valores específicos según el tipo
        if ($d instanceof Chocolate) {
            $stmt->bindValue(':porcentajeCacao', $d->getPorcentajeCacao());
            $stmt->bindValue(':peso', $d->getPeso());
            $stmt->bindValue(':rellenos', null);
            $stmt->bindValue(':numPisos', null);
            $stmt->bindValue(':minComensales', null);
            $stmt->bindValue(':maxComensales', null);

        } elseif ($d instanceof Tarta) {
            $stmt->bindValue(':porcentajeCacao', null);
            $stmt->bindValue(':peso', null);
            $stmt->bindValue(':rellenos', implode(',', $d->getRellenos()));
            $stmt->bindValue(':numPisos', $d->getNumPisos());
            $stmt->bindValue(':minComensales', $d->getMinNumComensales());
            $stmt->bindValue(':maxComensales', $d->getMaxNumComensales());
        } else {

            // Otros tipos (Bollo)
            $stmt->bindValue(':porcentajeCacao', null);
            $stmt->bindValue(':peso', null);
            $stmt->bindValue(':rellenos', null);
            $stmt->bindValue(':numPisos', null);
            $stmt->bindValue(':minComensales', null);
            $stmt->bindValue(':maxComensales', null);
        }

        return $stmt->execute();
    }



    public function eliminarProducto($id)
    {
        try {
            $db = Database::getConnection();
            $db->beginTransaction();

            // Eliminar registros relacionados en detalles_tarta
            $stmtDetallesTarta = $db->prepare("DELETE FROM detalles_tarta WHERE producto_id = :id");
            $stmtDetallesTarta->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtDetallesTarta->execute();

            // Eliminar registros relacionados en detalle_pedidos
            $stmtDetalle = $db->prepare("DELETE FROM detalle_pedidos WHERE producto_id = :id");
            $stmtDetalle->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtDetalle->execute();

            // Luego eliminar el producto
            $stmtProducto = $db->prepare("DELETE FROM productos WHERE id = :id");
            $stmtProducto->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtProducto->execute();
            $db->commit();

            return true;

        } catch (PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            // Retornar el mensaje al usuario
            return "Error al eliminar el producto: " . $e->getMessage();
        }
    }



    public function buscarProductoPorId(int $id): ?Dulce
    {
        $db = Database::getConnection();
        $query = "SELECT * FROM productos WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();


        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }


        // Crear la instancia del producto basado en el tipo
        switch ($row['tipo']) {
            case 'Bollo':
                return new Bollo(
                    $row['id'],
                    $row['nombre'],
                    $row['precio'],
                    $row['descripcion'] ?? '',
                    $row['categoria'],
                    $row['relleno'] ?? ''
                );

            case 'Chocolate':
                return new Chocolate(
                    $row['id'],
                    $row['nombre'],
                    $row['precio'],
                    $row['descripcion'] ?? '',
                    $row['categoria'],
                    $row['porcentajeCacao'] ?? 0,
                    $row['peso'] ?? 0
                );

            case 'Tarta':
                return new Tarta(
                    $row['id'],
                    $row['nombre'],
                    $row['precio'],
                    $row['descripcion'] ?? '',
                    $row['categoria'],
                    explode(',', $row['rellenos'] ?? ''), // Convierte a array
                    $row['numPisos'] ?? 1,
                    $row['minNumComensales'] ?? 2,
                    $row['maxNumComensales'] ?? 2
                );

            default:
                return null;
        }
    }



    public function incluirProducto(Dulce $d): void
    {
        $this->productos[] = $d;
        echo "Producto añadido: {$d->getNombre()}.\n";
    }



    public function listarProductos(): void
    {
        echo "Productos disponibles en la pastelería:\n";
        foreach ($this->productos as $producto) {
            echo "- {$producto->muestraResumen()}\n";
        }
    }



    public function buscarProducto(string $nombre): ?Dulce
    {
        try {
            foreach ($this->productos as $producto) {
                if ($producto->getNombre() === $nombre) {
                    return $producto;
                }
            }
            throw new DulceNoEncontradoException("Producto no encontrado: {$nombre}");
        } catch (DulceNoEncontradoException $e) {
            echo $e->getMessage();
            return null;
        }
    }




    public function guardarProducto(Dulce $d): bool
    {
        $db = Database::getConnection();

        $query = "INSERT INTO productos 
                  (nombre, precio, categoria, tipo, descripcion, porcentajeCacao, peso, rellenos, numPisos, minComensales, maxComensales) 
                  VALUES (:nombre, :precio, :categoria, :tipo, :descripcion, :porcentajeCacao, :peso, :rellenos, :numPisos, :minComensales, :maxComensales)";

        $stmt = $db->prepare($query);

        // Valores comunes
        $stmt->bindValue(':nombre', $d->getNombre());
        $stmt->bindValue(':precio', $d->getPrecio());
        $stmt->bindValue(':categoria', $d->getCategoria());
        $stmt->bindValue(':tipo', get_class($d));
        $stmt->bindValue(':descripcion', $d->getDescripcion());

        // Valores específicos según el tipo
        if ($d instanceof Chocolate) {
            $stmt->bindValue(':porcentajeCacao', $d->getPorcentajeCacao());
            $stmt->bindValue(':peso', $d->getPeso());
            $stmt->bindValue(':rellenos', null);
            $stmt->bindValue(':numPisos', null);
            $stmt->bindValue(':minComensales', null);
            $stmt->bindValue(':maxComensales', null);
        } elseif ($d instanceof Tarta) {
            $stmt->bindValue(':porcentajeCacao', null);
            $stmt->bindValue(':peso', null);
            $stmt->bindValue(':rellenos', implode(',', $d->getRellenos()));
            $stmt->bindValue(':numPisos', $d->getNumPisos());
            $stmt->bindValue(':minComensales', $d->getMinNumComensales());
            $stmt->bindValue(':maxComensales', $d->getMaxNumComensales());
        } else {
            // Otros tipos (Bollo)
            $stmt->bindValue(':porcentajeCacao', null);
            $stmt->bindValue(':peso', null);
            $stmt->bindValue(':rellenos', null);
            $stmt->bindValue(':numPisos', null);
            $stmt->bindValue(':minComensales', null);
            $stmt->bindValue(':maxComensales', null);
        }

        return $stmt->execute();
    }



    public function obtenerProductos(): array
    {
        $db = Database::getConnection();
        $query = "SELECT * FROM productos";
        $stmt = $db->query($query);
        $productos = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            switch ($row['tipo']) {
                case 'Bollo':
                    $productos[] = new Bollo(
                        $row['id'],
                        $row['nombre'],
                        $row['precio'],
                        $row['descripcion'] ?? '',
                        $row['categoria'],
                        $row['relleno'] ?? ''
                    );
                    break;

                case 'Chocolate':
                    $productos[] = new Chocolate(
                        $row['id'],
                        $row['nombre'],
                        $row['precio'],
                        $row['descripcion'] ?? '',
                        $row['categoria'],
                        $row['porcentajeCacao'] ?? 0,
                        $row['peso'] ?? 0
                    );
                    break;

                case 'Tarta':
                    $productos[] = new Tarta(
                        $row['id'],
                        $row['nombre'],
                        $row['precio'],
                        $row['descripcion'] ?? '',
                        $row['categoria'],
                        explode(',', $row['rellenos'] ?? ''),
                        $row['numPisos'] ?? 1,
                        $row['minComensales'] ?? 2,
                        $row['maxComensales'] ?? 2
                    );
                    break;

                default:
                    echo "Tipo desconocido: {$row['tipo']}";
            }
        }
        return $productos;
    }




    /* PEDIDOS */

    public function obtenerHistorialPedidos(int $clienteId): array
    {
        try {
            $db = Database::getConnection();
            $query = "SELECT p.id AS pedido_id, p.fecha, dp.producto_id, dp.cantidad, dp.precio_unitario, prod.nombre, prod.tipo
                  FROM pedidos p
                  JOIN detalle_pedidos dp ON p.id = dp.pedido_id
                  JOIN productos prod ON dp.producto_id = prod.id
                  WHERE p.cliente_id = :cliente_id
                  ORDER BY p.fecha DESC";
            $stmt = $db->prepare($query);
            $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener historial de pedidos: " . $e->getMessage());
            return [];
        }
    }



    public function obtenerPedidosRecientes(int $clienteId, int $limite = 3): array
    {
        try {
            $db = Database::getConnection();
            $query = "SELECT p.id AS pedido_id, p.fecha, SUM(dp.cantidad * dp.precio_unitario) AS total
                  FROM pedidos p
                  JOIN detalle_pedidos dp ON p.id = dp.pedido_id
                  WHERE p.cliente_id = :cliente_id
                  GROUP BY p.id, p.fecha
                  ORDER BY p.fecha DESC
                  LIMIT :limite";
            $stmt = $db->prepare($query);
            $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener pedidos recientes: " . $e->getMessage());
            return [];
        }
    }


}
?>