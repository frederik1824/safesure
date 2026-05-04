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

## ⚠️ Checklist de Emergencia
Si algo no funciona después de un despliegue:
1. Revisa la **Consola de Salida** en el Centro de Control.
2. Ejecuta **Limpiar Caché** desde el panel.
3. Si el problema persiste, revisa los logs en **Sistema > Auditoría**.