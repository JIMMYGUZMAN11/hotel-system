# Cómo subir este proyecto a Render

Render no tiene runtime nativo para PHP ni MySQL gestionado, así que usamos:
- **Docker** para correr el sitio PHP (ya incluido: `Dockerfile`).
- Una **base de datos MySQL externa** (Render no ofrece MySQL, solo Postgres/Redis).

## Paso 1: Crear una base de datos MySQL externa

Opciones gratuitas/económicas comunes:
- **Railway** (railway.app) — MySQL con plan gratuito limitado.
- **Aiven** (aiven.io) — free tier con MySQL.
- **Clever Cloud** — plan gratuito pequeño de MySQL.
- **FreeSQLDatabase.com** — gratis, solo para pruebas/demos.

Una vez creada, anota estos datos: host, puerto, nombre de la base, usuario y contraseña.

Importa el esquema: usa el archivo `database.sql` de este proyecto contra esa base
(por ejemplo con `mysql -h HOST -P PUERTO -u USUARIO -p NOMBRE_DB < database.sql`,
o desde el panel/phpMyAdmin que te dé el proveedor).

## Paso 2: Subir el proyecto a GitHub

1. Crea un repositorio nuevo en GitHub.
2. Sube esta carpeta completa (incluye el `Dockerfile`).

```bash
cd hotel_system
git init
git add .
git commit -m "Proyecto listo para Render"
git branch -M main
git remote add origin https://github.com/TU_USUARIO/TU_REPO.git
git push -u origin main
```

## Paso 3: Crear el servicio en Render

1. Entra a https://dashboard.render.com y haz clic en **New +** → **Web Service**.
2. Conecta tu repositorio de GitHub.
3. En "Runtime", elige **Docker** (Render detectará el `Dockerfile` automáticamente).
4. En "Environment Variables", agrega:
   - `DB_HOST` = host de tu MySQL externo
   - `DB_PORT` = puerto (normalmente 3306)
   - `DB_NAME` = nombre de la base de datos
   - `DB_USER` = usuario
   - `DB_PASS` = contraseña
5. Deja el resto de opciones por defecto y haz clic en **Create Web Service**.

Render construirá la imagen Docker y te dará una URL pública tipo
`https://tu-app.onrender.com`. Ahí debería cargar `index.php` directamente.

## Notas

- El plan gratuito de Render "duerme" el servicio tras inactividad; la primera
  petición después de dormir tarda unos segundos en responder.
- Si algo falla, revisa los "Logs" en el dashboard de Render: ahí verás errores
  de PHP o de conexión a la base de datos.
- Si prefieres no usar Docker, la alternativa más simple para hosting PHP+MySQL
  clásico (sin tocar nada del código) es un hosting compartido tipo Hostinger,
  InfinityFree o 000webhost, que sí soportan PHP/MySQL "tal cual" como XAMPP.
