# 🚀 Guía de Despliegue: SysSAFE Carnet (Dokploy + MySQL + Firebase)

Este documento resume el proceso de despliegue realizado para llevar la plataforma **SysSAFE** de un entorno local (XAMPP) a un entorno de producción profesional (Dokploy VPS).

---

## 🛠️ Stack Tecnológico
*   **Servidor:** VPS Ubuntu con Dokploy (Docker Swarm).
*   **Frontend/Backend:** Laravel 11 (PHP 8.3-fpm Alpine).
*   **Servidor Web:** Nginx optimizado para PHP-FPM.
*   **Base de Datos:** MySQL (Dokploy Service).
*   **Sincronización:** Firebase Firestore (REST API).

---

## 📁 Archivos Críticos y Configuración

### 1. Dockerfile & Entrypoint
*   **Optimización:** Se reemplazó la compilación manual por `php-extension-installer` para builds ultra-rápidos.
*   **Bypass de gRPC:** Se configuró Composer para ignorar `ext-grpc` ya que usamos la API REST de Google, lo que reduce el tiempo de build de 20 minutos a 2 minutos.
*   **Permisos:** El sistema corre bajo el usuario `www-data` para evitar errores de escritura en `storage` y `database`.

### 2. Variables de Entorno (Dokploy Panel)
Las variables más importantes configuradas en la pestaña **Environment** de Dokploy:
*   `APP_KEY`: Clave de cifrado de producción (crucial para sesiones y seguridad).
*   `DB_HOST`: Host interno de Dokploy (ej: `carnetsystem-safesyscarnet-85oc1m`).
*   `FIREBASE_CREDENTIALS`: Ruta al JSON montado (`storage/app/firebase-auth.json`).

---

## 💾 Migración de Base de Datos (XAMPP -> VPS)

Para el paso inicial, exportamos los datos de XAMPP e inyectamos el archivo `.sql` en el contenedor de producción siguiendo este flujo:

1.  **Exportar (PC):** `mysqldump -u root -p sys_safe_carnet > backup.sql`
2.  **Subir (PC -> VPS):** `scp backup.sql root@187.127.9.16:/root/backup.sql`
3.  **Wipe & Import (VPS):**
    ```bash
    # Borrar y recrear DB vacía
    docker exec -i NOMBRE_CONTENEDOR_DB mysql -u root -p@CONTRASEÑA -e "DROP DATABASE sys_safe_carnet; CREATE DATABASE sys_safe_carnet;"
    
    # Inyectar Backup
    cat /root/backup.sql | docker exec -i NOMBRE_CONTENEDOR_DB mysql -u root -p@CONTRASEÑA sys_safe_carnet
    ```

---

## ☁️ Sincronización con Firebase

El sistema cuenta con un motor de sincronización bidireccional:

### 🔄 Sincronización de Datos Reales (Firestore)

Una vez el sistema esté desplegado, el flujo de datos para mover la "Fuente de Verdad" (CMD) hacia la Nube y Producción es el siguiente:

### 1. Limpieza de Firebase (Borrar Datos Obsoletos)
Si hay errores en los datos de la nube, límpialos desde la carpeta local **`sys_safe_carnet`**:
```powershell
php artisan firebase:wipe
```
*Este comando borra todas las empresas, afiliados y roles de Firebase para empezar de cero.*

### 2. Subida desde CMD (Source of Truth)
En la carpeta local **`sys_carnet`**, usa los comandos divididos por seguridad y control:

*   **Subir Empresas**:
    ```powershell
    php artisan firebase:push --companies
    ```
*   **Subir Afiliados (6,000+ registros)**:
    ```powershell
    php artisan firebase:push --affiliates
    ```
    *Nota: Este proceso puede tardar unos minutos. Incluye una barra de progreso.*

### 3. Descarga en Producción (VPS)
Para actualizar el servidor con los datos reales subidos, entra a tu SSH y ejecuta:

```bash
docker exec -it $(docker ps -q --filter "name=systemcarnet") php artisan firebase:pull-all --full
```

### 🛠️ Reparación Post-Importación (Fix 404)
Si tras importar datos masivamente los enlaces de los afiliados o empresas dan **Error 404**, es porque les falta el identificador seguro (UUID). Ejecuta este comando para repararlos:
```bash
php artisan db:fix-uuids
```

## 🔧 Comandos de Mantenimiento Comunes

Si el sistema da errores 500 o los cambios no se ven reflejados, ejecuta estos comandos en la terminal de Dokploy o VPS SSH:

```bash
# Limpiar configuración y caché (Vital tras cambios en .env)
php artisan config:clear
php artisan cache:clear

# Forzar reinicio de permisos (Spatie)
php artisan permission:cache-reset

# Generar enlace de almacenamiento público
php artisan storage:link --force
```

---

---

## 💡 Notas de Configuración Especiales
- **Mensajeros**: La cédula ahora es un campo **opcional** (nullable). Se puede registrar solo con nombre y vehículo si es necesario.
- **Seguridad de Rutas**: Todas las rutas públicas usan UUIDs. El comando `db:fix-uuids` es vital para mantener la integridad tras migraciones manuales de SQL.

**¡Despliegue completado con éxito!** 🎉
*Documentado por: Antigravity AI el 07 de Abril de 2026 (Actualización de UUIDs y Mensajeros).*
