document.addEventListener('DOMContentLoaded', () => {
  const form  = document.getElementById('loginForm');
  const email = form.email;
  const pwd   = form.password;

  // Validación en tiempo real de email
  email.addEventListener('input', () => {
    if (email.checkValidity()) {
      email.classList.remove('is-invalid');
      email.classList.add('is-valid');
    } else {
      email.classList.remove('is-valid');
      email.classList.add('is-invalid');
    }
  });

  const pwdRegex = /^(?=.{8,}$)(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*_=+\-]).*$/;

  // Validación en tiempo real de contraseña
  pwd.addEventListener('input', () => {
   if (pwdRegex.test(pwd.value)) {
      pwd.setCustomValidity('');
      pwd.classList.replace('is-invalid','is-valid');
    } else {
      pwd.setCustomValidity('insegura');
      pwd.classList.replace('is-valid','is-invalid');
    //   event.preventDefault();
    //   event.stopPropagation();
      form.classList.add('was-validated');
    }
  });

  // Al enviar, bloquea si hay errores y muestra feedback
  form.addEventListener('submit', event => {
    if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
      form.classList.add('was-validated');
    }
  });
});
