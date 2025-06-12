USE inventario;

-- Primero, modificar la columna fecha_venta para quitar el valor por defecto
ALTER TABLE ventas MODIFY COLUMN fecha_venta DATETIME NULL;

-- Luego, actualizar la columna para que no tenga valor por defecto
ALTER TABLE ventas ALTER COLUMN fecha_venta DROP DEFAULT; 