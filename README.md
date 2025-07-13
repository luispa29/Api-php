# Proyecto Laravel

Este proyecto es una aplicación desarrollada con [Laravel]
## Requisitos

Antes de comenzar, asegúrate de tener instalado lo siguiente:

- PHP >= 8.1
- Composer
- Laravel >= 10
- Docker  para la base de datos

## Crear contenedor para la base de datos
1. Abrir cmd y ejecutar los comandos:
   - docker pull mcr.microsoft.com/mssql/server:2022-latest
   - docker run -e "ACCEPT_EULA=Y" -e "MSSQL_SA_PASSWORD=Admin123@" \
   -p 1433:1433 --name test --hostname test \
2. Ingrear a la base de datos y ejecutar el comando:
      - Create database TecnicaTT

## Instalación

1. **Clonar el repositorio:**
2. Instalar las dependencias de PHP:
    - composer install
3. Copiar el archivo de entorno y configurarlo:
    - cp .env.example .env      
4.  Generar la clave de la aplicación:
    - php artisan key:generate.
5.  Configurar Cadena de conexion.
    - En el .env DB_CONNECTION=sqlsrv colóquelo de esa manera.
6.  Levantar el servidor local
7.  Ir al swagger http://localhost:8000/api/documentation
 
## Ejecutar Pruebas Unitarias.
1. Ir a la carpeta routes al archivo api.php y sacar todas las rutas del middleware
2. Ejecutar php artisan test

