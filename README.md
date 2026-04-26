# my-uptime

Un sistema de monitoreo de tiempo de actividad (Uptime Monitor) autoalojado, construido con **Laravel 12**, **Livewire** y **Volt**.  
Permite supervisar sitios web y servidores mediante comprobaciones HTTP y Ping, rastreando el tiempo de actividad, tiempos de respuesta y registrando incidentes.


## 🚀 Características Principales

-   **Monitoreo Multi-tipo**:
    -   **HTTP(s)**: Soporta métodos GET, POST, PUT, DELETE.
    -   **Ping**: Comprobación de conectividad ICMP.
-   **Aserciones de Contenido**: Verifica si una palabra clave específica existe (o no existe) en la respuesta HTTP.
-   **Intervalos y Timeouts**: Configuración personalizada para tiempos de espera.
-   **Estadísticas de Uptime**: Cálculo automático de porcentaje de actividad para 12h, 24h, 7 días y 30 días.
-   **Historial de Logs**: Registro detallado de cada comprobación con tiempos de respuesta y códigos de estado.
-   **Interfaz Reactiva**: Panel de control moderno y rápido construido con **Mary UI** y **Livewire Volt**.
-   **Gestión de Usuarios**: Sistema de autenticación completo (Login, Registro, Equipo).

## 🛠️ Stack Tecnológico

-   **Framework**: [Laravel 12](https://laravel.com)
-   **Frontend**: [Livewire](https://livewire.laravel.com) + [Volt](https://livewire.laravel.com/docs/volt)
-   **Componentes UI**: [Mary UI](https://mary-ui.com)
-   **Base de Datos**: SQLite (por defecto, configurable a MySQL/PostgreSQL)
-   **Estilos**: [Tailwind CSS](https://tailwindcss.com)

## 📋 Requisitos Previos

Asegúrate de tener instalado en tu sistema:

-   PHP >= 8.2
-   Composer
-   Node.js & NPM

## ⚡ Instalación

Sigue estos pasos para levantar el proyecto localmente:

1.  **Clonar el repositorio**
    ```bash
    git clone https://github.com/LucasAraujoM/my-uptime.git
    cd my-uptime
    ```

2.  **Instalar dependencias de PHP**
    ```bash
    composer install
    ```

3.  **Instalar dependencias de Frontend**
    ```bash
    npm install
    npm run build
    ```

4.  **Configurar entorno**
    Copia el archivo de ejemplo y genera la clave de la aplicación:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Nota: Por defecto está configurado para usar SQLite. Si deseas usar otra base de datos, edita el archivo `.env`.*

5.  **Ejecutar migraciones**
    Crea las tablas en la base de datos:
    ```bash
    php artisan migrate
    ```

## 🏃‍♂️ Ejecución (Desarrollo)

El proyecto incluye un script conveniente en `composer.json` que inicia todos los procesos necesarios (servidor, colas, planificador y vite) en un solo comando:

```bash
composer run dev
```

Este comando ejecuta concurrentemente:
-   `php artisan serve`: Servidor web.
-   `php artisan queue:listen`: Procesador de trabajos en segundo plano (necesario para las comprobaciones).
-   `php artisan schedule:work`: Ejecutor del planificador (necesario para programar los monitoreos).
-   `npm run dev`: Servidor de desarrollo de Vite.

## 🕰️ Programación de Tareas (Producción)

En un entorno de producción, debes configurar el cron del sistema para que ejecute el planificador de Laravel cada minuto:

```bash
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

Además, asegúrate de tener un supervisor para procesar las colas (`php artisan queue:work`).

## 🤝 Contribuir

¡Las contribuciones son bienvenidas! Por favor, abre un issue o envía un Pull Request para mejoras o correcciones.

## 📄 Licencia

Este proyecto es software de código abierto licenciado bajo la [MIT license](https://opensource.org/licenses/MIT).
