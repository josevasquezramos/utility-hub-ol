<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        return view('chat'); // Apunta a tu vista de Blade
    }

    public function ask(Request $request)
    {
        $pregunta = $request->input('pregunta');
        $apiKey = env('GEMINI_API_KEY');
        $urlApi = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite-preview:generateContent?key={$apiKey}";

        // ==========================================
        // 1. ESPACIO PARA TU CONTEXTO DDL
        // ==========================================
        $esquemaBD = "
CREATE TABLE vai_articulos (
id INT PRIMARY KEY,
nombre_completo VARCHAR(255), -- Úsalo siempre para buscar por nombre de producto
categoria VARCHAR(255),
sub_categoria VARCHAR(255),
marca VARCHAR(255),
grupo VARCHAR(255),
presentacion VARCHAR(255),
unidad VARCHAR(255),
especificacion VARCHAR(255),
color VARCHAR(255),
medida DECIMAL(8,2),
descripcion TEXT,
costo DECIMAL(8,2),
precio DECIMAL(8,2),
stock DECIMAL(8,2),
abiertos DECIMAL(8,2),
mermas DECIMAL(8,2),
fraccionable TINYINT(1),
created_at TIMESTAMP,
updated_at TIMESTAMP,
deleted_at TIMESTAMP
);
CREATE TABLE vai_vehiculos (
id INT PRIMARY KEY,
placa VARCHAR(20),
vehiculo_completo VARCHAR(255), -- Úsalo para descripciones generales
marca VARCHAR(255),
modelo VARCHAR(255),
tipo_vehiculo VARCHAR(50),
color VARCHAR(255),
vin VARCHAR(255),
motor VARCHAR(255),
ano SMALLINT,
created_at TIMESTAMP,
updated_at TIMESTAMP,
deleted_at TIMESTAMP
);
CREATE TABLE vai_servicios (
id INT PRIMARY KEY,
servicio_completo VARCHAR(255),
servicio VARCHAR(255),
costo DECIMAL(8,2),
para_tipo_vehiculo VARCHAR(50),
created_at TIMESTAMP,
updated_at TIMESTAMP,
deleted_at TIMESTAMP
);
CREATE TABLE vai_repuestos (
id INT PRIMARY KEY,
codigo VARCHAR(255),
nombre VARCHAR(255),
categoria VARCHAR(255),
cantidad INT,
marca_modelo VARCHAR(255),
motor VARCHAR(255),
medidas_cod_oem VARCHAR(255),
estado VARCHAR(255),
notas TEXT,
fecha DATE,
tecnico_responsable VARCHAR(255),
created_at TIMESTAMP,
updated_at TIMESTAMP,
deleted_at TIMESTAMP
);

CREATE TABLE auditoria_trabajos (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
trabajo_id bigint(20) unsigned NOT NULL,
campo_afectado enum('kilometraje','hora_salida','ambos') NOT NULL,
valor_anterior_kilometraje decimal(10,2) DEFAULT NULL,
valor_anterior_hora_salida time DEFAULT NULL,
usuario_responsable varchar(255) DEFAULT NULL,
fecha_cambio datetime NOT NULL DEFAULT current_timestamp(),
ip_origen varchar(45) DEFAULT NULL,
motivo_cambio text DEFAULT NULL,
PRIMARY KEY (id),
KEY trabajo_id (trabajo_id),
KEY fecha_cambio (fecha_cambio)
);
CREATE TABLE clientes (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
identificador varchar(12) DEFAULT NULL,
nombre varchar(255) NOT NULL,
telefono varchar(255) DEFAULT NULL,
direccion varchar(255) DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
deleted_at timestamp NULL DEFAULT NULL,
nombre_completo varchar(255) GENERATED ALWAYS AS (case when identificador is null then nombre else concat(identificador,' - ',nombre) end) VIRTUAL,
PRIMARY KEY (id),
UNIQUE KEY clientes_identificador_unique (identificador)
);
CREATE TABLE comprobantes (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
codigo varchar(20) NOT NULL,
emision datetime NOT NULL,
total decimal(8,2) NOT NULL,
aplica_detraccion tinyint(1) NOT NULL DEFAULT 0,
url varchar(255) DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
UNIQUE KEY comprobantes_codigo_unique (codigo)
);
CREATE TABLE contenido_informes (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
informe varchar(255) NOT NULL,
contenido text NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id)
);
CREATE TABLE cronograma_tareas (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
nombre varchar(255) NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id)
);
CREATE TABLE herramientas (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
nombre varchar(255) NOT NULL,
costo decimal(8,2) NOT NULL DEFAULT 0.00,
stock int(10) unsigned NOT NULL DEFAULT 0,
asignadas int(10) unsigned NOT NULL DEFAULT 0,
mermas int(10) unsigned NOT NULL DEFAULT 0,
perdidas int(10) unsigned NOT NULL DEFAULT 0,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
UNIQUE KEY unique_nombre (nombre),
UNIQUE KEY herramientas_nombre_unique (nombre)
);
CREATE TABLE implementos (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
nombre varchar(255) NOT NULL,
costo decimal(8,2) NOT NULL DEFAULT 0.00,
stock int(10) unsigned NOT NULL DEFAULT 0,
asignadas int(10) unsigned NOT NULL DEFAULT 0,
mermas int(10) unsigned NOT NULL DEFAULT 0,
perdidas int(10) unsigned NOT NULL DEFAULT 0,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
UNIQUE KEY implementos_nombre_unique (nombre),
CONSTRAINT chk_implementos_nonneg CHECK (stock >= 0 and asignadas >= 0 and mermas >= 0 and perdidas >= 0)
);
CREATE TABLE notifications (
id char(36) NOT NULL,
type varchar(255) NOT NULL,
notifiable_type varchar(255) NOT NULL,
notifiable_id bigint(20) unsigned NOT NULL,
data text NOT NULL,
read_at timestamp NULL DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY notifications_notifiable_type_notifiable_id_index (notifiable_type,notifiable_id)
);
CREATE TABLE proveedores (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
nombre varchar(255) NOT NULL,
ruc varchar(20) DEFAULT NULL,
telefono varchar(20) DEFAULT NULL,
direccion varchar(255) DEFAULT NULL,
observacion text DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
UNIQUE KEY proveedores_ruc_unique (ruc)
);
CREATE TABLE talleres (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
nombre varchar(255) NOT NULL,
ubicacion varchar(255) NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
deleted_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
UNIQUE KEY talleres_nombre_unique (nombre)
);
CREATE TABLE trabajo_informe_plantillas (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
nombre varchar(255) NOT NULL,
contenido text NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id)
);
CREATE TABLE trabajo_pago_detalles (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
nombre varchar(255) NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
deleted_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
UNIQUE KEY trabajo_pago_detalles_nombre_unique (nombre)
);
CREATE TABLE ubicaciones (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
codigo varchar(10) NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
deleted_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
UNIQUE KEY ubicaciones_codigo_unique (codigo)
);
CREATE TABLE users (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
name varchar(255) NOT NULL,
email varchar(255) NOT NULL,
email_verified_at timestamp NULL DEFAULT NULL,
password varchar(255) NOT NULL,
remember_token varchar(100) DEFAULT NULL,
avatar_url varchar(255) DEFAULT NULL,
is_admin tinyint(1) NOT NULL DEFAULT 0,
dni varchar(8) DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
deleted_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
UNIQUE KEY users_email_unique (email)
);
CREATE TABLE asistencias (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
user_id bigint(20) unsigned NOT NULL,
lat decimal(10,7) NOT NULL,
lng decimal(10,7) NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY asistencias_user_id_foreign (user_id),
CONSTRAINT asistencias_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE CASCADE
);
CREATE TABLE calendar_events (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
title varchar(255) NOT NULL,
description text DEFAULT NULL,
starts_at datetime NOT NULL,
ends_at datetime NOT NULL,
is_global tinyint(1) NOT NULL DEFAULT 0,
target_roles longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(target_roles)),
notification_date datetime DEFAULT NULL,
second_notification_date datetime DEFAULT NULL,
notification_sent tinyint(1) NOT NULL DEFAULT 0,
second_notification_sent tinyint(1) NOT NULL DEFAULT 0,
user_id bigint(20) unsigned NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY calendar_events_user_id_foreign (user_id),
CONSTRAINT calendar_events_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);
CREATE TABLE cronogramas (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
user_id bigint(20) unsigned NOT NULL,
tarea_id bigint(20) unsigned NOT NULL,
fecha date NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY cronogramas_user_id_fecha_index (user_id,fecha),
KEY cronogramas_tarea_id_fecha_index (tarea_id,fecha),
CONSTRAINT cronogramas_tarea_id_foreign FOREIGN KEY (tarea_id) REFERENCES cronograma_tareas (id),
CONSTRAINT cronogramas_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);
CREATE TABLE entradas (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
guia varchar(20) NOT NULL,
fecha date NOT NULL,
hora time NOT NULL,
observacion text DEFAULT NULL,
responsable_id bigint(20) unsigned NOT NULL,
proveedor_id bigint(20) unsigned DEFAULT NULL,
evidencia_url varchar(255) DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
deleted_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
UNIQUE KEY entradas_guia_unique (guia),
KEY entradas_responsable_id_foreign (responsable_id),
KEY entradas_proveedor_id_foreign (proveedor_id),
CONSTRAINT entradas_proveedor_id_foreign FOREIGN KEY (proveedor_id) REFERENCES proveedores (id) ON UPDATE CASCADE,
CONSTRAINT entradas_responsable_id_foreign FOREIGN KEY (responsable_id) REFERENCES users (id) ON UPDATE CASCADE
);
CREATE TABLE equipos (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
codigo varchar(255) NOT NULL,
propietario_id bigint(20) unsigned DEFAULT NULL,
evidencia varchar(255) DEFAULT NULL,
observacion text DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
UNIQUE KEY uk_equipos_codigo (codigo),
UNIQUE KEY equipos_codigo_unique (codigo),
KEY equipos_propietario_id_foreign (propietario_id),
CONSTRAINT equipos_propietario_id_foreign FOREIGN KEY (propietario_id) REFERENCES users (id) ON UPDATE CASCADE
);
CREATE TABLE herramienta_entradas (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
codigo varchar(255) NOT NULL,
fecha datetime NOT NULL,
observacion text DEFAULT NULL,
responsable_id bigint(20) unsigned NOT NULL,
evidencia_url varchar(255) DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
UNIQUE KEY uk_he_codigo (codigo),
KEY herramienta_entradas_responsable_id_foreign (responsable_id),
CONSTRAINT herramienta_entradas_responsable_id_foreign FOREIGN KEY (responsable_id) REFERENCES users (id) ON UPDATE CASCADE
);
CREATE TABLE implemento_entradas (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
codigo varchar(255) NOT NULL,
fecha datetime NOT NULL,
observacion text DEFAULT NULL,
responsable_id bigint(20) unsigned NOT NULL,
evidencia_url varchar(255) DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
UNIQUE KEY uk_ie_codigo (codigo),
KEY implemento_entradas_responsable_id_foreign (responsable_id),
CONSTRAINT implemento_entradas_responsable_id_foreign FOREIGN KEY (responsable_id) REFERENCES users (id) ON UPDATE CASCADE
);
CREATE TABLE maletas (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
codigo varchar(255) NOT NULL,
propietario_id bigint(20) unsigned DEFAULT NULL,
evidencia varchar(255) DEFAULT NULL,
observacion text DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
UNIQUE KEY uk_maletas_codigo (codigo),
UNIQUE KEY maletas_codigo_unique (codigo),
KEY maletas_propietario_id_foreign (propietario_id),
CONSTRAINT maletas_propietario_id_foreign FOREIGN KEY (propietario_id) REFERENCES users (id) ON UPDATE CASCADE
);
CREATE TABLE model_has_permissions (
permission_id bigint(20) unsigned NOT NULL,
model_type varchar(255) NOT NULL,
model_id bigint(20) unsigned NOT NULL,
PRIMARY KEY (permission_id,model_id,model_type),
KEY model_has_permissions_model_id_model_type_index (model_id,model_type),
CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE
);
CREATE TABLE model_has_roles (
role_id bigint(20) unsigned NOT NULL,
model_type varchar(255) NOT NULL,
model_id bigint(20) unsigned NOT NULL,
PRIMARY KEY (role_id,model_id,model_type),
KEY model_has_roles_model_id_model_type_index (model_id,model_type),
CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE
);
CREATE TABLE ventas (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
codigo varchar(20) NOT NULL,
fecha date NOT NULL,
hora time NOT NULL,
observacion text DEFAULT NULL,
responsable_id bigint(20) unsigned NOT NULL,
cliente_id bigint(20) unsigned NOT NULL,
vehiculo_id bigint(20) unsigned DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
UNIQUE KEY ventas_codigo_unique (codigo),
KEY ventas_responsable_id_foreign (responsable_id),
KEY ventas_cliente_id_foreign (cliente_id),
KEY ventas_vehiculo_id_foreign (vehiculo_id),
CONSTRAINT ventas_cliente_id_foreign FOREIGN KEY (cliente_id) REFERENCES clientes (id) ON UPDATE CASCADE,
CONSTRAINT ventas_responsable_id_foreign FOREIGN KEY (responsable_id) REFERENCES users (id) ON UPDATE CASCADE,
CONSTRAINT ventas_vehiculo_id_foreign FOREIGN KEY (vehiculo_id) REFERENCES vai_vehiculos (id) ON UPDATE CASCADE
);
CREATE TABLE articulo_ubicaciones (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
articulo_id bigint(20) unsigned NOT NULL,
ubicacion_id bigint(20) unsigned NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY articulo_ubicaciones_articulo_id_foreign (articulo_id),
KEY articulo_ubicaciones_ubicacion_id_foreign (ubicacion_id),
CONSTRAINT articulo_ubicaciones_articulo_id_foreign FOREIGN KEY (articulo_id) REFERENCES vai_articulos (id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT articulo_ubicaciones_ubicacion_id_foreign FOREIGN KEY (ubicacion_id) REFERENCES ubicaciones (id) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE cliente_vehiculos (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
cliente_id bigint(20) unsigned NOT NULL,
vehiculo_id bigint(20) unsigned NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY cliente_vehiculos_cliente_id_foreign (cliente_id),
KEY cliente_vehiculos_vehiculo_id_foreign (vehiculo_id),
CONSTRAINT cliente_vehiculos_cliente_id_foreign FOREIGN KEY (cliente_id) REFERENCES clientes (id) ON UPDATE CASCADE,
CONSTRAINT cliente_vehiculos_vehiculo_id_foreign FOREIGN KEY (vehiculo_id) REFERENCES vai_vehiculos (id) ON UPDATE CASCADE
);
CREATE TABLE control_equipos (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
equipo_id bigint(20) unsigned NOT NULL,
fecha datetime NOT NULL,
responsable_id bigint(20) unsigned NOT NULL,
propietario_id bigint(20) unsigned DEFAULT NULL,
evidencia_url varchar(255) DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY idx_ce_equipo (equipo_id),
KEY idx_ce_responsable (responsable_id),
KEY idx_ce_propietario (propietario_id),
CONSTRAINT control_equipos_equipo_id_foreign FOREIGN KEY (equipo_id) REFERENCES equipos (id) ON UPDATE CASCADE,
CONSTRAINT control_equipos_propietario_id_foreign FOREIGN KEY (propietario_id) REFERENCES users (id) ON UPDATE CASCADE,
CONSTRAINT control_equipos_responsable_id_foreign FOREIGN KEY (responsable_id) REFERENCES users (id) ON UPDATE CASCADE
);
CREATE TABLE control_maletas (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
maleta_id bigint(20) unsigned NOT NULL,
fecha datetime NOT NULL,
responsable_id bigint(20) unsigned NOT NULL,
propietario_id bigint(20) unsigned DEFAULT NULL,
evidencia_url text DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY idx_cm_maleta (maleta_id),
KEY idx_cm_responsable (responsable_id),
KEY idx_cm_propietario (propietario_id),
CONSTRAINT control_maletas_maleta_id_foreign FOREIGN KEY (maleta_id) REFERENCES maletas (id) ON UPDATE CASCADE,
CONSTRAINT control_maletas_propietario_id_foreign FOREIGN KEY (propietario_id) REFERENCES users (id) ON UPDATE CASCADE,
CONSTRAINT control_maletas_responsable_id_foreign FOREIGN KEY (responsable_id) REFERENCES users (id) ON UPDATE CASCADE
);
CREATE TABLE cotizaciones (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
vehiculo_id bigint(20) unsigned DEFAULT NULL,
cliente_id bigint(20) unsigned DEFAULT NULL,
igv tinyint(1) NOT NULL DEFAULT 0,
observacion text DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY cotizaciones_vehiculo_id_foreign (vehiculo_id),
KEY cotizaciones_cliente_id_foreign (cliente_id),
CONSTRAINT cotizaciones_cliente_id_foreign FOREIGN KEY (cliente_id) REFERENCES clientes (id) ON UPDATE CASCADE,
CONSTRAINT cotizaciones_vehiculo_id_foreign FOREIGN KEY (vehiculo_id) REFERENCES vai_vehiculos (id) ON UPDATE CASCADE
);
CREATE TABLE entrada_articulos (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
entrada_id bigint(20) unsigned NOT NULL,
articulo_id bigint(20) unsigned NOT NULL,
costo decimal(8,2) NOT NULL,
cantidad int(10) unsigned NOT NULL DEFAULT 1,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
deleted_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY entrada_articulos_entrada_id_foreign (entrada_id),
KEY entrada_articulos_articulo_id_foreign (articulo_id),
CONSTRAINT entrada_articulos_articulo_id_foreign FOREIGN KEY (articulo_id) REFERENCES vai_articulos (id) ON UPDATE CASCADE,
CONSTRAINT entrada_articulos_entrada_id_foreign FOREIGN KEY (entrada_id) REFERENCES entradas (id) ON UPDATE CASCADE
);
CREATE TABLE entrega_equipos (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
equipo_id bigint(20) unsigned NOT NULL,
propietario_id bigint(20) unsigned DEFAULT NULL,
responsable_id bigint(20) unsigned NOT NULL,
evidencia text DEFAULT NULL,
fecha datetime NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY entrega_equipos_equipo_id_foreign (equipo_id),
KEY entrega_equipos_propietario_id_foreign (propietario_id),
KEY entrega_equipos_responsable_id_foreign (responsable_id),
CONSTRAINT entrega_equipos_equipo_id_foreign FOREIGN KEY (equipo_id) REFERENCES equipos (id),
CONSTRAINT entrega_equipos_propietario_id_foreign FOREIGN KEY (propietario_id) REFERENCES users (id),
CONSTRAINT entrega_equipos_responsable_id_foreign FOREIGN KEY (responsable_id) REFERENCES users (id)
);
CREATE TABLE entrega_maletas (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
maleta_id bigint(20) unsigned NOT NULL,
propietario_id bigint(20) unsigned DEFAULT NULL,
responsable_id bigint(20) unsigned NOT NULL,
firma_propietario longtext DEFAULT NULL,
firma_responsable longtext DEFAULT NULL,
evidencia text DEFAULT NULL,
fecha datetime NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY entrega_maletas_maleta_id_foreign (maleta_id),
KEY entrega_maletas_propietario_id_foreign (propietario_id),
KEY entrega_maletas_responsable_id_foreign (responsable_id),
CONSTRAINT entrega_maletas_maleta_id_foreign FOREIGN KEY (maleta_id) REFERENCES maletas (id) ON UPDATE CASCADE,
CONSTRAINT entrega_maletas_propietario_id_foreign FOREIGN KEY (propietario_id) REFERENCES users (id) ON UPDATE CASCADE,
CONSTRAINT entrega_maletas_responsable_id_foreign FOREIGN KEY (responsable_id) REFERENCES users (id) ON UPDATE CASCADE
);
CREATE TABLE equipo_detalles (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
equipo_id bigint(20) unsigned NOT NULL,
implemento_id bigint(20) unsigned NOT NULL,
ultimo_estado enum('OPERATIVO','MERMA','PERDIDO') DEFAULT NULL,
evidencia_url varchar(255) DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
deleted_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY idx_ed_equipo (equipo_id),
KEY idx_ed_implemento (implemento_id),
CONSTRAINT equipo_detalles_equipo_id_foreign FOREIGN KEY (equipo_id) REFERENCES equipos (id) ON UPDATE CASCADE,
CONSTRAINT equipo_detalles_implemento_id_foreign FOREIGN KEY (implemento_id) REFERENCES implementos (id) ON UPDATE CASCADE
);
CREATE TABLE herramienta_entrada_detalles (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
herramienta_entrada_id bigint(20) unsigned NOT NULL,
herramienta_id bigint(20) unsigned NOT NULL,
cantidad int(10) unsigned NOT NULL,
costo decimal(8,2) NOT NULL DEFAULT 0.00,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY idx_hed_he (herramienta_entrada_id),
KEY idx_hed_h (herramienta_id),
CONSTRAINT herramienta_entrada_detalles_herramienta_entrada_id_foreign FOREIGN KEY (herramienta_entrada_id) REFERENCES herramienta_entradas (id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT herramienta_entrada_detalles_herramienta_id_foreign FOREIGN KEY (herramienta_id) REFERENCES herramientas (id) ON UPDATE CASCADE
);
CREATE TABLE implemento_entrada_detalles (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
implemento_entrada_id bigint(20) unsigned NOT NULL,
implemento_id bigint(20) unsigned NOT NULL,
cantidad int(10) unsigned NOT NULL,
costo decimal(8,2) NOT NULL DEFAULT 0.00,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY idx_ied_ie (implemento_entrada_id),
KEY idx_ied_i (implemento_id),
CONSTRAINT implemento_entrada_detalles_implemento_entrada_id_foreign FOREIGN KEY (implemento_entrada_id) REFERENCES implemento_entradas (id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT implemento_entrada_detalles_implemento_id_foreign FOREIGN KEY (implemento_id) REFERENCES implementos (id) ON UPDATE CASCADE,
CONSTRAINT chk_ied_cantidad_gt_zero CHECK (cantidad > 0)
);
CREATE TABLE implemento_incidencias (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
fecha datetime NOT NULL,
tipo_origen enum('EQUIPO','STOCK') NOT NULL,
equipo_detalle_id bigint(20) unsigned DEFAULT NULL,
implemento_id bigint(20) unsigned NOT NULL,
cantidad int(10) unsigned NOT NULL DEFAULT 1,
propietario_id bigint(20) unsigned DEFAULT NULL,
responsable_id bigint(20) unsigned NOT NULL,
motivo enum('MERMA','PERDIDO') NOT NULL,
prev_estado enum('OPERATIVO','MERMA','PERDIDO') DEFAULT NULL,
prev_deleted_at datetime DEFAULT NULL,
observacion text DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY implemento_incidencias_equipo_detalle_id_foreign (equipo_detalle_id),
KEY implemento_incidencias_implemento_id_foreign (implemento_id),
KEY implemento_incidencias_propietario_id_foreign (propietario_id),
KEY implemento_incidencias_responsable_id_foreign (responsable_id),
CONSTRAINT implemento_incidencias_equipo_detalle_id_foreign FOREIGN KEY (equipo_detalle_id) REFERENCES equipo_detalles (id),
CONSTRAINT implemento_incidencias_implemento_id_foreign FOREIGN KEY (implemento_id) REFERENCES implementos (id),
CONSTRAINT implemento_incidencias_propietario_id_foreign FOREIGN KEY (propietario_id) REFERENCES users (id),
CONSTRAINT implemento_incidencias_responsable_id_foreign FOREIGN KEY (responsable_id) REFERENCES users (id),
CONSTRAINT chk_ii_coherencia CHECK (tipo_origen = 'EQUIPO' and cantidad = 1 or tipo_origen = 'STOCK' and cantidad > 0)
);
CREATE TABLE maleta_detalles (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
maleta_id bigint(20) unsigned NOT NULL,
herramienta_id bigint(20) unsigned NOT NULL,
ultimo_estado enum('OPERATIVO','MERMA','PERDIDO') DEFAULT NULL,
evidencia_url varchar(255) DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
deleted_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY idx_md_maleta (maleta_id),
KEY idx_md_herramienta (herramienta_id),
CONSTRAINT maleta_detalles_herramienta_id_foreign FOREIGN KEY (herramienta_id) REFERENCES herramientas (id) ON UPDATE CASCADE,
CONSTRAINT maleta_detalles_maleta_id_foreign FOREIGN KEY (maleta_id) REFERENCES maletas (id) ON UPDATE CASCADE
);
CREATE TABLE trabajos (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
control tinyint(1) NOT NULL DEFAULT 0,
codigo varchar(29) NOT NULL,
cliente_id bigint(20) unsigned DEFAULT NULL,
vehiculo_id bigint(20) unsigned NOT NULL,
conductor_id bigint(20) unsigned DEFAULT NULL,
taller_id bigint(20) unsigned NOT NULL,
kilometraje decimal(10,2) DEFAULT NULL,
descripcion_servicio text NOT NULL,
importe decimal(8,2) NOT NULL DEFAULT 0.00,
a_cuenta decimal(8,2) NOT NULL DEFAULT 0.00,
desembolso enum('A CUENTA','COBRADO','POR COBRAR','SIN COBRO') DEFAULT NULL,
presupuesto_enviado tinyint(1) NOT NULL DEFAULT 0,
disponible tinyint(1) NOT NULL DEFAULT 0,
aplica_kilometraje tinyint(1) NOT NULL DEFAULT 1,
igv tinyint(1) NOT NULL DEFAULT 0,
garantia varchar(255) DEFAULT NULL,
observaciones text DEFAULT NULL,
inventario_vehiculo_ingreso longtext DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
deleted_at timestamp NULL DEFAULT NULL,
fecha_ingreso datetime DEFAULT NULL,
fecha_salida datetime DEFAULT NULL,
fecha_entrega datetime DEFAULT NULL,
PRIMARY KEY (id),
UNIQUE KEY trabajos_codigo_unique (codigo),
KEY trabajos_vehiculo_id_foreign (vehiculo_id),
KEY trabajos_taller_id_foreign (taller_id),
KEY fk_trabajos_clientes (cliente_id),
KEY trabajos_conductor_id_foreign (conductor_id),
CONSTRAINT fk_trabajos_clientes FOREIGN KEY (cliente_id) REFERENCES clientes (id) ON UPDATE CASCADE,
CONSTRAINT trabajos_conductor_id_foreign FOREIGN KEY (conductor_id) REFERENCES clientes (id) ON UPDATE CASCADE,
CONSTRAINT trabajos_taller_id_foreign FOREIGN KEY (taller_id) REFERENCES talleres (id) ON UPDATE CASCADE,
CONSTRAINT trabajos_vehiculo_id_foreign FOREIGN KEY (vehiculo_id) REFERENCES vai_vehiculos (id) ON UPDATE CASCADE
);
CREATE TABLE venta_articulos (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
venta_id bigint(20) unsigned NOT NULL,
articulo_id bigint(20) unsigned NOT NULL,
precio decimal(8,2) NOT NULL,
cantidad int(10) unsigned NOT NULL DEFAULT 1,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY venta_articulos_venta_id_foreign (venta_id),
KEY venta_articulos_articulo_id_foreign (articulo_id),
CONSTRAINT venta_articulos_articulo_id_foreign FOREIGN KEY (articulo_id) REFERENCES vai_articulos (id) ON UPDATE CASCADE,
CONSTRAINT venta_articulos_venta_id_foreign FOREIGN KEY (venta_id) REFERENCES ventas (id) ON UPDATE CASCADE
);
CREATE TABLE control_equipo_detalles (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
control_equipo_id bigint(20) unsigned NOT NULL,
equipo_detalle_id bigint(20) unsigned NOT NULL,
implemento_id bigint(20) unsigned NOT NULL,
estado enum('OPERATIVO','MERMA','PERDIDO') NOT NULL DEFAULT 'OPERATIVO',
observacion text DEFAULT NULL,
prev_estado enum('OPERATIVO','MERMA','PERDIDO') DEFAULT NULL,
prev_deleted_at datetime DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
UNIQUE KEY uk_ced_control_detalle (control_equipo_id,equipo_detalle_id),
KEY control_equipo_detalles_equipo_detalle_id_foreign (equipo_detalle_id),
KEY control_equipo_detalles_implemento_id_foreign (implemento_id),
CONSTRAINT control_equipo_detalles_control_equipo_id_foreign FOREIGN KEY (control_equipo_id) REFERENCES control_equipos (id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT control_equipo_detalles_equipo_detalle_id_foreign FOREIGN KEY (equipo_detalle_id) REFERENCES equipo_detalles (id) ON UPDATE CASCADE,
CONSTRAINT control_equipo_detalles_implemento_id_foreign FOREIGN KEY (implemento_id) REFERENCES implementos (id) ON UPDATE CASCADE
);
CREATE TABLE control_maleta_detalles (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
control_maleta_id bigint(20) unsigned NOT NULL,
maleta_detalle_id bigint(20) unsigned NOT NULL,
herramienta_id bigint(20) unsigned NOT NULL,
estado enum('OPERATIVO','MERMA','PERDIDO') NOT NULL DEFAULT 'OPERATIVO',
observacion text DEFAULT NULL,
prev_estado enum('OPERATIVO','MERMA','PERDIDO') DEFAULT NULL,
prev_deleted_at datetime DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
UNIQUE KEY uk_cmd_control_detalle (control_maleta_id,maleta_detalle_id),
KEY idx_cmd_control (control_maleta_id),
KEY idx_cmd_md (maleta_detalle_id),
KEY idx_cmd_h (herramienta_id),
CONSTRAINT control_maleta_detalles_control_maleta_id_foreign FOREIGN KEY (control_maleta_id) REFERENCES control_maletas (id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT control_maleta_detalles_herramienta_id_foreign FOREIGN KEY (herramienta_id) REFERENCES herramientas (id) ON UPDATE CASCADE,
CONSTRAINT control_maleta_detalles_maleta_detalle_id_foreign FOREIGN KEY (maleta_detalle_id) REFERENCES maleta_detalles (id) ON UPDATE CASCADE
);
CREATE TABLE cotizacion_articulos (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
cotizacion_id bigint(20) unsigned NOT NULL,
descripcion varchar(255) NOT NULL,
cantidad int(10) unsigned NOT NULL DEFAULT 1,
precio decimal(10,2) NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY cotizacion_articulos_cotizacion_id_foreign (cotizacion_id),
CONSTRAINT cotizacion_articulos_cotizacion_id_foreign FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones (id) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE cotizacion_servicios (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
cotizacion_id bigint(20) unsigned NOT NULL,
servicio_id bigint(20) unsigned NOT NULL,
detalle text DEFAULT NULL,
cantidad int(10) unsigned NOT NULL DEFAULT 1,
precio decimal(10,2) NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY cotizacion_servicios_cotizacion_id_foreign (cotizacion_id),
KEY cotizacion_servicios_servicio_id_foreign (servicio_id),
CONSTRAINT cotizacion_servicios_cotizacion_id_foreign FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones (id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT cotizacion_servicios_servicio_id_foreign FOREIGN KEY (servicio_id) REFERENCES vai_servicios (id) ON UPDATE CASCADE
);
CREATE TABLE despachos (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
codigo varchar(20) NOT NULL,
fecha date NOT NULL,
hora time NOT NULL,
observacion text DEFAULT NULL,
trabajo_id bigint(20) unsigned DEFAULT NULL,
tecnico_id bigint(20) unsigned NOT NULL,
responsable_id bigint(20) unsigned NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
deleted_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY despachos_trabajo_id_foreign (trabajo_id),
KEY despachos_tecnico_id_foreign (tecnico_id),
KEY despachos_responsable_id_foreign (responsable_id),
CONSTRAINT despachos_responsable_id_foreign FOREIGN KEY (responsable_id) REFERENCES users (id) ON UPDATE CASCADE,
CONSTRAINT despachos_tecnico_id_foreign FOREIGN KEY (tecnico_id) REFERENCES users (id) ON UPDATE CASCADE,
CONSTRAINT despachos_trabajo_id_foreign FOREIGN KEY (trabajo_id) REFERENCES trabajos (id) ON UPDATE CASCADE
);
CREATE TABLE entrega_equipo_detalles (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
entrega_equipo_id bigint(20) unsigned NOT NULL,
implemento_id bigint(20) unsigned NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY entrega_equipo_detalles_entrega_equipo_id_foreign (entrega_equipo_id),
KEY entrega_equipo_detalles_implemento_id_foreign (implemento_id),
CONSTRAINT entrega_equipo_detalles_entrega_equipo_id_foreign FOREIGN KEY (entrega_equipo_id) REFERENCES entrega_equipos (id) ON DELETE CASCADE,
CONSTRAINT entrega_equipo_detalles_implemento_id_foreign FOREIGN KEY (implemento_id) REFERENCES implementos (id)
);
CREATE TABLE entrega_maleta_detalles (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
entrega_maleta_id bigint(20) unsigned NOT NULL,
herramienta_id bigint(20) unsigned NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY entrega_maleta_detalles_entrega_maleta_id_foreign (entrega_maleta_id),
KEY entrega_maleta_detalles_herramienta_id_foreign (herramienta_id),
CONSTRAINT entrega_maleta_detalles_entrega_maleta_id_foreign FOREIGN KEY (entrega_maleta_id) REFERENCES entrega_maletas (id) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT entrega_maleta_detalles_herramienta_id_foreign FOREIGN KEY (herramienta_id) REFERENCES herramientas (id) ON UPDATE CASCADE
);
CREATE TABLE evidencias (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
trabajo_id bigint(20) unsigned NOT NULL,
user_id bigint(20) unsigned NOT NULL,
evidencia_url varchar(255) NOT NULL,
tipo enum('imagen','video') NOT NULL DEFAULT 'video',
observacion text DEFAULT NULL,
sort int(10) unsigned NOT NULL DEFAULT 0,
mostrar tinyint(1) DEFAULT 1,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY evidencias_trabajo_id_foreign (trabajo_id),
KEY evidencias_user_id_foreign (user_id),
CONSTRAINT evidencias_trabajo_id_foreign FOREIGN KEY (trabajo_id) REFERENCES trabajos (id) ON UPDATE CASCADE,
CONSTRAINT evidencias_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE CASCADE
);
CREATE TABLE herramienta_incidencias (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
fecha datetime NOT NULL,
tipo_origen enum('MALETA','STOCK') NOT NULL,
maleta_detalle_id bigint(20) unsigned DEFAULT NULL,
herramienta_id bigint(20) unsigned NOT NULL,
cantidad int(10) unsigned NOT NULL DEFAULT 1,
propietario_id bigint(20) unsigned DEFAULT NULL,
responsable_id bigint(20) unsigned NOT NULL,
motivo enum('MERMA','PERDIDO') NOT NULL,
prev_estado enum('OPERATIVO','MERMA','PERDIDO') DEFAULT NULL,
prev_deleted_at datetime DEFAULT NULL,
observacion text DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY idx_hi_tipo (tipo_origen),
KEY idx_hi_md (maleta_detalle_id),
KEY idx_hi_h (herramienta_id),
KEY idx_hi_prop (propietario_id),
KEY idx_hi_resp (responsable_id),
CONSTRAINT herramienta_incidencias_herramienta_id_foreign FOREIGN KEY (herramienta_id) REFERENCES herramientas (id) ON UPDATE CASCADE,
CONSTRAINT herramienta_incidencias_maleta_detalle_id_foreign FOREIGN KEY (maleta_detalle_id) REFERENCES maleta_detalles (id) ON UPDATE CASCADE,
CONSTRAINT herramienta_incidencias_propietario_id_foreign FOREIGN KEY (propietario_id) REFERENCES users (id) ON UPDATE CASCADE,
CONSTRAINT herramienta_incidencias_responsable_id_foreign FOREIGN KEY (responsable_id) REFERENCES users (id) ON UPDATE CASCADE
);
CREATE TABLE trabajo_archivos (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
archivo_url varchar(255) NOT NULL,
trabajo_id bigint(20) unsigned NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY trabajo_archivos_trabajo_id_foreign (trabajo_id),
CONSTRAINT trabajo_archivos_trabajo_id_foreign FOREIGN KEY (trabajo_id) REFERENCES trabajos (id) ON UPDATE CASCADE
);
CREATE TABLE trabajo_articulos (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
despacho_id bigint(20) unsigned DEFAULT NULL,
fecha date NOT NULL,
hora time NOT NULL,
trabajo_id bigint(20) unsigned DEFAULT NULL,
articulo_id bigint(20) unsigned NOT NULL,
precio decimal(8,2) unsigned NOT NULL,
cantidad decimal(8,2) unsigned NOT NULL DEFAULT 1.00,
tecnico_id bigint(20) unsigned NOT NULL,
responsable_id bigint(20) unsigned NOT NULL,
movimiento enum('consumo_completo','abrir_nuevo','terminar_abierto','consumo_parcial') NOT NULL DEFAULT 'consumo_completo',
observacion text DEFAULT NULL,
confirmado tinyint(1) NOT NULL DEFAULT 0,
presupuesto tinyint(1) NOT NULL DEFAULT 1,
sort bigint(20) NOT NULL DEFAULT 0,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
orden_combinado int(11) DEFAULT 0,
PRIMARY KEY (id),
KEY trabajo_articulos_despacho_id_foreign (despacho_id),
KEY trabajo_articulos_trabajo_id_foreign (trabajo_id),
KEY trabajo_articulos_articulo_id_foreign (articulo_id),
KEY trabajo_articulos_tecnico_id_foreign (tecnico_id),
KEY trabajo_articulos_responsable_id_foreign (responsable_id),
CONSTRAINT trabajo_articulos_articulo_id_foreign FOREIGN KEY (articulo_id) REFERENCES vai_articulos (id) ON UPDATE CASCADE,
CONSTRAINT trabajo_articulos_despacho_id_foreign FOREIGN KEY (despacho_id) REFERENCES despachos (id) ON UPDATE CASCADE,
CONSTRAINT trabajo_articulos_responsable_id_foreign FOREIGN KEY (responsable_id) REFERENCES users (id) ON UPDATE CASCADE,
CONSTRAINT trabajo_articulos_tecnico_id_foreign FOREIGN KEY (tecnico_id) REFERENCES users (id) ON UPDATE CASCADE,
CONSTRAINT trabajo_articulos_trabajo_id_foreign FOREIGN KEY (trabajo_id) REFERENCES trabajos (id) ON UPDATE CASCADE
);
CREATE TABLE trabajo_comprobantes (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
trabajo_id bigint(20) unsigned NOT NULL,
comprobante_id bigint(20) unsigned NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY trabajo_comprobantes_trabajo_id_foreign (trabajo_id),
KEY trabajo_comprobantes_comprobante_id_foreign (comprobante_id),
CONSTRAINT trabajo_comprobantes_comprobante_id_foreign FOREIGN KEY (comprobante_id) REFERENCES comprobantes (id) ON UPDATE CASCADE,
CONSTRAINT trabajo_comprobantes_trabajo_id_foreign FOREIGN KEY (trabajo_id) REFERENCES trabajos (id) ON UPDATE CASCADE
);
CREATE TABLE trabajo_descripcion_tecnicos (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
trabajo_id bigint(20) unsigned NOT NULL,
user_id bigint(20) unsigned NOT NULL,
descripcion text NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY trabajo_descripcion_tecnicos_trabajo_id_foreign (trabajo_id),
KEY trabajo_descripcion_tecnicos_user_id_foreign (user_id),
CONSTRAINT trabajo_descripcion_tecnicos_trabajo_id_foreign FOREIGN KEY (trabajo_id) REFERENCES trabajos (id) ON UPDATE CASCADE,
CONSTRAINT trabajo_descripcion_tecnicos_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE CASCADE
);
CREATE TABLE trabajo_descuentos (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
trabajo_id bigint(20) unsigned DEFAULT NULL,
detalle text NOT NULL,
descuento decimal(8,2) NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY trabajo_descuentos_trabajo_id_foreign (trabajo_id),
CONSTRAINT trabajo_descuentos_trabajo_id_foreign FOREIGN KEY (trabajo_id) REFERENCES trabajos (id) ON UPDATE CASCADE
);
CREATE TABLE trabajo_detalles (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
trabajo_id bigint(20) unsigned DEFAULT NULL,
descripcion text NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY trabajo_detalles_trabajo_id_foreign (trabajo_id),
CONSTRAINT trabajo_detalles_trabajo_id_foreign FOREIGN KEY (trabajo_id) REFERENCES trabajos (id) ON UPDATE CASCADE
);
CREATE TABLE trabajo_informes (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
trabajo_id bigint(20) unsigned DEFAULT NULL,
contenido text NOT NULL,
visible tinyint(1) NOT NULL DEFAULT 1,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY trabajo_informes_trabajo_id_foreign (trabajo_id),
CONSTRAINT trabajo_informes_trabajo_id_foreign FOREIGN KEY (trabajo_id) REFERENCES trabajos (id) ON UPDATE CASCADE
);
CREATE TABLE trabajo_otros (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
trabajo_id bigint(20) unsigned DEFAULT NULL,
user_id bigint(20) unsigned DEFAULT NULL,
creador_id bigint(20) unsigned DEFAULT NULL,
descripcion text NOT NULL,
precio decimal(8,2) unsigned NOT NULL,
cantidad int(10) unsigned NOT NULL DEFAULT 1,
sort bigint(20) NOT NULL DEFAULT 0,
confirmado tinyint(1) NOT NULL DEFAULT 0,
presupuesto tinyint(1) NOT NULL DEFAULT 1,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
orden_combinado int(11) DEFAULT 0,
PRIMARY KEY (id),
KEY trabajo_otros_trabajo_id_foreign (trabajo_id),
KEY trabajo_otros_user_id_foreign (user_id),
KEY trabajo_otros_creador_id_foreign (creador_id),
CONSTRAINT trabajo_otros_creador_id_foreign FOREIGN KEY (creador_id) REFERENCES users (id) ON DELETE CASCADE,
CONSTRAINT trabajo_otros_trabajo_id_foreign FOREIGN KEY (trabajo_id) REFERENCES trabajos (id) ON UPDATE CASCADE,
CONSTRAINT trabajo_otros_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE
);
CREATE TABLE trabajo_pagos (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
trabajo_id bigint(20) unsigned NOT NULL,
monto decimal(8,2) NOT NULL,
fecha_pago date NOT NULL,
detalle_id bigint(20) unsigned NOT NULL,
observacion varchar(255) DEFAULT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY trabajo_pagos_trabajo_id_foreign (trabajo_id),
KEY trabajo_pagos_detalle_id_foreign (detalle_id),
CONSTRAINT trabajo_pagos_detalle_id_foreign FOREIGN KEY (detalle_id) REFERENCES trabajo_pago_detalles (id) ON UPDATE CASCADE,
CONSTRAINT trabajo_pagos_trabajo_id_foreign FOREIGN KEY (trabajo_id) REFERENCES trabajos (id) ON UPDATE CASCADE
);
CREATE TABLE trabajo_servicios (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
trabajo_id bigint(20) unsigned NOT NULL,
servicio_id bigint(20) unsigned NOT NULL,
detalle text DEFAULT NULL,
precio decimal(8,2) NOT NULL,
cantidad int(10) unsigned NOT NULL DEFAULT 1,
sort bigint(20) NOT NULL DEFAULT 0,
presupuesto tinyint(1) NOT NULL DEFAULT 1,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY trabajo_servicios_trabajo_id_foreign (trabajo_id),
KEY trabajo_servicios_servicio_id_foreign (servicio_id),
CONSTRAINT trabajo_servicios_servicio_id_foreign FOREIGN KEY (servicio_id) REFERENCES vai_servicios (id) ON UPDATE CASCADE,
CONSTRAINT trabajo_servicios_trabajo_id_foreign FOREIGN KEY (trabajo_id) REFERENCES trabajos (id) ON UPDATE CASCADE
);
CREATE TABLE trabajo_tecnicos (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
tecnico_id bigint(20) unsigned NOT NULL,
trabajo_id bigint(20) unsigned NOT NULL,
finalizado tinyint(1) NOT NULL DEFAULT 0,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
KEY trabajo_tecnicos_tecnico_id_foreign (tecnico_id),
KEY trabajo_tecnicos_trabajo_id_foreign (trabajo_id),
CONSTRAINT trabajo_tecnicos_tecnico_id_foreign FOREIGN KEY (tecnico_id) REFERENCES users (id) ON UPDATE CASCADE,
CONSTRAINT trabajo_tecnicos_trabajo_id_foreign FOREIGN KEY (trabajo_id) REFERENCES trabajos (id) ON UPDATE CASCADE
);
        ";

        // ==========================================
        // PASO 1: DE TEXTO A SQL STRICTO
        // ==========================================
        $instruccionesSQL = "Eres un analista de datos experto y traductor estricto de lenguaje natural a SQL para MariaDB.
REGLAS INQUEBRANTABLES DE FORMATO:
1. Devuelve ÚNICAMENTE la consulta SQL cruda basada en el esquema proporcionado.
2. NADA de saludos, explicaciones, ni formato markdown (```sql). Solo el código.
3. Si la consulta puede devolver muchos resultados, agrega obligatoriamente LIMIT 50.

REGLAS TÉCNICAS (BÚSQUEDAS Y CRUCES):
1. BÚSQUEDAS DE TEXTO: Nunca uses `=` para buscar placas, nombres de clientes, repuestos o vehículos. Usa SIEMPRE `LOWER(columna) LIKE '%termino%'`.
2. PLACAS: Los usuarios pueden escribir la placa con o sin guiones. Ignora el formato exacto usando `REPLACE(placa, '-', '') LIKE '%termino%'`.

REGLAS DE NEGOCIO (DICCIONARIO DE CONTEXTO):
1. TIEMPO EN TALLER: Si preguntan cuánto tiempo estuvo un vehículo en el taller, calcula la diferencia de horas o días entre `trabajos.fecha_ingreso` y `trabajos.fecha_salida` usando `TIMESTAMPDIFF(HOUR, fecha_ingreso, fecha_salida)`. Si `fecha_salida` es NULL, significa que el vehículo 'sigue en el taller'.
2. EVIDENCIAS: Si preguntan quién subió más evidencias o fotos, cuenta los registros en la tabla `evidencias` agrupados por `evidencias.user_id` (que cruza con `users.name`).
3. INGRESOS / VENTAS / DINERO: Si preguntan cuánto se ganó o cobró en trabajos, suma la columna `trabajos.importe`. Si preguntan por ventas directas, suma `venta_articulos.precio * venta_articulos.cantidad`.
4. REPUESTOS MÁS USADOS: Si preguntan qué se le instaló más a un carro, haz JOIN entre `trabajo_articulos`, `vai_articulos` (para el nombre) y `trabajos` (para cruzar con el vehículo). 
5. TÉCNICOS/MECÁNICOS: Cuando hablen de 'técnicos', 'empleados' o 'quién hizo el trabajo', se refieren a la tabla `users` (columna `name`). 
6. TRABAJOS PENDIENTES: Un trabajo o vehículo está 'pendiente', 'en curso' o 'en taller' si su `fecha_salida` es NULL o si la columna `finalizado` en `trabajo_tecnicos` es 0.
7. REGISTROS ELIMINADOS (SOFT DELETES): Es OBLIGATORIO filtrar los registros eliminados. Todas tus consultas deben incluir estrictamente `WHERE deleted_at IS NULL` en la tabla principal (y en los JOINs si aplica), a menos que el usuario pida explícitamente ver registros borrados o en papelera.

ESQUEMA DE BASE DE DATOS: \n" . $esquemaBD;

        $responseSQL = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post($urlApi, [
                'systemInstruction' => ['parts' => [['text' => $instruccionesSQL]]],
                'contents' => [['parts' => [['text' => $pregunta]]]],
                'generationConfig' => ['temperature' => 0.0] // 0 creatividad, 100% precisión
            ]);

        if (!$responseSQL->successful()) {
            return response()->json([
                'error' => 'Error real de Google: ' . $responseSQL->body()
            ]);
        }

        // Limpiamos la respuesta por si mandó comillas invertidas
        $sqlGenerado = trim($responseSQL->json('candidates.0.content.parts.0.text'));
        $sqlGenerado = str_replace(['```sql', '```'], '', $sqlGenerado);

        // ==========================================
        // PASO 2: EJECUTAR EN LA BASE DE DATOS
        // ==========================================
        try {
            // CAMBIA 'nombre_conexion_app2' POR LA CONEXIÓN DE TU OTRA APP EN database.php
            $resultadosBD = DB::connection('mariadb_app2')->select($sqlGenerado);

            // Si la consulta no devuelve nada
            if (empty($resultadosBD)) {
                return response()->json(['respuesta' => 'La consulta se ejecutó correctamente, pero no se encontraron resultados en la base de datos para tu pregunta.']);
            }
        } catch (\Exception $e) {
            // Si el SQL tiene error, no seguimos al paso 3
            return response()->json(['error' => 'La IA generó una consulta inválida o hay un error en la BD. Por favor, reformula tu pregunta.']);
        }

        // ==========================================
        // PASO 3: DE RESULTADOS A MARKDOWN
        // ==========================================
        // Convertimos los resultados a JSON para que la IA los lea
        $datosCrudos = json_encode($resultadosBD);

        $instruccionesMarkdown = "Eres un analista de datos amigable. 
        El usuario hizo esta pregunta: '{$pregunta}'.
        La base de datos devolvió estos datos crudos en formato JSON: {$datosCrudos}.
        TU TAREA:
        1. Responde a la pregunta del usuario utilizando exclusivamente los datos proporcionados.
        2. Formatea la respuesta usando Markdown (usa tablas de markdown si hay varios registros, o listas si es apropiado).
        3. No menciones que recibiste un JSON ni expliques el SQL. Sé directo y natural.";

        $responseMarkdown = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post($urlApi, [
                'contents' => [['parts' => [['text' => $instruccionesMarkdown]]]],
                'generationConfig' => ['temperature' => 0.3] // Un poco de temperatura para que redacte natural
            ]);

        if (!$responseMarkdown->successful()) {
            return response()->json(['error' => 'Error al contactar a la IA para formatear los resultados (Paso 3).']);
        }

        $respuestaFinal = $responseMarkdown->json('candidates.0.content.parts.0.text');

        // Devolvemos la respuesta final a Javascript
        return response()->json(['respuesta' => $respuestaFinal, 'sql_debug' => $sqlGenerado]);
    }
}
