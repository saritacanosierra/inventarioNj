.tabla-clientes {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #E1B8E2;
    border-radius: 4px;
}

.tabla-clientes table {
    width: 100%;
    border-collapse: collapse;
}

.tabla-clientes th {
    position: sticky;
    top: 0;
    background-color: #E1B8E2;
    color: white;
    padding: 12px;
    text-align: left;
    z-index: 1;
}

.tabla-clientes td {
    padding: 12px;
    border-bottom: 1px solid #E1B8E2;
}

.tabla-clientes tr:last-child td {
    border-bottom: none;
}

.tabla-clientes tr:hover {
    background-color: #f8f0f8;
}

/* Estilo para el scrollbar */
.tabla-clientes::-webkit-scrollbar {
    width: 8px;
}

.tabla-clientes::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.tabla-clientes::-webkit-scrollbar-thumb {
    background: #E1B8E2;
    border-radius: 4px;
}

.tabla-clientes::-webkit-scrollbar-thumb:hover {
    background: #d4a7d5;
}

.tabla-container {
    margin-top: 20px;
    height: 400px;
    overflow-y: auto;
    border: 1px solid #E1B8E2;
    border-radius: 4px;
    background: white;
}

.tabla-container table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.tabla-container th {
    position: sticky;
    top: 0;
    background-color: #E1B8E2;
    color: white;
    padding: 12px;
    text-align: left;
    z-index: 1;
    border-bottom: 2px solid #d4a7d5;
}

.tabla-container td {
    padding: 12px;
    border-bottom: 1px solid #E1B8E2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.tabla-container tr:last-child td {
    border-bottom: none;
}

.tabla-container tr:hover {
    background-color: #f8f0f8;
}

/* Estilo para el scrollbar */
.tabla-container::-webkit-scrollbar {
    width: 8px;
}

.tabla-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.tabla-container::-webkit-scrollbar-thumb {
    background: #E1B8E2;
    border-radius: 4px;
}

.tabla-container::-webkit-scrollbar-thumb:hover {
    background: #d4a7d5;
}

/* Ajustes para las columnas */
.tabla-container th:nth-child(1),
.tabla-container td:nth-child(1) {
    width: 10%;
}

.tabla-container th:nth-child(2),
.tabla-container td:nth-child(2) {
    width: 30%;
}

.tabla-container th:nth-child(3),
.tabla-container td:nth-child(3) {
    width: 20%;
}

.tabla-container th:nth-child(4),
.tabla-container td:nth-child(4) {
    width: 25%;
}

.tabla-container th:nth-child(5),
.tabla-container td:nth-child(5) {
    width: 15%;
}

.acciones {
    display: flex;
    gap: 8px;
    justify-content: flex-start;
}

.btn-editar, .btn-eliminar {
    padding: 6px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-editar {
    background-color: #E1B8E2;
    color: white;
}

.btn-eliminar {
    background-color: #ff6b6b;
    color: white;
}

.btn-editar:hover {
    background-color: #d4a7d5;
}

.btn-eliminar:hover {
    background-color: #ff5252;
} 