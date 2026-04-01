# Guía de Comandos SysSAFE Logistics Platform

Esta guía detalla los comandos necesarios para desplegar y mantener el sistema en cualquier entorno local o servidor.

## 🚀 Instalación Inicial (Nueva Máquina)

Si es la primera vez que clonas el proyecto, sigue este orden:

1. **Instalar Dependencias**:
   ```powershell
   composer install
   npm install
   ```
2. **Configurar Entorno**:
   - Copiar `.env.example` a `.env`
   - Configurar la base de datos en el `.env`
   - Asegurarse de tener el archivo `syscarnet-firebase-adminsdk-xxxxx.json` en la raíz.
3. **Migraciones y Datos Semilla**:
   ```powershell
   php artisan migrate:fresh --seed
   php artisan db:seed --class=RolePermissionSeeder
   ```

---

## ☁️ Sincronización con Firebase (Cloud)

El sistema utiliza Firebase Firestore como puente entre la ARS CMD y Safesure.

### 📥 Descarga de Datos (Pull)
Utiliza este comando para traer la base de afiliados y empresas de la nube a tu servidor local.

*   **Sincronización Diferencial (Recomendado)**:
    Solo descarga lo nuevo o modificado desde la última ejecución.
    ```powershell
    php artisan firebase:pull-all
    ```
*   **Sincronización Total (Fuerza Bruta)**:
    Descarga toda la base de datos de nuevo, útil si hay inconsistencias.
    ```powershell
    php artisan firebase:pull-all --full
    ```

### 📤 Subida de Datos (Push/Sync)
Utiliza este comando si realizas cambios masivos localmente y necesitas verlos reflejados en la nube.

*   **Sincronización General**:
    ```powershell
    php artisan firebase:sync-all
    ```

---

## 🛠️ Comandos de Mantenimiento

### Limpieza de Cache
Si notas comportamientos extraños en los permisos o rutas después de una actualización:
```powershell
php artisan optimize:clear
```

### Ejecutar Servidor Local
Para pruebas de escritorio:
```powershell
php artisan serve
npm run dev
```

---

## 🚨 Notas de Seguridad
- El archivo de credenciales de Firebase (`.json`) es crítico. **Nunca lo subas a un repositorio público**.
- El comando `firebase:pull-all` desactiva momentáneamente los `FOREIGN_KEY_CHECKS` para acelerar la carga masiva inicial; asegúrate de que el comando termine exitosamente para reactivarlos automáticamente.
