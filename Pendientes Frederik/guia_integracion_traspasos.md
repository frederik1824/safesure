# Guía de Integración: Consumo de Datos de Traspasos (Firebase)

Esta documentación detalla cómo la empresa receptora debe consumir los datos de producción suministrados a través de Firebase Firestore.

## 1. Credenciales y Acceso
El acceso se realiza mediante el SDK de Firebase (Web, Admin SDK o REST API). Se requiere una llave de servicio con permisos de **Solo Lectura** (Read-Only).

*   **Proyecto**: `sys-carnet-afiliados`
*   **Colección**: `traspasos`

## 2. Estructura de Datos (Documento)
Cada documento en la colección `traspasos` representa una solicitud de traspaso procesada en el sistema maestro. El ID del documento coincide con el ID interno del registro original.

### Diccionario de Campos
| Campo | Tipo | Descripción |
| :--- | :--- | :--- |
| `nombre_afiliado` | String | Nombre completo del titular del traspaso. |
| `cedula_afiliado` | String | Cédula del titular. |
| `agente` | String | Nombre resuelto del agente responsable (ej: "PEDRO RAMIREZ"). |
| `estado` | String | Estado actual del traspaso (ej: "EFECTIVO", "RECHAZADO"). |
| `cantidad_dependientes`| Integer| Número de familiares incluidos en el traspaso. |
| `fecha_solicitud` | Date String| Fecha en que se originó la solicitud (YYYY-MM-DD). |
| `fecha_efectivo` | Date String| Fecha en que entra en vigor el traspaso (si aplica). |
| `periodo` | String | Periodo de vigencia (Formato YYYY-MM). |
| `status_unipago` | String | Estatus reportado por Unipago (si aplica). |
| `updated_at` | Timestamp | Fecha y hora de la última actualización en la nube. |

## 3. Ejemplo de Documento (JSON)
```json
{
  "nombre_afiliado": "JUAN PEREZ",
  "cedula_afiliado": "40212345678",
  "agente": "MARIA GONZALEZ",
  "estado": "EFECTIVO",
  "cantidad_dependientes": 2,
  "fecha_solicitud": "2026-05-10",
  "fecha_efectivo": "2026-06-01",
  "periodo": "2026-06",
  "status_unipago": "APROBADO",
  "updated_at": "2026-05-15T11:45:00Z"
}
```

## 4. Mejores Prácticas para el Receptor
1.  **Escucha en Tiempo Real**: Se recomienda el uso de `onSnapshot` para recibir actualizaciones automáticas.
2.  **Seguridad**: No intentar realizar operaciones de escritura.
3.  **Filtrado**: Pueden filtrar por el campo `estado` para procesar únicamente los traspasos vigentes.

---
*Documento generado por Antigravity AI.*
