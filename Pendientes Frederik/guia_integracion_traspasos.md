# Guía de Integración: Consumo de Datos de Traspasos (Firebase)

Esta documentación detalla cómo la empresa receptora (**SAFESURE**) debe consumir los datos de producción de traspasos suministrados a través de Firebase Firestore.

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
| `fecha_nacimiento` | Date String| **[NUEVO]** Fecha de nacimiento del afiliado (YYYY-MM-DD). |
| `sexo` | String | **[NUEVO]** Sexo del afiliado ("M", "F", "Masculino", "Femenino"). |
| `agente` | String | Nombre resuelto del agente responsable (ej: "PEDRO RAMIREZ"). |
| `estado` | String | Estado actual del traspaso (ej: "EFECTIVO", "RECHAZADO"). |
| `cantidad_dependientes`| Integer| Número de familiares incluidos en el traspaso. |
| `fecha_solicitud` | Date String| Fecha en que se originó la solicitud (YYYY-MM-DD). |
| `fecha_efectivo` | Date String| Fecha en que entra en vigor el traspaso (si aplica). |
| `periodo` | String | Periodo de vigencia (Formato YYYY-MM). |
| `status_unipago` | String | Estatus reportado por Unipago (si aplica). |
| `motivo_rechazo` | String | **[NUEVO]** Descripción del motivo formal de rechazo (`descripcion` de la tabla de motivos) o en su defecto la observación manual de estado (`motivos_estado`). Si no está rechazado, será `null`. |
| `fecha_rechazo` | Date String | **[NUEVO]** Fecha en que se registró el rechazo en formato `YYYY-MM-DD`. Si no está rechazado, será `null`. |
| `updated_at` | Timestamp | Fecha y hora de la última actualización en la nube. |

> [!IMPORTANT]
> **Ajuste requerido para SAFESURE**: Se han incorporado los campos `motivo_rechazo`, `fecha_rechazo`, `fecha_nacimiento` y `sexo` en el payload de Firestore para automatizar la sincronización de expedientes inválidos o devueltos, y la ingesta de metadatos del afiliado desde el sistema maestro CMD. Por favor, actualicen sus listeners y scripts de sincronización para mapear y procesar estos campos.

## 3. Ejemplos de Documentos (JSON)

### Caso A: Traspaso Efectivo (Exitoso)
```json
{
  "nombre_afiliado": "JUAN PEREZ",
  "cedula_afiliado": "40212345678",
  "fecha_nacimiento": "1994-08-12",
  "sexo": "MASCULINO",
  "agente": "MARIA GONZALEZ",
  "estado": "EFECTIVO",
  "cantidad_dependientes": 2,
  "fecha_solicitud": "2026-05-10",
  "fecha_efectivo": "2026-06-01",
  "periodo": "2026-06",
  "status_unipago": "APROBADO",
  "motivo_rechazo": null,
  "fecha_rechazo": null,
  "updated_at": "2026-05-15T11:45:00Z"
}
```

### Caso B: Traspaso Rechazado
```json
{
  "nombre_afiliado": "FRANCISCO ANTONIO DIAZ",
  "cedula_afiliado": "01000148001",
  "fecha_nacimiento": "1988-03-24",
  "sexo": "MASCULINO",
  "agente": "PERLA MASSIEL DUVAL",
  "estado": "RECHAZADO",
  "cantidad_dependientes": 0,
  "fecha_solicitud": "2026-05-14",
  "fecha_efectivo": null,
  "periodo": "2026-05",
  "status_unipago": "RECHAZADO",
  "motivo_rechazo": "Firma del afiliado no coincide con cédula",
  "fecha_rechazo": "2026-05-18",
  "updated_at": "2026-05-18T16:32:00Z"
}
```

## 4. Mejores Prácticas para el Receptor
1.  **Escucha en Tiempo Real**: Se recomienda el uso de `onSnapshot` para recibir actualizaciones automáticas.
2.  **Seguridad**: No intentar realizar operaciones de escritura.
3.  **Filtrado de Rechazos**: Al detectar que un documento tiene un estado `RECHAZADO`, SAFESURE debe leer el campo `motivo_rechazo` para notificar al agente correspondiente y agilizar la corrección de la firma/documento.

---
*Documento generado por Antigravity AI.*
