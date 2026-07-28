# Sistema de Gestion Hotelera

Proyecto academico: PHP puro (PDO), MySQL, HTML/CSS/JS. Sin frameworks, sin contenedores.

## 1. Modelo Entidad-Relacion

Tablas (basadas en el diagrama: HABITACION, TIPO_HABITACION, CLIENTE, RESERVA, SERVICIOS, GASTOS):

- **cliente** — datos personales del huesped.
- **tipo_habitacion** — categorias (Sencilla, Doble, Suite) con precio/noche y capacidad.
- **habitacion** — habitaciones fisicas, cada una asociada a un tipo_habitacion (FK) y con estado.
- **reserva** — vincula cliente + habitacion, con fechas y total (calculado automaticamente).
- **servicio** — servicios adicionales del hotel (desayuno, spa, lavanderia, etc).
- **gasto** — consumo de un servicio asociado a una reserva (cantidad x precio = subtotal).

Relaciones: 1 tipo_habitacion → N habitaciones. 1 cliente → N reservas. 1 habitacion → N reservas.
1 reserva → N gastos. 1 servicio → N gastos.

Ver el script completo en `database.sql`.

## 2. Instalacion (XAMPP / localhost)

1. Copia la carpeta `hotel_system` dentro de `htdocs` (ej: `C:\xampp\htdocs\hotel_system`).
2. Abre phpMyAdmin (o consola MySQL) e importa `database.sql`. Esto crea la base `hotel_db`
   con las 6 tablas y datos de prueba.
3. Revisa `config/conexion.php` si tu usuario/clave de MySQL no son los de XAMPP por defecto
   (por defecto: usuario `root`, sin clave).
4. Inicia Apache y MySQL desde el panel de XAMPP.
5. Abre en el navegador: `http://localhost/hotel_system/index.php`

## 3. Estructura del proyecto

```
hotel_system/
├── database.sql                 -> Script SQL (MER completo + datos de prueba)
├── config/
│   ├── conexion.php              -> Conexion PDO a MySQL
│   └── funciones.php             -> Helpers: respuestas JSON, validaciones
├── api/                          -> LOGICA DE NEGOCIO (backend)
│   ├── clientes.php
│   ├── tipos_habitacion.php
│   ├── habitaciones.php
│   ├── reservas.php               (calcula total automaticamente, sincroniza estado de habitacion)
│   ├── servicios.php
│   └── gastos.php                 (calcula subtotal, actualiza total de la reserva)
├── assets/
│   ├── css/style.css              -> Estilos de toda la interfaz
│   └── js/                        -> LOGICA DE INTERFAZ (frontend, fetch a la API)
├── index.php                     -> Dashboard con indicadores
├── clientes.php / tipos_habitacion.php / habitaciones.php / reservas.php / servicios.php / gastos.php
└── nav.php                       -> Menu de navegacion comun
```

## 4. Como se dividen los 3 puntos pedidos

1. **Modelo Entidad-Relacion SQL** → `database.sql`
2. **Interfaces graficas (Frontend)** → los archivos `.php` de paginas + `assets/css` + `assets/js`
   (HTML generado, CSS y JS puro, consumen la API vía `fetch`)
3. **Logica del negocio (Backend)** → los archivos dentro de `api/` (PDO, validaciones, calculos,
   transacciones)

## 5. Validaciones incluidas

- Campos obligatorios no vacios en cada formulario (frontend `required` + backend).
- Cedula: solo numeros. Email: formato valido.
- Precios y capacidades: deben ser numericos y mayores a 0.
- Fechas de reserva: formato valido y fecha de salida posterior a la de entrada.
- Manejo de errores de PDO (duplicados por UNIQUE, llaves foraneas en uso) devueltos como
  mensajes claros en JSON, nunca como errores crudos de PHP/MySQL.
- Transacciones (`beginTransaction`/`commit`/`rollBack`) en reservas y gastos para mantener
  consistencia entre tablas relacionadas.

## 6. Para la defensa

Cada modulo sigue el mismo patron: la pagina PHP solo genera el HTML del formulario y la tabla;
todo el CRUD real ocurre por peticiones `fetch` en JavaScript hacia `api/<modulo>.php`, que usa
PDO con sentencias preparadas (proteccion contra inyeccion SQL) y devuelve JSON. El calculo del
total de una reserva y del subtotal de un gasto se hace en el backend, no en el frontend, para
evitar que se pueda manipular desde el navegador.
