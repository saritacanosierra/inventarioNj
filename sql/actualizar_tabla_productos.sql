-- Agregar la columna id_categoria a la tabla productos
ALTER TABLE productos
ADD COLUMN id_categoria INT;

-- Agregar la llave foránea
ALTER TABLE productos
ADD CONSTRAINT fk_producto_categoria
FOREIGN KEY (id_categoria) REFERENCES categorias(id)
ON DELETE SET NULL
ON UPDATE CASCADE; 