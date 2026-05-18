# 🚀 Guía de Despliegue y Mantenimiento: SysSAFE-Carnet v2.1

Este documento detalla el proceso para realizar actualizaciones seguras en el sistema, aprovechando el nuevo **Centro de Control (DevOps Dashboard)** implementado para eliminar la dependencia de la consola SSH.

---

## 1. Fase de Desarrollo y Repositorio (Local)
Cada vez que realices cambios en el código local:

1. **Guardar cambios:** `git add .`
2. **Commit:** `git commit -m "Descripción clara de la mejora"`
3. **Subir al servidor:** `git push origin main`

---

## 2. Fase de Despliegue Automático (Dokploy)
1. Entra a tu panel de **Dokploy**.
2. Verifica que el **Build** haya finalizado (esto genera la nueva imagen de Docker).
3. Asegúrate de que el contenedor esté en estado **"Running"**.

---

## 3. Fase de Activación (Panel de Control Web)
¡Ya no necesitas SSH! Ahora usa la interfaz del sistema:

1. Accede a `https://admin.discan.cloud/sistema/control` (Centro de Control).
2. **Paso A: Migrar Base de Datos.** Si subiste cambios en las tablas, pulsa el botón azul. Verifica el resultado en la consola integrada.
3. **Paso B: Optimizar Caché.** Pulsa este botón para que Laravel reconozca las nuevas rutas y configuraciones.
4. **Paso C: Reiniciar Workers.** (CRÍTICO) Pulsa este botón para que el **Supervisor** cargue el código nuevo en los procesos de fondo.

---

## 4. Configuraciones Críticas (.env)
Asegúrate de que estas variables estén configuradas en Dokploy para que la seguridad funcione:

| Variable | Valor / Función |
| :--- | :--- |
| `FIREBASE_WEBHOOK_SECRET` | Clave para la firma HMAC (Debe coincidir con Firebase Cloud Functions). |
| `FIREBASE_PROJECT_ID` | Tu ID de proyecto en Firebase. |
| `QUEUE_CONNECTION` | Debe estar en `database` o `redis` para que Supervisor funcione. |

---

## 🛡️ Mejoras Logradas (v2.1) - Resumen de Auditoría
Hoy transformamos el sistema en una plataforma de grado ERP:

*   **Seguridad HMAC:** Los Webhooks de Firebase ahora son inviolables gracias a la validación de firmas criptográficas.
*   **Circuit Breaker:** El sistema protege al VPS. Si Firebase falla, el sistema suspende la conexión temporalmente para evitar bloqueos y reintenta después.
*   **Colas de Prioridad:** 
    *   `sync-high`: Sincronización inmediata de Afiliados.
    *   `sync-low`: Geocodificación y tareas pesadas.
*   **Auto-Cierre Autómata:** El sistema evalúa evidencias y cierra expedientes automáticamente cuando están completos.
*   **Scheduler Nocturno:** Cada noche a las 02:00 AM, el sistema se "auto-limpia" y concilia datos con Firebase.

---

## ⚠️ Checklist de Emergencia y Gotchas Conocidos

### 1. El Gran Gotcha de la Caché en Docker (Dokploy)
> [!IMPORTANT]
> Cuando Dokploy compila la imagen de Docker, ejecuta `php artisan optimize` en frío. En esta etapa de compilación, el constructor **no tiene acceso a tus variables de entorno reales de Dokploy** (como `FIREBASE_CREDENTIALS_JSON`). 
> Esto hace que Laravel cachee las credenciales de Firebase como `null` en el contenedor recién creado.
>
> **Solución Directa (SSH):**
> Si la sincronización web se queda colgada al 0% o te da alerta de credenciales inválidas en los logs tras un despliegue, ejecuta este comando único en tu terminal SSH del VPS para limpiar la caché fría y regenerarla con las variables de producción en caliente:
> ```bash
> docker exec -it $(docker ps -q -f name=systemcarnet) sh -c "php artisan config:clear && php artisan optimize && php artisan queue:restart"
> ```

### 2. Contadores Web Trabados al 0%
Si la web figura como "Sincronizando" pero se queda en 0% indefinidamente sin registrar números:
1. Revisa la **Consola de Salida** o los logs en **Sistema > Auditoría**.
2. Presiona el botón rojo **"DETENER PROCESO"** en el Centro de Control para liberar cualquier bloqueo de caché o lock de base de datos huérfano (`firebase_sync_lock`).
3. Si el indicador del corta-circuitos está activado, presiona **"Restablecer Corta-circuitos"** para reanudar la comunicación con Firebase.
4. Ejecuta el comando SSH de limpieza de caché e inicio de workers detallado arriba.

---

### 🔑 Comandos Administrativos Adicionales (SSH):

* **Cambiar contraseña de Dokploy en PostgreSQL:**
  ```bash
  docker exec -it ddcd32143a5a psql -U dokploy -d dokploy -c "ALTER USER dokploy WITH PASSWORD 'zfdCDiYER21TWHwH6SZSyle7ZHibQgoD';"
  ```

* **Crear Administrador Inicial en Laravel:**
  ```bash
  docker exec -it d93b4fca3266 php artisan tinker --execute="\$u = \App\Models\User::create(['name' => 'Admin', 'email' => 'admin@safesure.com', 'password' => Hash::make('password')]); if(class_exists('\Spatie\Permission\Models\Role')) { \$u->assignRole('admin'); } echo 'Usuario creado con éxito';"
  ```
