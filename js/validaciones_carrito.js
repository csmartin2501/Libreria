// validaciones_carrito.js

document.addEventListener('DOMContentLoaded', function() {
    // Validación para formularios de cantidad en el carrito
    const cantidadInputs = document.querySelectorAll('input[name="cantidad"]');
    
    cantidadInputs.forEach(input => {
        input.addEventListener('input', function() {
            validateQuantity(this);
        });
        
        input.addEventListener('blur', function() {
            validateQuantity(this);
        });
    });
    
    // Validación para el checkout
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            if (!validateCheckoutForm()) {
                e.preventDefault();
            }
        });
    }
});

function validateQuantity(input) {
    const value = parseInt(input.value);
    const min = parseInt(input.getAttribute('min')) || 0;
    const max = parseInt(input.getAttribute('max')) || 999;
    
    // Remover clases de validación anteriores
    input.classList.remove('is-valid', 'is-invalid');
    
    if (isNaN(value) || value < min || value > max) {
        input.classList.add('is-invalid');
        showValidationMessage(input, `La cantidad debe estar entre ${min} y ${max}`);
        return false;
    } else {
        input.classList.add('is-valid');
        hideValidationMessage(input);
        return true;
    }
}

function validateCheckoutForm() {
    const direccionInput = document.getElementById('direccion_envio');
    let isValid = true;
    
    // Validar dirección de envío
    if (!direccionInput.value.trim()) {
        direccionInput.classList.add('is-invalid');
        showValidationMessage(direccionInput, 'La dirección de envío es requerida');
        isValid = false;
    } else if (direccionInput.value.trim().length < 10) {
        direccionInput.classList.add('is-invalid');
        showValidationMessage(direccionInput, 'La dirección debe ser más específica (mínimo 10 caracteres)');
        isValid = false;
    } else {
        direccionInput.classList.remove('is-invalid');
        direccionInput.classList.add('is-valid');
        hideValidationMessage(direccionInput);
    }
    
    return isValid;
}

function showValidationMessage(input, message) {
    // Buscar o crear el elemento de mensaje
    let messageElement = input.parentNode.querySelector('.validation-message');
    
    if (!messageElement) {
        messageElement = document.createElement('div');
        messageElement.className = 'validation-message text-danger small mt-1';
        input.parentNode.appendChild(messageElement);
    }
    
    messageElement.textContent = message;
}

function hideValidationMessage(input) {
    const messageElement = input.parentNode.querySelector('.validation-message');
    if (messageElement) {
        messageElement.remove();
    }
}

// Función para confirmar eliminación de items
function confirmarEliminacion(titulo) {
    return confirm(`¿Estás seguro de que quieres eliminar "${titulo}" del carrito?`);
}

// Función para confirmar vaciado del carrito
function confirmarVaciarCarrito() {
    return confirm('¿Estás seguro de que quieres vaciar todo el carrito? Esta acción no se puede deshacer.');
}

// Función para actualizar totales dinámicamente (opcional)
function actualizarTotales() {
    // Esta función podría implementarse para calcular totales en tiempo real
    // sin necesidad de recargar la página
}

// Validación en tiempo real para formularios de agregar al carrito
function validarAgregarCarrito(form) {
    const cantidadInput = form.querySelector('input[name="cantidad"]');
    const libroId = form.querySelector('input[name="libro_id"]').value;
    
    if (!validateQuantity(cantidadInput)) {
        return false;
    }
    
    // Mostrar mensaje de carga (opcional)
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Agregando...';
    submitBtn.disabled = true;
    
    // Restaurar el botón después de un tiempo (esto sería manejado por el servidor normalmente)
    setTimeout(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    }, 2000);
    
    return true;
}
