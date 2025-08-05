// js/validacion_registro.js
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("registerForm");
  const email = form.email;
  const pwd = form.password;
  const cpwd = form.confirm_password;

  // Regex para contraseña
  const pwdRegex = /^(?=.{8,}$)(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*_=+\-]).*$/;

  // Función común para mostrar/ocultar valid-feedback
  function toggleValidity(input, isValid) {
    if (isValid) {
      input.classList.remove("is-invalid");
      input.classList.add("is-valid");
    } else {
      input.classList.remove("is-valid");
      input.classList.add("is-invalid");
    }
  }

  // Validación en tiempo real del email
  email.addEventListener("input", () => {
    toggleValidity(email, email.checkValidity());
  });

  // Validación en tiempo real de la contraseña
  pwd.addEventListener("input", () => {
    const ok = pwdRegex.test(pwd.value);
    pwd.setCustomValidity(ok ? "" : "insegura");
    toggleValidity(pwd, ok);
    validateMatch();
  });

  // Validar que password y confirm_password coincidan
  cpwd.addEventListener("input", validateMatch);
  function validateMatch() {
    const ok = pwd.value === cpwd.value && pwd.value !== "";
    cpwd.setCustomValidity(ok ? "" : "no-match");
    toggleValidity(cpwd, ok);
  }

  // Al enviar, bloquea si hay errores y fuerza mostrar feedback
  form.addEventListener("submit", event => {
    if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
    }
    form.classList.add("was-validated");
  });
});
