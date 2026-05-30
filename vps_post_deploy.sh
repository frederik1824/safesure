#!/bin/bash

# ==============================================================================
# Script de Post-Despliegue para Dokploy / VPS - SysSAFE Carnet
# ==============================================================================
# Este script automatiza la detección de la instancia activa del contenedor
# y ejecuta las migraciones de base de datos, limpieza de caché de Laravel
# y restauración de permisos Spatie.
# ==============================================================================

echo "🔍 Buscando la instancia activa del contenedor de la aplicación..."

# 1. Encontrar la última instancia desplegada filtrando por nombre
# En Dokploy las instancias suelen llevar la cadena del nombre de la app (ej. systemcarnet)
CONTAINER_ID=$(docker ps -q --filter "name=systemcarnet" | head -n 1)

if [ -z "$CONTAINER_ID" ]; then
    echo "❌ ERROR: No se encontró ningún contenedor activo con el patrón 'systemcarnet'."
    echo "Por favor, ejecuta 'docker ps' para verificar el nombre real de tu contenedor."
    exit 1
fi

echo "✅ Instancia de despliegue detectada: ID $CONTAINER_ID"
echo "--------------------------------------------------------"
echo "🚀 Iniciando comandos post-despliegue dentro del contenedor..."
echo "--------------------------------------------------------"

# 2. Ejecutar Migraciones pendientes de Base de Datos
echo "🗄️ 1/4. Ejecutando migraciones de base de datos..."
docker exec -i $CONTAINER_ID php artisan migrate --force

# 3. Limpiar y Regenerar la Caché del Core
echo "🧹 2/4. Limpiando y optimizando cachés (config, rutas, vistas)..."
docker exec -i $CONTAINER_ID php artisan config:cache
docker exec -i $CONTAINER_ID php artisan route:cache
docker exec -i $CONTAINER_ID php artisan view:cache

# 4. Reiniciar la caché de permisos (Spatie)
echo "🔑 3/4. Restableciendo caché de permisos Spatie..."
docker exec -i $CONTAINER_ID php artisan permission:cache-reset 2>/dev/null || docker exec -i $CONTAINER_ID php artisan cache:clear

# 5. Asegurar enlace simbólico de evidencias/storage
echo "🔗 4/4. Verificando enlace simbólico de almacenamiento público..."
docker exec -i $CONTAINER_ID php artisan storage:link --force

echo "--------------------------------------------------------"
echo "🎉 ¡PROCESO DE POST-DESPLIEGUE FINALIZADO CON ÉXITO!"
echo "Instancia optimizada: $CONTAINER_ID"
echo "--------------------------------------------------------"
