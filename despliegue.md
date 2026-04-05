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

### Descargar desde Firebase (Pull)
Usa este comando en el servidor para traer empresas, roles y usuarios:
```bash
docker exec -it $(docker ps -q --filter "name=systemcarnet") php artisan firebase:pull-all --full
```

### Subir a Firebase (Push)
Usa este comando en tu PC Local para subir tus cambios de XAMPP a la nube:
```bash
php artisan firebase:push
```

---

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

**¡Despliegue completado con éxito!** 🎉
*Documentado por: Antigravity AI el 02 de Abril de 2026.*
