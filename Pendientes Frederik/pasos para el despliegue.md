# 🚀 Guía de Actualización Segura - SysSAFE Carnet (Dokploy + VPS)

Sigue estos pasos cada vez que realices una actualización en el sistema para garantizar que los cambios se apliquen correctamente y sin interrupciones.

---

## 1. Fase de Preparación (Local)
Antes de subir los cambios, asegúrate de estar en la carpeta correcta y compilar los recursos:

```powershell
# Ir a la carpeta del proyecto
cd C:\Users\frede.FREDERIKLOPEZ18\Desktop\Deploy\sys_safe_carnet

# Compilar assets de producción (Logo, CSS, JS)
npm run build

# Subir cambios al repositorio
git add .
git commit -m "feat: descripción de los cambios"
git push origin main
```

---

## 2. Fase de Despliegue (Panel Dokploy)
1. Entra a tu panel de Dokploy.
2. Verifica que el **Build** haya finalizado exitosamente.
3. Asegúrate de que el estado sea **"Running"**.

---

## 3. Fase de Ejecución (SSH en VPS)
Conéctate por SSH a tu servidor y ejecuta estos comandos para refrescar el sistema.

### Paso A: Identificar el contenedor NUEVO
```bash
# Este comando captura automáticamente el ID del contenedor más reciente
CONTAINER_ID=$(docker ps -q --filter ancestor=carnetsystem-systemcarnet-hrjkrg:latest | head -n 1)

# Verificar que tenemos un ID
echo "Trabajando en el contenedor: $CONTAINER_ID"
```

### Paso B: Comandos de Mantenimiento (Copiar y Pegar)
Ejecuta este bloque de comandos para aplicar los cambios técnicos:

```bash
# 1. Aplicar migraciones de base de datos
docker exec -it $CONTAINER_ID php artisan migrate --force

# 2. Refrescar caché de configuración (Crítico para variables .env)
docker exec -it $CONTAINER_ID php artisan config:cache

# 3. Refrescar caché de rutas
docker exec -it $CONTAINER_ID php artisan route:cache

# 4. Refrescar caché de vistas (Crítico para cambios de diseño/Blade)
docker exec -it $CONTAINER_ID php artisan view:cache

# 5. Reiniciar los workers de colas (Supervisor)
docker exec -it $CONTAINER_ID php artisan queue:restart
```

---

## 4. Fase de Verificación (Checklist)
Accede a `https://admin.discan.cloud` y verifica:

- [ ] **Identidad:** ¿El logo de SafeSure aparece en el login y sidebar?
- [ ] **Diseño:** ¿La barra lateral es blanca y las gráficas tienen los nuevos colores?
- [ ] **Seguridad:** Entra a `Sistema > Sync Center` y verifica que los logs de Webhook se estén registrando.
- [ ] **Rendimiento:** Prueba la búsqueda rápida con `CTRL + K`.

---

## 💡 Tips de Emergencia
*   **Si algo sale mal:** Puedes ver los logs en tiempo real con `docker logs -f $CONTAINER_ID`.
*   **Si el diseño no cambia:** Asegúrate de limpiar la caché de tu navegador o abrir en modo incógnito.
*   **Si el ID no se encuentra:** Ejecuta `docker ps` manualmente para verificar que el contenedor esté encendido.