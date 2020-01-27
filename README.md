# 3 Proyectos - Introducción a Laravel

Cada carpeta incluye un proyecto.

## Installation

Ingresamos a cualquier proyecto

```bash
cd proyecto
```
Instalamos sus dependencias

```bash
composer install
```
Creamos el archivo de configuración

```bash
cp .env.example .env
```
Finalmente configuramos los datos de nuestra base de datos y ejecutamos migraciones.

```bash
php artisan migrate:refresh --seed
```

## Usage

Verifica el sistema desde el navegador. 

## License
[MIT](https://choosealicense.com/licenses/mit/)