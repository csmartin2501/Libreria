// js/validacion_registro_libro.js
document.addEventListener('DOMContentLoaded', () => {
  const form      = document.getElementById('bookForm');
  const precio    = document.getElementById('precio');
  const cantidad  = document.getElementById('cantidad');

  function validatePositive(input) {
    const val   = parseFloat(input.value);
    const valid = !isNaN(val) && val > 0;

    input.setCustomValidity(valid ? '' : 'invalid');
    if (valid) {
      input.classList.remove('is-invalid');
      input.classList.add('is-valid');
    } else {
      input.classList.remove('is-valid');
      input.classList.add('is-invalid');
    }
  }

  precio.addEventListener('input', () => validatePositive(precio));
  cantidad.addEventListener('input', () => validatePositive(cantidad));

  form.addEventListener('submit', event => {
    if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
    }
    form.classList.add('was-validated');
  });
});
