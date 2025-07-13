# Proyecto Laravel

Este proyecto es una aplicación desarrollada con [Laravel](https://laravel.com/), un framework PHP moderno y robusto para el desarrollo de aplicaciones web.

## Requisitos

Antes de comenzar, asegúrate de tener instalado lo siguiente:

- PHP >= 8.1
- Composer
- Laravel >= 10
- MySQL o PostgreSQL
- Node.js y NPM (opcional, para compilar assets)
- Docker (opcional)

## Instalación

1. **Clonar el repositorio:**
2. Instalar las dependencias de PHP:
    - composer install
3. Copiar el archivo de entorno y configurarlo:
    - cp .env.example .env
4  Generar la clave de la aplicación:
    - php artisan key:generate
5  Configurar Cadena de conexion.
    - En el .env DB_CONNECTION=sqlsrv colóquelo de esa manera.
6  Crea la base de datos
    - Create database TecnicaTT
7  Levantar el servidor local
8  Ir al swagger http://localhost:8000/api/documentation
 
## Ejecutar Pruebas Unitarias.
1. Ir a la carpeta routes al archivo api.php y sacar todas las rutas del middleware
2. Ejecutar php artisan test

