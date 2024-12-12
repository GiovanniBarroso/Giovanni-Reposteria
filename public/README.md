Proyecto: Pastelería Online

Este proyecto es una aplicación web para gestionar una pastelería online. Incluye funcionalidades para administradores y clientes, permitiendo la gestión de productos, pedidos y usuarios.

Tecnologías Utilizadas

Frontend:

HTML5

CSS3 con Bootstrap 5 para un diseño responsivo y moderno

JavaScript (para validaciones y mejoras de interfaz)

Backend:

PHP 8

Base de datos MySQL

Servidor:

Apache (XAMPP para desarrollo local)

Funcionalidades Principales

Administrador

Gestín de productos:

Crear, editar y eliminar productos.

Soporte para distintos tipos de productos (Bollo, Chocolate, Tarta) con características específicas.

Gestín de usuarios:

Visualizar, editar y eliminar usuarios.

Asignar roles de cliente o administrador.

Visualización de pedidos:

Historial de pedidos con detalles completos.

Cliente

Gestín de perfil:

Editar información personal y contraseña.

Realización de pedidos:

Seleccionar productos y confirmar compras.

Historial de pedidos:

Ver detalles de pedidos anteriores.

Instalación

Clona este repositorio:

git clone https://github.com/GiovanniBarroso/Giovanni-Reposteria

Configura el entorno local:

Instala XAMPP o un entorno LAMP equivalente.

Copia los archivos del proyecto en el directorio htdocs de XAMPP.

Configura la base de datos:

Importa el archivo create_db.sql ubicado en el directorio db.

Actualiza las credenciales de conexión en el archivo Database.php.

Inicia el servidor:

Asegúrate de que Apache y MySQL estén activos.

Accede a la aplicación desde el navegador:

http://localhost/pastelería/public/util/index.php

Estructura del Proyecto

public/: Archivos accesibles públicamente (HTML, CSS, JS).

index.php: Página de inicio.

registro.php: Formulario de registro.

main.php: Página principal para clientes.

mainAdmin.php: Panel de administración.

src/: Clases PHP.

Pasteleria.php: Gestión de lógica principal.

Tarta.php, Bollo.php, Chocolate.php: Clases específicas de productos.

Pedido.php: Gestión de pedidos.

db/: Archivos relacionados con la base de datos.

Database.php: Conexión a la base de datos.

create_db.sql: Script para crear la base de datos.

css/ y js/: Archivos estáticos para estilos y funcionalidades.

Uso

Acceso como Administrador

Crea un usuario con rol admin mediante la base de datos o el registro (si está habilitado).

Inicia sesión y accede al panel de administración.

Acceso como Cliente

Regístrate como cliente.

Navega por los productos y realiza pedidos.

Consulta tu historial de pedidos.

Próximas Mejora

Integración de un sistema de envío de correos para notificaciones.

Implementación de pasarelas de pago.

Soporte para subir imágenes de productos desde el panel de administración.

Contribuciones

Las contribuciones son bienvenidas. Por favor, sigue las mejores prácticas de desarrollo y abre un pull request para revisión.

Licencia

Este proyecto está bajo la licencia MIT. Consulta el archivo LICENSE para más información.

Desarrollado por Giovanni Barroso Álvarez.