# 📝 Pendientes para mañana - Sincronización Firebase

Este archivo contiene los pasos necesarios para reanudar la sincronización una vez que la cuota de Google Cloud (Firebase) se haya reiniciado.

## 🚀 Comandos SSH (Ejecutar en el servidor)

### 1. Sincronizar Empresas Verificadas (Prioridad)
Este comando corregirá los contadores del Dashboard al traer las 117 empresas con el flag `es_verificada`.
```bash
docker exec -it $(docker ps -q --filter ancestor=carnetsystem-systemcarnet-hrjkrg:latest) php artisan firebase:pull-all --verificadas
```

### 2. Sincronizar Cambios de las últimas 24 Horas
Mantiene el sistema al día con CMD consumiendo el mínimo de cuota posible.
```bash
docker exec -it $(docker ps -q --filter ancestor=carnetsystem-systemcarnet-hrjkrg:latest) php artisan firebase:pull-all --hours=24
```

### 3. Sincronización de Emergencia (Solo Reales)
Si solo quieres traer las empresas marcadas como `es_real` (101 registros).
```bash
docker exec -it $(docker ps -q --filter ancestor=carnetsystem-systemcarnet-hrjkrg:latest) php artisan firebase:pull-all --reales
```

### 4. Sincronización Total (CUIDADO)
Solo ejecutar si es absolutamente necesario, ya que consume toda la cuota diaria muy rápido.
```bash
docker exec -it $(docker ps -q --filter ancestor=carnetsystem-systemcarnet-hrjkrg:latest) php artisan firebase:pull-all --full
```

---

## 🖥️ Nuevo Módulo Web (Control Central)
He dejado instalado el módulo de **Centro de Sincronización** en la siguiente ruta:
`https://admin.discan.cloud/admin/firebase-sync`

Desde allí podrás presionar botones para disparar estos mismos procesos y ver los resultados en una tabla de historial.
