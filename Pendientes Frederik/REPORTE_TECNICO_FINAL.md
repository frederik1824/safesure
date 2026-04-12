# 📑 Reporte Técnico: Arquitectura de Sincronización Firebase-First
**Proyecto:** SysSAFE (CMD Ecosystem)
**Fecha:** 11 de Abril, 2026

---

## 1. Resumen del Problema
Al inicio de la sesión, el sistema presentaba fallos en la sincronización bidireccional:
- **Identificadores inconsistentes:** Se usaba el RNC como ID, lo que impedía sincronizar empresas sin RNC.
- **Agotamiento de Cuota (Error 429):** El sistema descargaba colecciones completas en cada petición, agotando rápidamente el límite gratuito de Google Cloud.
- **Desajuste de Datos:** El dashboard mostraba "0 Entidades Reales" a pesar de existir datos en Firebase.

## 2. Soluciones Implementadas

### A. Migración de ID a UUID
Se cambió el identificador de documentos en Firebase de `RNC` a `UUID`.
- **Efecto:** Compatibilidad del 100% con los registros de CMD, permitiendo empresas sin RNC y asegurando unicidad absoluta.
- **Archivos:** `EmpresaObserver.php`, `EmpresaController.php`.

### B. Motor de Sincronización Incremental (Delta Sync)
Implementamos una lógica que pide a Firebase solo los registros modificados desde la última vez.
- **Uso de Structured Queries:** Se utiliza `:runQuery` con filtros de rango.
- **Fallback de Fechas:** Debido a la estructura de Firebase, el sistema busca primero por `updated_at` y, si no existe, por `created_at`.
- **Formato ISO-8601:** Se ajustó el motor para manejar fechas en formato Zulú (`YYYY-MM-DDTHH:MM:SS.UUUUUUZ`).

### C. Resiliencia y Manejo de Errores
- **Exponential Backoff:** Si el servidor recibe un error 429 (límite de cuota), el sistema espera unos segundos y reintenta automáticamente antes de fallar.
- **Caché de Tokens:** Se optimizó el acceso a Google APIs guardando el Access Token en caché.

## 3. Centro de Sincronización (Web UI)
Se creó un nuevo módulo administrativo para evitar el uso constante de la terminal:
- **Ruta:** `/admin/firebase-sync`
- **Funciones:** Botones para disparar sincronizaciones incrementales, de empresas reales únicamente, o totales.
- **Auditoría:** Tabla de `firebase_sync_logs` para monitorear el éxito o fracaso de cada operación.

## 4. Comandos de Consola Relevantes
Para uso vía SSH:
- `php artisan firebase:pull-all --verificadas`: (Recomendado) Trae las empresas verificadas consumiendo mínima cuota.
- `php artisan firebase:pull-all --hours=24`: Sincronización rápida de lo ocurrido el último día.
- `php artisan firebase:pull-all --reales`: Sincroniza las empresas reales (es_real: true).

---

## 5. Instrucciones de Continuidad (Mañana)
**Estado de la Cuota:** Agotada por hoy.
**Acción Sugerida:**
1. Realizar Redeploy en Dokploy para cargar el nuevo módulo web.
2. Ejecutar la sincronización de verificadas desde la Web o SSH.
3. El Dashboard (Entidades Reales) se actualizará automáticamente al terminar.

---
*Reporte generado por Antigravity AI Coding Assistant.*
