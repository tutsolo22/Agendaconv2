# Esquemas de Base de Datos para Catálogos del SAT

Esta sección define la nomenclatura y abreviaturas utilizadas para los prefijos de las tablas:

*   **Catálogos de CFDI 4.0:** `sat_cfdi_40`
*   **Catálogos de CFDI 3.3:** `sat_cfdi`
*   **Catálogos de CFDI De Retenciones e Información de Pagos 2.0:** `sat_ret_20`
*   **Catálogos de complemento de Pagos 1.1:** `sat_pagos`
*   **Catálogos de complemento de Nóminas 1.1:** `sat_nomina`
*   **Catálogos de complemento de Comercio Exterior 1.1:** `sat_cce`
*   **Catálogos de complemento de Comercio Exterior 2.0:** `sat_cce_20`
*   **Catálogos de complemento de Carta Porte 2.0:** `sat_ccp_20`
*   **Catálogos de complemento de Carta Porte 3.0:** `sat_ccp_30`
*   **Catálogos de complemento de Carta Porte 3.1:** `sat_ccp_31`

---

A continuación se detallan los esquemas SQL para cada tabla, generados el 08/05/2025 22:21:57.

## Tabla: `sat_cce_20_claves_pedimentos`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cce_20_colonias`"
````sql
`colonia` VARCHAR(255) not null,
`codigo_postal` VARCHAR(10) not null,
`asentamiento` VARCHAR(255) not null,
PRIMARY KEY(`colonia`, `codigo_postal`)
````

---\n

## Tabla: `sat_cce_20_estados`"
````sql
`estado` VARCHAR(255) not null,
`pais` VARCHAR(100) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`estado`, `pais`)
````

## Tabla: `sat_cce_20_fracciones_arancelarias`"
````sql
`fraccion` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
`unidad` VARCHAR(255) not null,
PRIMARY KEY(`fraccion`)
````

---\n

## Tabla: `sat_cce_20_incoterms`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cce_20_localidades`"
````sql
`localidad` VARCHAR(255) not null,
`estado` VARCHAR(255) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`localidad`, `estado`)
````

---\n

## Tabla: `sat_cce_20_motivos_traslado`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cce_20_municipios`"
````sql
`municipio` VARCHAR(255) not null,
`estado` VARCHAR(255) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`municipio`, `estado`)
````

---\n

## Tabla: `sat_cce_20_tipos_operacion`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cce_20_unidades_medida`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cce_claves_pedimentos`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cce_colonias`"
````sql
`colonia` VARCHAR(255) not null,
`codigo_postal` VARCHAR(10) not null,
`asentamiento` VARCHAR(255) not null,
PRIMARY KEY(`colonia`, `codigo_postal`)
````

---\n

## Tabla: `sat_cce_estados`"
````sql
`estado` VARCHAR(255) not null,
`pais` VARCHAR(100) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`estado`, `pais`)
````

---\n

## Tabla: `sat_cce_fracciones_arancelarias`"
````sql
`fraccion` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
`unidad` VARCHAR(255) not null,
PRIMARY KEY(`fraccion`)
````

---\n

## Tabla: `sat_cce_incoterms`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cce_localidades`"
````sql
`localidad` VARCHAR(255) not null,
`estado` VARCHAR(255) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`localidad`, `estado`)
````

---\n

## Tabla: `sat_cce_motivos_traslado`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cce_municipios`"
````sql
`municipio` VARCHAR(255) not null,
`estado` VARCHAR(255) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`municipio`, `estado`)
````

---\n

## Tabla: `sat_cce_tipos_operacion`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cce_unidades_medida`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_autorizaciones_naviero`"
````sql
`id` VARCHAR(30) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_claves_unidades`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`descripcion` VARCHAR(255) not null,
`nota` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
`simbolo` VARCHAR(255) not null,
`bandera` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_codigos_transporte_aereo`"
````sql
`id` VARCHAR(30) not null,
`nacionalidad` VARCHAR(255) not null,
`texto` TEXT not null,
`designador_oaci` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_colonias`"
````sql
`colonia` VARCHAR(255) not null,
`codigo_postal` VARCHAR(10) not null,
`texto` TEXT not null,
PRIMARY KEY(`colonia`, `codigo_postal`)
````

---\n

## Tabla: `sat_ccp_20_configuraciones_autotransporte`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`numero_de_ejes` TEXT not null,
`numero_de_llantas` TEXT not null,
`remolque` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_configuraciones_maritimas`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_contenedores`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`descripcion` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_contenedores_maritimos`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_derechos_de_paso`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`entre` VARCHAR(255) not null,
`hasta` VARCHAR(255) not null,
`otorga_recibe` VARCHAR(255) not null,
`concesionario` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_estaciones`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`clave_transporte` VARCHAR(255) not null,
`nacionalidad` VARCHAR(255) not null,
`designador_iata` VARCHAR(255) not null,
`linea_ferrea` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_figuras_transporte`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_localidades`"
````sql
`localidad` VARCHAR(255) not null,
`estado` VARCHAR(255) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`localidad`, `estado`)
````

---\n

## Tabla: `sat_ccp_20_materiales_peligrosos`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`clase_o_div` VARCHAR(255) not null,
`peligro_secundario` VARCHAR(255) not null,
`nombre_tecnico` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null
````

---\n

## Tabla: `sat_ccp_20_municipios`"
````sql
`municipio` VARCHAR(255) not null,
`estado` VARCHAR(255) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`municipio`, `estado`)
````

---\n

## Tabla: `sat_ccp_20_partes_transporte`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_productos_servicios`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`similares` VARCHAR(255) not null,
`material_peligroso` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_tipos_carga`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_tipos_carro`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`contenedor` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_tipos_embalaje`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_tipos_estacion`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`claves_transportes` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_tipos_permiso`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`clave_transporte` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_tipos_remolque`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_tipos_servicio`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`contenedor` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_tipos_trafico`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_20_transportes`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_autorizaciones_naviero`"
````sql
`id` VARCHAR(30) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_claves_unidades`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`descripcion` VARCHAR(255) not null,
`nota` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
`simbolo` VARCHAR(255) not null,
`bandera` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_codigos_transporte_aereo`"
````sql
`id` VARCHAR(30) not null,
`nacionalidad` VARCHAR(255) not null,
`texto` TEXT not null,
`designador_oaci` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_colonias`"
````sql
`colonia` VARCHAR(255) not null,
`codigo_postal` VARCHAR(10) not null,
`texto` TEXT not null,
PRIMARY KEY(`colonia`, `codigo_postal`)
````

---\n

## Tabla: `sat_ccp_30_condiciones_especiales`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_configuraciones_autotransporte`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`numero_de_ejes` TEXT not null,
`numero_de_llantas` TEXT not null,
`remolque` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_configuraciones_maritimas`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_contenedores`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`descripcion` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_contenedores_maritimos`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_derechos_de_paso`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`entre` VARCHAR(255) not null,
`hasta` VARCHAR(255) not null,
`otorga_recibe` VARCHAR(255) not null,
`concesionario` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_documentos_aduaneros`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_estaciones`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`clave_transporte` VARCHAR(255) not null,
`nacionalidad` VARCHAR(255) not null,
`designador_iata` VARCHAR(255) not null,
`linea_ferrea` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_figuras_transporte`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_formas_farmaceuticas`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_localidades`"
````sql
`localidad` VARCHAR(255) not null,
`estado` VARCHAR(255) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`localidad`, `estado`)
````

---\n

## Tabla: `sat_ccp_30_materiales_peligrosos`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`clase_o_div` VARCHAR(255) not null,
`peligro_secundario` VARCHAR(255) not null,
`nombre_tecnico` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null
````

---\n

## Tabla: `sat_ccp_30_municipios`"
````sql
`municipio` VARCHAR(255) not null,
`estado` VARCHAR(255) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`municipio`, `estado`)
````

---\n

## Tabla: `sat_ccp_30_partes_transporte`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_productos_servicios`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`similares` VARCHAR(255) not null,
`material_peligroso` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_regimenes_aduaneros`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`impoexpo` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_registros_istmo`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_sectores_cofepris`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_tipos_carga`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_tipos_carro`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`contenedor` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_tipos_embalaje`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_tipos_estacion`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`claves_transportes` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_tipos_materia`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_tipos_permiso`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`clave_transporte` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_tipos_remolque`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_tipos_servicio`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`contenedor` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_tipos_trafico`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_30_transportes`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_autorizaciones_naviero`"
````sql
`id` VARCHAR(30) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_claves_unidades`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`descripcion` VARCHAR(255) not null,
`nota` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
`simbolo` VARCHAR(255) not null,
`bandera` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_codigos_transporte_aereo`"
````sql
`id` VARCHAR(30) not null,
`nacionalidad` VARCHAR(255) not null,
`texto` TEXT not null,
`designador_oaci` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_colonias`"
````sql
`colonia` VARCHAR(255) not null,
`codigo_postal` VARCHAR(10) not null,
`texto` TEXT not null,
PRIMARY KEY(`colonia`, `codigo_postal`)
````

---\n

## Tabla: `sat_ccp_31_condiciones_especiales`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_configuraciones_autotransporte`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`numero_de_ejes` TEXT not null,
`numero_de_llantas` TEXT not null,
`remolque` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_configuraciones_maritimas`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_contenedores`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`descripcion` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_contenedores_maritimos`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_derechos_de_paso`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`entre` VARCHAR(255) not null,
`hasta` VARCHAR(255) not null,
`otorga_recibe` VARCHAR(255) not null,
`concesionario` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_documentos_aduaneros`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_estaciones`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`clave_transporte` VARCHAR(255) not null,
`nacionalidad` VARCHAR(255) not null,
`designador_iata` VARCHAR(255) not null,
`linea_ferrea` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

## Tabla: `sat_ccp_31_figuras_transporte`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_formas_farmaceuticas`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_localidades`"
````sql
`localidad` VARCHAR(255) not null,
`estado` VARCHAR(255) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`localidad`, `estado`)
````

---\n

## Tabla: `sat_ccp_31_materiales_peligrosos`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`clase_o_div` VARCHAR(255) not null,
`peligro_secundario` VARCHAR(255) not null,
`nombre_tecnico` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null
````

---\n

## Tabla: `sat_ccp_31_municipios`"
````sql
`municipio` VARCHAR(255) not null,
`estado` VARCHAR(255) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`municipio`, `estado`)
````

---\n

## Tabla: `sat_ccp_31_partes_transporte`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_productos_servicios`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`similares` VARCHAR(255) not null,
`material_peligroso` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_regimenes_aduaneros`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`impoexpo` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_registros_istmo`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_sectores_cofepris`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_tipos_carga`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_tipos_carro`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`contenedor` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_tipos_embalaje`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_tipos_estacion`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`claves_transportes` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_tipos_materia`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_tipos_permiso`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`clave_transporte` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_tipos_remolque`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_tipos_servicio`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`contenedor` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_tipos_trafico`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ccp_31_transportes`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_40_aduanas`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_40_claves_unidades`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`descripcion` VARCHAR(255) not null,
`notas` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
`simbolo` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_40_codigos_postales`"
````sql
`id` VARCHAR(30) not null,
`estado` VARCHAR(255) not null,
`municipio` VARCHAR(255) not null,
`localidad` VARCHAR(255) not null,
`estimulo_frontera` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
`huso_descripcion` VARCHAR(255) not null,
`huso_verano_mes_inicio` VARCHAR(255) not null,
`huso_verano_dia_inicio` VARCHAR(255) not null,
`huso_verano_hora_inicio` VARCHAR(255) not null,
`huso_verano_diferencia` VARCHAR(255) not null,
`huso_invierno_mes_inicio` VARCHAR(255) not null,
`huso_invierno_dia_inicio` VARCHAR(255) not null,
`huso_invierno_hora_inicio` VARCHAR(255) not null,
`huso_invierno_diferencia` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_40_colonias`"
````sql
`colonia` VARCHAR(255) not null,
`codigo_postal` VARCHAR(10) not null,
`texto` TEXT not null,
PRIMARY KEY(`colonia`, `codigo_postal`)
````

---\n

## Tabla: `sat_cfdi_40_estados`"
````sql
`estado` VARCHAR(255) not null,
`pais` VARCHAR(100) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`estado`, `pais`)
````

---\n

## Tabla: `sat_cfdi_40_exportaciones`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_40_formas_pago`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`es_bancarizado` TEXT not null,
`requiere_numero_operacion` TEXT not null,
`permite_banco_ordenante_rfc` TEXT not null,
`permite_cuenta_ordenante` TEXT not null,
`patron_cuenta_ordenante` VARCHAR(255) not null,
`permite_banco_beneficiario_rfc` TEXT not null,
`permite_cuenta_beneficiario` TEXT not null,
`patron_cuenta_beneficiario` VARCHAR(255) not null,
`permite_tipo_cadena_pago` TEXT not null,
`requiere_banco_ordenante_nombre_ext` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_40_impuestos`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`retencion` TEXT not null,
`traslado` TEXT not null,
`ambito` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_40_localidades`"
````sql
`localidad` VARCHAR(255) not null,
`estado` VARCHAR(255) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`localidad`, `estado`)
````

---\n

## Tabla: `sat_cfdi_40_meses`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_40_metodos_pago`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_40_monedas`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`decimales` TEXT not null,
`porcentaje_variacion` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_40_municipios`"
````sql
`municipio` VARCHAR(255) not null,
`estado` VARCHAR(255) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`municipio`, `estado`)
````

---\n

## Tabla: `sat_cfdi_40_numeros_pedimento_aduana`"
````sql
`aduana` VARCHAR(255) not null,
`patente` VARCHAR(255) not null,
`ejercicio` TEXT not null,
`cantidad` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null
````

---\n

## Tabla: `sat_cfdi_40_objetos_impuestos`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_40_paises`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`patron_codigo_postal` VARCHAR(255) not null,
`patron_identidad_tributaria` VARCHAR(255) not null,
`validacion_identidad_tributaria` VARCHAR(255) not null,
`agrupaciones` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_40_patentes_aduanales`"
````sql
`id` VARCHAR(30) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_40_periodicidades`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_40_productos_servicios`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`iva_trasladado` TEXT not null,
`ieps_trasladado` TEXT not null,
`complemento` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
`estimulo_frontera` TEXT not null,
`similares` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_40_regimenes_fiscales`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`aplica_fisica` TEXT not null,
`aplica_moral` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_40_reglas_tasa_cuota`"
````sql
`tipo` VARCHAR(255) not null,
`minimo` VARCHAR(255) not null,
`valor` VARCHAR(255) not null,
`impuesto` VARCHAR(255) not null,
`factor` VARCHAR(255) not null,
`traslado` TEXT not null,
`retencion` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null
````

---\n

## Tabla: `sat_cfdi_40_tipos_comprobantes`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`valor_maximo` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_40_tipos_factores`"
````sql
`id` VARCHAR(30) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_40_tipos_relaciones`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_40_usos_cfdi`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`aplica_fisica` TEXT not null,
`aplica_moral` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
`regimenes_fiscales_receptores` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_aduanas`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_claves_unidades`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`descripcion` VARCHAR(255) not null,
`notas` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
`simbolo` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_codigos_postales`"
````sql
`id` VARCHAR(30) not null,
`estado` VARCHAR(255) not null,
`municipio` VARCHAR(255) not null,
`localidad` VARCHAR(255) not null,
`estimulo_frontera` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
`huso_descripcion` VARCHAR(255) not null,
`huso_verano_mes_inicio` VARCHAR(255) not null,
`huso_verano_dia_inicio` VARCHAR(255) not null,
`huso_verano_hora_inicio` VARCHAR(255) not null,
`huso_verano_diferencia` VARCHAR(255) not null,
`huso_invierno_mes_inicio` VARCHAR(255) not null,
`huso_invierno_dia_inicio` VARCHAR(255) not null,
`huso_invierno_hora_inicio` VARCHAR(255) not null,
`huso_invierno_diferencia` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_colonias`"
````sql
`colonia` VARCHAR(255) not null,
`codigo_postal` VARCHAR(10) not null,
`texto` TEXT not null,
PRIMARY KEY(`colonia`, `codigo_postal`)
````

---\n

## Tabla: `sat_cfdi_estados`"
````sql
`estado` VARCHAR(255) not null,
`pais` VARCHAR(100) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`estado`, `pais`)
````

---\n

## Tabla: `sat_cfdi_localidades`"
````sql
`localidad` VARCHAR(255) not null,
`estado` VARCHAR(255) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`localidad`, `estado`)
````

---\n

## Tabla: `sat_cfdi_municipios`"
````sql
`municipio` VARCHAR(255) not null,
`estado` VARCHAR(255) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`municipio`, `estado`)
````

---\n

## Tabla: `sat_cfdi_formas_pago`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`es_bancarizado` TEXT not null,
`requiere_numero_operacion` TEXT not null,
`permite_banco_ordenante_rfc` TEXT not null,
`permite_cuenta_ordenante` TEXT not null,
`patron_cuenta_ordenante` VARCHAR(255) not null,
`permite_banco_beneficiario_rfc` TEXT not null,
`permite_cuenta_beneficiario` TEXT not null,
`patron_cuenta_beneficiario` VARCHAR(255) not null,
`permite_tipo_cadena_pago` TEXT not null,
`requiere_banco_ordenante_nombre_ext` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_impuestos`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`retencion` TEXT not null,
`traslado` TEXT not null,
`ambito` VARCHAR(255) not null,
`entidad` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_metodos_pago`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_monedas`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`decimales` TEXT not null,
`porcentaje_variacion` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_numeros_pedimento_aduana`"
````sql
`aduana` VARCHAR(255) not null,
`patente` VARCHAR(255) not null,
`ejercicio` TEXT not null,
`cantidad` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null
````

---\n

## Tabla: `sat_cfdi_paises`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`patron_codigo_postal` VARCHAR(255) not null,
`patron_identidad_tributaria` VARCHAR(255) not null,
`validacion_identidad_tributaria` VARCHAR(255) not null,
`agrupaciones` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_patentes_aduanales`"
````sql
`id` VARCHAR(30) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_productos_servicios`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`iva_trasladado` TEXT not null,
`ieps_trasladado` TEXT not null,
`complemento` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
`estimulo_frontera` TEXT not null,
`similares` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_regimenes_fiscales`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`aplica_fisica` TEXT not null,
`aplica_moral` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_reglas_tasa_cuota`"
````sql
`tipo` VARCHAR(255) not null,
`minimo` VARCHAR(255) not null,
`valor` VARCHAR(255) not null,
`impuesto` VARCHAR(255) not null,
`factor` VARCHAR(255) not null,
`traslado` TEXT not null,
`retencion` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null
````

---\n

## Tabla: `sat_cfdi_tipos_comprobantes`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`valor_maximo` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_tipos_factores`"
````sql
`id` VARCHAR(30) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_tipos_relaciones`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_cfdi_usos_cfdi`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`aplica_fisica` TEXT not null,
`aplica_moral` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_nomina_bancos`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`razon_social` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_nomina_estados`"
````sql
`estado` VARCHAR(255) not null,
`pais` VARCHAR(100) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`estado`, `pais`)
````

---\n

## Tabla: `sat_nomina_origenes_recursos`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_nomina_periodicidades_pagos`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_nomina_riesgos_puestos`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_nomina_tipos_contratos`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_nomina_tipos_deducciones`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_nomina_tipos_horas`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_nomina_tipos_incapacidades`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_nomina_tipos_jornadas`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_nomina_tipos_nominas`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_nomina_tipos_otros_pagos`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_nomina_tipos_percepciones`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_nomina_tipos_regimenes`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_pagos_tipos_cadena_pago`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ret_20_claves_retencion`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`nombre_complemento` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ret_20_ejercicios`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ret_20_entidades_federativas`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ret_20_paises`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ret_20_periodicidades`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`nombre_complemento` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ret_20_periodos`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ret_20_tipos_contribuyentes`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ret_20_tipos_dividendos_utilidades`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ret_20_tipos_impuestos`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n

## Tabla: `sat_ret_20_tipos_pago_retencion`"
````sql
`id` VARCHAR(30) not null,
`texto` TEXT not null,
`tipo_impuesto` VARCHAR(255) not null,
`vigencia_desde` VARCHAR(255) not null,
`vigencia_hasta` VARCHAR(255) not null,
PRIMARY KEY(`id`)
````

---\n
