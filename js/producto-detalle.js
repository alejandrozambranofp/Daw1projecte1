/**
* Suma o resta unidades de un ingrediente y recalcula el precio total del plato
     */
    function cambiarCantidad(idIngrediente, delta) {
        const input = document.getElementById('input-qty-' + idIngrediente);
        const vista = document.getElementById('vista-qty-' + idIngrediente);

        let cantidadActual = parseInt(input.value, 10);
        let nuevaCantidad = cantidadActual + delta;

        // Limitamos entre 0 (sin ingrediente) y un máximo razonable de 5 unidades
        if (nuevaCantidad < 0) nuevaCantidad = 0;
        if (nuevaCantidad > 5) nuevaCantidad = 5;

        // Actualizamos tanto el número visible como el input oculto que se enviará por POST
        input.value = nuevaCantidad;
        vista.textContent = nuevaCantidad;

        recalcularTotal();
    }

    /**
     * Recorre todas las filas de ingredientes y calcula:
     * Precio Base del plato + suma de los extras añadidos
     */
    function recalcularTotal() {
        const spanTotal = document.getElementById('precio-total');
        let total = parseFloat(spanTotal.dataset.base);

        // Seleccionamos todas las filas de ingredientes
        const filas = document.querySelectorAll('.ingrediente-fila');

        filas.forEach(fila => {
            const precioExtra = parseFloat(fila.dataset.precio);
            const esBase = parseInt(fila.dataset.base, 10); // 1 si viene incluido, 0 si es extra
            const cantidad = parseInt(fila.querySelector('input[type="hidden"]').value, 10);

            // Solo cobramos las unidades que superen la cantidad incluida por defecto (1 si es base, 0 si es extra)
            const unidadesCobrables = Math.max(0, cantidad - esBase);
            total += unidadesCobrables * precioExtra;
        });

        // Formateamos con coma decimal estilo español (ej: 12,50)
        spanTotal.textContent = total.toFixed(2).replace('.', ',');
    }     