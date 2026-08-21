# 📋 Guía de Integración: API REST SAFESURE

Esta guía detalla las especificaciones técnicas del API REST seguro implementado en **SysCarnet (Laravel 11)** para que **SAFESURE** pueda consultar y actualizar el estado de los carnets de los afiliados de forma bidireccional.

---

## 🔐 1. Seguridad y Autenticación

Todas las solicitudes HTTP enviadas al API deben incluir el token de seguridad. SAFESURE puede elegir cualquiera de los dos métodos de autenticación soportados:

### Método A: Cabecera personalizada `X-API-Key`
Se debe incluir el token de seguridad en la cabecera `X-API-Key`:
```http
X-API-Key: safesure_api_secret_token_2026_xyz
```

### Método B: Cabecera estándar `Authorization` (Bearer Token)
Se debe incluir el token de seguridad utilizando el esquema Bearer:
```http
Authorization: Bearer safesure_api_secret_token_2026_xyz
```

*Cualquier solicitud que no incluya un token válido recibirá una respuesta HTTP con código de estado `401 Unauthorized`.*

---

## 🚦 2. Especificación de Endpoints

La URL base para todas las llamadas en producción es:
`https://discan.cloud/api/v1/safesure`

### 2.1 Obtener Catálogo de Estados
Retorna el catálogo de estados disponibles en el sistema. Es útil para mapear los IDs correspondientes.

*   **Método:** `GET`
*   **Ruta:** `/estados`
*   **Respuesta Exitosa (200 OK):**
    ```json
    {
      "success": true,
      "data": [
        { "id": 1, "nombre": "Pendiente", "es_final": 0 },
        { "id": 3, "nombre": "En ruta", "es_final": 0 },
        { "id": 6, "nombre": "Carnet entregado", "es_final": 0 },
        { "id": 9, "nombre": "Completado", "es_final": 1 },
        { "id": 10, "nombre": "Acuse recibido", "es_final": 0 }
      ]
    }
    ```

---

### 2.2 Listar Afiliados Asignados
Retorna el listado paginado de los afiliados que están bajo la responsabilidad de SAFESURE.

*   **Método:** `GET`
*   **Ruta:** `/afiliados`
*   **Parámetros de Búsqueda (Opcionales):**
    *   `estado_id` (int): Filtrar por el ID del estado.
    *   `cedula` (string): Buscar un afiliado específico por su número de cédula.
    *   `updated_after` (string): Sincronización Incremental. Retorna los registros modificados a partir de esta fecha (Formatos: ISO 8601 `2026-08-20T12:00:00Z` o `2026-08-20 12:00:00`).
    *   `per_page` (int): Cantidad de registros por página (Mínimo: 1, Maximo: 250, Por defecto: 50).
*   **Respuesta Exitosa (200 OK):**
    ```json
    {
      "success": true,
      "data": [
        {
          "uuid": "22d9c880-1592-4364-854c-a3ae9331ca74",
          "nombre_completo": "Frederik Lopez Al",
          "cedula": "079-0017790-7",
          "sexo": "F",
          "telefono": "809-555-0199",
          "direccion": "Calle Principal #12",
          "provincia": "SANTIAGO",
          "municipio": "Santiago de los Caballeros",
          "empresa": "ARS COLEGIO MEDICO DOMINICANO",
          "rnc_empresa": "401501015",
          "poliza": "00026-40749-00",
          "contrato": "472571",
          "estado": {
            "id": 3,
            "nombre": "En ruta",
            "es_final": false
          },
          "fecha_entrega_proveedor": "2026-06-03T08:33:18Z",
          "fecha_entrega_safesure": null,
          "updated_at": "2026-06-03T08:33:18Z",
          "evidencias": []
        }
      ],
      "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 50,
        "total": 234
      }
    }
    ```

---

### 2.3 Obtener Detalle de un Afiliado
Retorna los datos detallados de un afiliado en particular. La cédula se puede enviar limpia o formateada.

*   **Método:** `GET`
*   **Ruta:** `/afiliados/{cedula}`
*   **Respuesta Exitosa (200 OK):**
    *(Mismo formato de objeto de datos que en el listado, pero sin la envoltura de paginación `meta`)*.
*   **Respuesta de Error (404 Not Found):**
    ```json
    {
      "success": false,
      "error": "Afiliado no encontrado o no asignado a SAFESURE."
    }
    ```

---

### 2.4 Actualizar Estado Logístico
Permite a SAFESURE reportar un cambio de estado sobre el carnet del afiliado.

*   **Método:** `POST`
*   **Ruta:** `/afiliados/{cedula}/estado`
*   **Cabecera Obligatoria:** `Content-Type: application/json`
*   **Cuerpo de la Solicitud (JSON):**
    *   `estado_id` (obligatorio, int): ID del nuevo estado (ej: `6` para "Carnet entregado", `4` para "No localizado").
    *   `observacion` (opcional, string): Comentario descriptivo del cambio.
    *   `fecha_entrega` (opcional, string): Fecha/hora exacta de la entrega física. Si se omite, se registra la hora actual.
*   **Respuesta Exitosa (200 OK):**
    ```json
    {
      "success": true,
      "message": "Estado del afiliado actualizado correctamente.",
      "data": {
        "uuid": "22d9c880-1592-4364-854c-a3ae9331ca74",
        "nombre_completo": "Frederik Lopez Al",
        "cedula": "079-0017790-7",
        "estado": {
          "id": 6,
          "nombre": "Carnet entregado",
          "es_final": false
        },
        "fecha_entrega_safesure": "2026-08-20T16:10:00Z"
      }
    }
    ```

---

### 2.5 Cargar Evidencias de Entrega
Permite a SAFESURE subir el archivo digitalizado (imagen o PDF) del acuse de recibo firmado o formulario de entrega.

> 💡 **Regla de Negocio Crítica:**
> Si un afiliado tiene cargados y validados tanto el `acuse_recibo` como el `formulario_firmado`, el motor del sistema transicionará el estado del afiliado automáticamente a **Completado (ID: 9)**.

*   **Método:** `POST`
*   **Ruta:** `/afiliados/{cedula}/evidencia`
*   **Cabecera Obligatoria:** `Content-Type: multipart/form-data`
*   **Cuerpo de la Solicitud (Multipart Form):**
    *   `file` (obligatorio, file): Archivo de imagen (`jpeg`, `jpg`, `png`) o documento `pdf` (máximo 10MB).
    *   `tipo_documento` (obligatorio, string): Debe ser `acuse_recibo` o `formulario_firmado`.
    *   `observaciones` (opcional, string): Nota aclaratoria.
*   **Respuesta Exitosa (200 OK):**
    ```json
    {
      "success": true,
      "message": "Evidencia digital subida y registrada exitosamente.",
      "evidencia": {
        "id": 1557,
        "tipo_documento": "acuse_recibo",
        "status": "recibido",
        "file_path": "https://discan.cloud/storage/evidencias/079-0017790-7/lJn3IlQ2bXCK.jpg",
        "observaciones": "Acuse firmado por titular"
      },
      "data": { ... }
    }
    ```

---

## 💻 3. Ejemplos Rápidos de Consumo (`curl`)

### Listar afiliados en ruta
```bash
curl -X GET "https://discan.cloud/api/v1/safesure/afiliados?estado_id=3" \
     -H "X-API-Key: safesure_api_secret_token_2026_xyz"
```

### Reportar carnet no localizado
```bash
curl -X POST "https://discan.cloud/api/v1/safesure/afiliados/079-0017790-7/estado" \
     -H "X-API-Key: safesure_api_secret_token_2026_xyz" \
     -H "Content-Type: application/json" \
     -d '{
           "estado_id": 4,
           "observacion": "Dirección no coincide, teléfono fuera de servicio"
         }'
```

### Subir foto de acuse recibido
```bash
curl -X POST "https://discan.cloud/api/v1/safesure/afiliados/079-0017790-7/evidencia" \
     -H "X-API-Key: safesure_api_secret_token_2026_xyz" \
     -F "file=@/documentos/acuse_firmado.jpg" \
     -F "tipo_documento=acuse_recibo" \
     -F "observaciones=Acuse de recibido en campo"
```
