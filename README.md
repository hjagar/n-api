# 🚀 N-API

Una **API REST** en PHP 7.4 con Apache, MySQL/MariaDB y Docker Compose, lista para pruebas locales y para pasar a producción fácilmente.

---

## 📁 Estructura del proyecto

```
n-api/
├── docker-compose.yml
├── Dockerfile
├── composer.json
├── virtualhost.conf
├── src/
│   ├── index.php
│   ├── .env
│   ├── .htaccess
├── db/
│   └── schema.sql
```

---

## 🧩 ¿Qué incluye?

- 🐘 **Apache + PHP 7.4**
- 🔌 **PDO MySQL** habilitado
- 📦 **Composer** ya instalado en el contenedor
- 🔄 **mod_rewrite** activado para routing limpio con `.htaccess`
- 🗄️ **Base de datos MariaDB** con un `schema.sql` de ejemplo
- ⚙️ **Variables de entorno** en `.env`
- 🔒 **VirtualHost de ejemplo con SSL** (Let’s Encrypt) para producción

---

## 🚦 Cómo usar

### 1️⃣ Clonar o descomprimir el proyecto

```bash
git clone https://github.com/hjagar/n-api.git n-api
cd n-api
```
_O descomprimir el archivo ZIP._

### 2️⃣ Levantar la API con Docker

```bash
docker-compose up --build
```
Esto construye la imagen PHP 7.4 + Apache, crea el contenedor de la API y el contenedor de la base de datos con datos de ejemplo.

### 3️⃣ Acceder

Tu API estará disponible en:  
[http://localhost:8080](http://localhost:8080)

**Ejemplo de ruta:**  
[http://localhost:8080/api/hello](http://localhost:8080/api/hello)

### 4️⃣ Instalar dependencias con Composer (opcional)

Si agregas paquetes:
```bash
docker-compose exec app composer require <vendor/package>
```

---

## 📄 Archivos importantes

- `.env`: Configura modo desarrollo (`IS_DEV`), API Key opcional (`API_KEY`) y conexión a la base de datos.
- `.htaccess`: Redirige todas las rutas a `index.php` para que funcionen las rutas RESTful.
- `virtualhost.conf`: Ejemplo de VirtualHost Apache para producción con HTTPS (Let’s Encrypt).

---

## 🚀 Producción

- Usa el mismo `src/` en tu servidor Apache.
- Instala **certbot** para Let’s Encrypt.
- Apunta el VirtualHost a `/var/www/html/src`.
- Activa **mod_rewrite**.

---

## 🗄️ Inicializar base de datos

El contenedor `db` carga `db/schema.sql` automáticamente la primera vez.

---

## ⚙️ Requisitos

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

---

## 📜 Licencia

Uso libre para pruebas, demos y adaptaciones.

---

**Hecho por:** Hjagarsoft