// SPA de Biblioteca Digital Interactiva - app.js

document.addEventListener("DOMContentLoaded", () => {
  checkSession();

  window.addEventListener("popstate", (event) => {
    if (event.state && event.state.page) {
      loadPage(event.state.page);
    }
  });
});

function checkSession() {
  fetch("php/session_check.php")
    .then(res => res.text())
    .then(data => {
      if (data === "admin") {
        loadPage("panel_admin");
      } else if (data === "lector") {
        loadPage("panel");
      } else {
        loadPage("login");
      }
    });
}

function loadPage(page) {
  fetch(`html/${page}.html`)
    .then(res => res.text())
    .then(html => {
      document.getElementById("app-container").innerHTML = html;
      if (page === "panel") initPanel();
      if (page === "login") initLogin();
      if (page === "registro") initRegistro();
      if (page === "panel_admin") initAdminPanel();
    });
}

function navegarA(page) {
  history.pushState({ page: page }, "", `#${page}`);
  loadPage(page);
}

//////////////////////////////
// Inicializadores por página
//////////////////////////////

function initLogin() {
  const form = document.getElementById("form-login");
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const formData = new FormData(form);
    fetch("php/login.php", {
      method: "POST",
      body: formData
    })
      .then(res => res.text())
      .then(data => {
        if (data === "ok") {
          checkSession();
        } else {
          document.getElementById("mensaje-login").innerText = data;
        }
      });
  });

  document.getElementById("ir-registro").addEventListener("click", () => {
    navegarA("registro");
  });
}

function initRegistro() {
  const form = document.getElementById("form-registro");
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const formData = new FormData(form);
    fetch("php/registrar.php", {
      method: "POST",
      body: formData
    })
      .then(res => res.text())
      .then(data => {
        if (data === "ok") {
          alert("Registro exitoso. Inicia sesión.");
          navegarA("login");
        } else {
          document.getElementById("mensaje-registro").innerText = data;
        }
      });
  });

  document.getElementById("ir-login").addEventListener("click", () => {
    navegarA("login");
  });
}

function initPanel() {
  cargarSeccion("catalogo");

}

function cargarSeccion(nombre) {
  fetch(`php/secciones/${nombre}.php`)
    .then(res => res.text())
    .then(html => {
      document.getElementById("contenido-panel").innerHTML = html;
      if (nombre === "catalogo") initCatalogo();
      if (nombre === "historial") initHistorial();
      if (nombre === "recomendaciones") initRecomendaciones();
      if (nombre === "haz_amigos") initHazAmigos();
    });
}

function cargarSeccionAdmin(nombre) {
  fetch(`php/admin/${nombre}.php`)
    .then(res => res.text())
    .then(html => {
      document.getElementById("contenido-panel-admin").innerHTML = html;

      if (nombre === "libros") initAdminLibros();
      if (nombre === "usuarios") {
        initAdminUsuarios();

        // ✅ Registrar función global para búsqueda
        window.filtrarLectores = function () {
          const filtro = document.getElementById("busqueda-lector")?.value.trim();
          const url = "php/admin/usuarios.php" + (filtro ? `?filtro=${encodeURIComponent(filtro)}` : "");

          fetch(url)
            .then(res => res.text())
            .then(html => {
              document.getElementById("contenido-panel-admin").innerHTML = html;
            });
        };
      }

      if (nombre === "estadisticas") {
        // Esperar brevemente a que el DOM se actualice antes de usar Chart.js
        setTimeout(() => {
          const el = document.getElementById("datos-estadisticas");

          const prestadosLabels = JSON.parse(el?.dataset.prestadosLabels || "[]");
          const prestadosData = JSON.parse(el?.dataset.prestadosData || "[]");
          const valoradosLabels = JSON.parse(el?.dataset.valoradosLabels || "[]");
          const valoradosData = JSON.parse(el?.dataset.valoradosData || "[]");


          console.log("📈 Datos de préstamos:", prestadosLabels, prestadosData);
          console.log("⭐ Datos de valoraciones:", valoradosLabels, valoradosData);

          if (prestadosLabels && prestadosLabels.length > 0) {
            new Chart(document.getElementById('graficoPrestados'), {
              type: 'pie',
              data: {
                labels: prestadosLabels,
                datasets: [{
                  label: 'Préstamos',
                  data: prestadosData,
                  backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8']
                }]
              }
            });
          }

          if (valoradosLabels && valoradosLabels.length > 0) {
            new Chart(document.getElementById('graficoValorados'), {
              type: 'pie',
              data: {
                labels: valoradosLabels,
                datasets: [{
                  label: 'Valoraciones',
                  data: valoradosData,
                  backgroundColor: ['#6f42c1', '#fd7e14', '#20c997', '#6610f2', '#e83e8c']
                }]
              }
            });
          }
        }, 100); // Espera corta para asegurar que el DOM esté renderizado
      }
    });
}


function initAdminPanel() {
  cargarSeccionAdmin("libros");
}

function cerrarSesion() {
  fetch("php/logout.php").then(() => location.reload());
}

function initAdminLibros() {
  const form = document.getElementById("form-agregar-libro");
  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(form);
      fetch("php/admin/agregar_libro.php", {
        method: "POST",
        body: formData
      })
        .then(res => res.text())
        .then(data => {
          alert(data);
          cargarSeccionAdmin("libros");
        });
    });
  }

  const botonesEliminar = document.querySelectorAll("button.btn-danger");
  botonesEliminar.forEach(btn => {
    btn.addEventListener("click", () => {
      const id = btn.getAttribute("onclick").match(/\d+/)[0];
      eliminarLibro(id);
    });
  });
}

function eliminarLibro(id) {
  if (!confirm("¿Estás segura de eliminar este libro?")) return;
  fetch("php/admin/eliminar_libro.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "id=" + id
  })
    .then(res => res.text())
    .then(data => {
      alert(data);
      cargarSeccionAdmin("libros");
    });
}

function initAdminUsuarios() {
  const botones = document.querySelectorAll("button.btn-danger");
  botones.forEach(btn => {
    btn.addEventListener("click", () => {
      const id = btn.getAttribute("onclick").match(/\d+/)[0];
      eliminarUsuario(id);
    });
  });
}

function filtrarLectores() {
  const filtro = document.getElementById("busqueda-lector")?.value.trim();
  const url = "php/admin/usuarios.php" + (filtro ? `?filtro=${encodeURIComponent(filtro)}` : "");

  fetch(url)
    .then(res => res.text())
    .then(html => {
      document.getElementById("contenido-panel-admin").innerHTML = html;
    });
}


function eliminarUsuario(id) {
  if (!confirm("¿Estás segura de eliminar este usuario?")) return;
  fetch("php/admin/eliminar_usuario.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "id=" + id
  })
    .then(res => res.text())
    .then(data => {
      alert(data);
      cargarSeccionAdmin("usuarios");
    });
}

function verDetallesUsuario(id) {
  fetch(`php/admin/detalles_usuario.php?id=${id}`)
    .then(res => res.text())
    .then(html => {
      const contenedor = document.createElement("div");
      contenedor.innerHTML = html;
      document.getElementById("contenido-panel-admin").innerHTML = contenedor.innerHTML;
    });
}

function initCatalogo() {
  cargarLibros();
  document.getElementById("filtro-categoria")?.addEventListener("change", filtrarLibros);
  document.getElementById("busqueda")?.addEventListener("keydown", e => {
    if (e.key === "Enter") filtrarLibros();
  });
  document.querySelector("button[onclick='filtrarLibros()']")?.addEventListener("click", filtrarLibros);
}

function cargarLibros(filtro = "", categoria = "") {
  const params = new URLSearchParams();
  if (filtro) params.append("busqueda", filtro);
  if (categoria) params.append("categoria", categoria);

  fetch(`php/secciones/catalogo_lista.php?${params.toString()}`)
    .then(res => res.text())
    .then(html => {
      document.getElementById("lista-libros").innerHTML = html;
    });
}

function filtrarLibros() {
  const filtro = document.getElementById("busqueda")?.value.trim() || "";
  const categoria = document.getElementById("filtro-categoria")?.value || "";
  cargarLibros(filtro, categoria);
}

function alquilarLibro(libroId, btn) {
  if (!confirm("¿Deseas alquilar este libro?")) return;
  btn.disabled = true;

  fetch("php/secciones/alquilar.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "id=" + libroId
  })
    .then(res => res.text())
    .then(msg => {
      alert(msg);
      cargarSeccion("catalogo");
    })
    .catch(() => {
      alert("Error al alquilar.");
      btn.disabled = false;
    });
}

function unirseCola(libroId, btn) {
  if (!confirm("Este libro está prestado. ¿Deseas unirte a la cola de espera?")) return;
  btn.disabled = true;

  fetch("php/secciones/unirse_cola.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "libro_id=" + libroId
  })
    .then(res => res.text())
    .then(msg => {
      alert(msg);
      cargarSeccion("catalogo");
    })
    .catch(() => {
      alert("Error al unirte a la cola.");
      btn.disabled = false;
    });
}


function initHistorial() {
  // Nada extra aún, pero sirve como hook
}

function devolverLibro(libroId) {
  if (!confirm("¿Seguro que deseas devolver este libro?")) return;

  fetch("php/secciones/devolver.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "id=" + libroId
  })
    .then(res => res.text())
    .then(msg => {
      alert(msg);
      cargarSeccion("historial");
    });
}

function valorarLibro(libroId, valor) {
  fetch("php/secciones/valorar.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "libro_id=" + libroId + "&valor=" + valor
  })
    .then(res => res.text())
    .then(msg => {
      alert(msg);
      cargarSeccion("historial");
    });
}

function initHazAmigos() {
  console.log("Haz amigos cargado");

  const input = document.getElementById("input-busqueda");
  const boton = document.getElementById("btn-buscar");
  const contenedor = document.getElementById("resultados-busqueda");

  boton.addEventListener("click", () => {
    const q = input.value.trim();
    if (q === "") return;

    fetch(`php/secciones/buscar_usuarios.php?q=${encodeURIComponent(q)}`)
      .then(res => res.text())
      .then(html => {
        contenedor.innerHTML = html;
      });
  });
}






