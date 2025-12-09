# Docker Setup para Saldop

## Estructura Docker

El proyecto está configurado con Docker Compose con los siguientes servicios:

- **app**: PHP 8.2-FPM (aplicación Symfony 7.4)
- **web**: Nginx 1.25 (servidor web)
- **database**: PostgreSQL 16 (base de datos)

## Requisitos

- Docker
- Docker Compose

## Pasos para levantar el entorno

### 1. Construir la imagen
```bash
docker compose build
```

### 2. Levantar los servicios
```bash
docker compose up -d
```

### 3. Instalar dependencias (si es necesario)
```bash
docker compose exec app composer install
```

### 4. Crear la base de datos
```bash
docker compose exec app php bin/console doctrine:database:create
```

### 5. Ejecutar migraciones
```bash
docker compose exec app php bin/console doctrine:migrations:migrate
```

## Acceso a la aplicación

- **URL**: http://localhost:8080
- **Base de datos**: PostgreSQL en `localhost:5432`
  - Usuario: `app`
  - Contraseña: `!ChangeMe!`
  - Base de datos: `app`

## Comandos útiles

### Ver logs
```bash
docker compose logs -f app
docker compose logs -f web
docker compose logs -f database
```

### Ejecutar comandos en el contenedor app
```bash
docker compose exec app php bin/console <comando>
docker compose exec app composer <comando>
```

### Detener los servicios
```bash
docker compose down
```

### Detener y eliminar volúmenes (cuidado: borra la BD)
```bash
docker compose down -v
```

## Notas

- El código está montado como volumen, así que los cambios se reflejan inmediatamente
- La base de datos persiste en el volumen `database_data`
- En producción, cambia las contraseñas en `.env`
