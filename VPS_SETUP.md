# Configuración de Servidor (VPS) - Safesure Firebase Sync

Para que la sincronización en tiempo real funcione correctamente en tu VPS sin depender de mantener una terminal bierta, debes configurar **Supervisor**.

## 1. Instalación de Supervisor
Ejecuta estos comandos vía SSH en tu servidor:
```bash
sudo apt update
sudo apt install supervisor -y
```

## 2. Crear Configuración del Worker
Crea un archivo de configuración para Laravel:
```bash
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

Pega el siguiente contenido (ajusta la ruta `/var/www/sys_safe_carnet` a tu ruta real):
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/sys_safe_carnet/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/sys_safe_carnet/storage/logs/worker.log
stopwaitsecs=3600
```

## 3. Iniciar el Servicio
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

## 4. Comandos Útiles de Monitoreo
- **Ver estado**: `sudo supervisorctl status`
- **Reiniciar workers**: `sudo supervisorctl restart laravel-worker:*`
- **Ver logs en vivo**: `tail -f storage/logs/worker.log`

---

## Solución de Problemas (Mutex Lock)
Si el sistema dice `SISTEMA BLOQUEADO` y estás seguro de que no hay nada corriendo:
1. Ve al Dashboard de Sincronización.
2. Si un proceso aparece como "Estancado", aparecerá un botón rojo: **Liberar Bloqueo Forzado**. Presiónalo.
3. Esto limpiará el cache y permitirá iniciar una nueva sincronización.
