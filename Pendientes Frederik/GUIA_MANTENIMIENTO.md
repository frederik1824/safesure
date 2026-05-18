# 🛠️ Guía de Mantenimiento y Operación - SysCarnet

Este documento detalla las acciones y comandos fundamentales para asegurar el correcto funcionamiento del sistema, especialmente los procesos de sincronización con Firebase Cloud.

---

## 🚀 1. El Motor de Segundo Plano (Queues)

La sincronización en SysCarnet (Pull y Push) se ejecuta de forma asíncrona para no bloquear la interfaz del usuario. Para que esto funcione, **debes tener un proceso de escucha de colas activo.**

### Comando Principal:
```bash
php artisan queue:work
```
- **¿Qué hace?**: Escucha y ejecuta los trabajos (Jobs) de sincronización que se envían cuando presionas botones en el "Sync Center".
- **Recomendación**: En un entorno de producción, este comando debería ser gestionado por una herramienta como **Supervisor** para que se reinicie automáticamente si falla.

---

## 🤖 2. Comandos de Sincronización Manual (Artisan)

Aunque existe el "Sync Center" en la web, puedes ejecutar estos comandos directamente desde la terminal para mantenimiento o depuración masiva.

### Descarga de Datos (Firebase -> Local):
```bash
# Modo Inteligente (Delta Sync): Solo descarga lo que ha cambiado desde la última vez.
php artisan firebase:sync-pull

# Modo Total: Descarga todo desde cero (Cuidado: consume mucha cuota).
php artisan firebase:sync-pull --full
```

### Subida de Datos (Local -> Firebase):
```bash
# Sincronización Diferencial: Sube solo los registros locales modificados.
php artisan firebase:push-all

# Forzar Subida Completa: Sobrescribe todo en la nube con los datos locales.
php artisan firebase:push-all --force
```

---

## ⚙️ 3. Configuración y Credenciales

El sistema se comunica con Firebase mediante una cuenta de servicio de Google Cloud.

- **Archivo de Credenciales**: `syscarnet-firebase-adminsdk-fbsvc-7eab5483a2.json` (Ubicado en la raíz).
- **Variable de Entorno**: Verifica en tu archivo `.env` que la ruta sea correcta:
  ```env
  FIREBASE_CREDENTIALS=syscarnet-firebase-adminsdk-fbsvc-7eab5483a2.json
  ```

> [!WARNING]
> **Seguridad**: Nunca compartas el archivo `.json` de credenciales fuera de tu entorno seguro, ya que otorga acceso administrativo total a tu base de datos de Firebase.

---

## 🧹 4. Mantenimiento del Sistema

Acciones periódicas para mantener el sistema limpio y actualizado:

| Acción | Comando | Motivo |
| :--- | :--- | :--- |
| **Limpiar Caché** | `php artisan cache:clear` | Si el indicador de progreso (barra de carga) se queda "pegado" erróneamente. |
| **Actualizar DB** | `php artisan migrate` | Si añado una nueva función que requiera cambios en las tablas de la base de datos. |
| **Verificar Colas** | `php artisan queue:failed` | Para ver si algún proceso de sincronización falló por problemas de internet. |
| **Reiniciar Colas** | `php artisan queue:flush` | Limpia todos los trabajos fallidos de la lista de espera. |

---

## 📈 5. Automatización (Opcional)

Si deseas que el sistema se sincronice solo todas las noches, puedes configurar una tarea programada (Cron Job) que ejecute:
```bash
php artisan schedule:run
```
*(Nota: Requiere configuración previa en `routes/console.php`)*.

---

> [!TIP]
> **¿Problemas con la barra de progreso congelada en 0%?**
>
> 1. **El Gotcha de la Caché en Docker (Dokploy):**
>    Cuando Dokploy compila la imagen de Docker, Laravel cachea la configuración (`php artisan optimize`) en frío, dejando las variables de Firebase como `null`.
>    * **Solución (SSH):** Ejecuta este comando en la terminal SSH de tu VPS para limpiar la caché de compilación y recargar las credenciales en caliente:
>      ```bash
>      docker exec -it \$(docker ps -q -f name=systemcarnet) sh -c "php artisan config:clear && php artisan optimize && php artisan queue:restart"
>      ```
>
> 2. **Corte-Circuitos y Bloqueos:**
>    Si la web muestra "Sincronizando" pero no avanza:
>    * Haz clic en **"DETENER PROCESO"** en el Centro de Control web para liberar el lock de sincronización (`firebase_sync_lock`).
>    * Si el corta-circuitos se abrió por sobrepasar cuotas de Firebase, presiona **"Restablecer Corta-circuitos"**.
>
> 3. **Compatibilidad PostgreSQL (`costo_entrega`):**
>    PostgreSQL tiene restricciones estrictas de `NOT NULL`. Ya hemos implementado un mutador automático en `Afiliado.php` para que cualquier campo `costo_entrega` nulo proveniente de Firestore se guarde por defecto como `0`, previniendo errores de base de datos silenciosos.
