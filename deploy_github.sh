#!/bin/bash

# Script de Despliegue a GitHub - Safesure System
# Este script prepara el repositorio y lo sube a https://github.com/frederik1824/safesure

echo "🚀 Iniciando preparación de despliegue..."

# 1. Asegurar que .gitignore esté al día
echo "📝 Verificando archivos sensibles..."
if grep -q ".env" .gitignore && grep -q "*.json" .gitignore; then
    echo "✅ .gitignore configurado correctamente."
else
    echo "⚠️ Advertencia: Revisa tu .gitignore para evitar subir credenciales."
fi

# 2. Limpiar cachés locales antes de subir
echo "🧹 Limpiando archivos temporales..."
php artisan config:clear
php artisan cache:clear

# 3. Git Workflow
echo "📦 Empaquetando cambios..."
git init
git remote add origin https://github.com/frederik1824/safesure.git 2>/dev/null || git remote set-url origin https://github.com/frederik1824/safesure.git

git add .
git commit -m "🚀 DEPLOY: Finalización de protocolo CMD, Geocodificación Automática e Inmutabilidad de Registros"

echo "⬆️ Subiendo a GitHub (Main)..."
git branch -M main
git push -u origin main

echo "✅ ¡Listo! Proyecto disponible en: https://github.com/frederik1824/safesure"
echo "👉 Sigue la Guía de Despliegue en VPS para configurar el servidor de producción."
