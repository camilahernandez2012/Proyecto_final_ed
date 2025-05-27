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
      if (page === "panel") {
        initPanel();

        // Inicializar formulario de mensaje una vez cargado el DOM
        setTimeout(() => {
          const form = document.getElementById("form-mensaje");
          if (form) {
            form.addEventListener("submit", function (e) {
              e.preventDefault();
              const para = document.getElementById("mensaje-para").value;
              const mensaje = document.getElementById("mensaje-texto").value;
              const estado = document.getElementById("mensaje-estado");

              fetch("php/secciones/enviar_mensaje.php", {
                method: "POST",
                body: new URLSearchParams({ para, mensaje })
              })
                .then(res => res.text())
                .then(data => {
                  estado.innerText = data;
                });
            });
          }
        }, 100);
      }

      if (page === "login") initLogin();
      if (page === "registro") initRegistro();
      if (page === "panel_admin") initAdminPanel();
    });
}

function navegarA(page) {
  history.pushState({ page: page }, "", `#${page}`);
  loadPage(page);
}

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
      if (nombre === "mensajes") initMensajes();
    });
}

function initMensajes() {
  // Podrías agregar lógica extra si lo necesitas
}

function initHazAmigos() {
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

function enviarMensaje(paraId) {
  const inputPara = document.getElementById("mensaje-para");
  const textarea = document.getElementById("mensaje-texto");
  const estado = document.getElementById("mensaje-estado");

  if (!inputPara || !textarea || !estado) {
    alert("Error: el modal de mensajes no está disponible.");
    return;
  }

  inputPara.value = paraId;
  textarea.value = "";
  estado.innerText = "";
  new bootstrap.Modal(document.getElementById("modalMensaje")).show();
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

function initHistorial() {}

function initRecomendaciones() {}

function initAdminPanel() {
  cargarSeccionAdmin("libros");

  fetch("php/admin/grafo.php")
    .then(res => res.json())
    .then(data => dibujarGrafo(data));
}

function cargarSeccionAdmin(nombre) {
  fetch(`php/admin/${nombre}.php`)
    .then(res => res.text())
    .then(html => {
      document.getElementById("contenido-panel-admin").innerHTML = html;

      if (nombre === "libros") initAdminLibros();
      if (nombre === "usuarios") initAdminUsuarios();

      if (nombre === "grafo") {
        document.getElementById("contenido-panel-admin").innerHTML = `
          <h3 class="mt-4">🔗 Red de Afinidad entre Lectores</h3>
          <div id="grafo-container" style="width:100%; height:500px; border:1px solid #ccc;"></div>
        `;
        fetch("php/admin/grafo.php")
          .then(res => res.json())
          .then(data => dibujarGrafo(data));
      }
    });
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

function dibujarGrafo(data) {
  const container = document.getElementById("grafo-container");
  const width = container.offsetWidth;
  const height = 400;

  d3.select("#grafo-container").html("");

  const svg = d3.select("#grafo-container")
    .append("svg")
    .attr("width", width)
    .attr("height", height);

  const simulation = d3.forceSimulation(data.nodes)
    .force("link", d3.forceLink(data.links).id(d => d.id).distance(80))
    .force("charge", d3.forceManyBody().strength(-200))
    .force("center", d3.forceCenter(width / 2, height / 2));

  const link = svg.append("g")
    .attr("stroke", "#ccc")
    .selectAll("line")
    .data(data.links)
    .join("line")
    .attr("stroke-width", 1.5);

  const node = svg.append("g")
    .selectAll("circle")
    .data(data.nodes)
    .join("circle")
    .attr("r", 10)
    .attr("fill", "#1f77b4")
    .call(drag(simulation));

  const label = svg.append("g")
    .selectAll("text")
    .data(data.nodes)
    .join("text")
    .text(d => d.name)
    .attr("font-size", "10px")
    .attr("x", 12)
    .attr("dy", 4);

  simulation.on("tick", () => {
    link
      .attr("x1", d => d.source.x)
      .attr("y1", d => d.source.y)
      .attr("x2", d => d.target.x)
      .attr("y2", d => d.target.y);

    node
      .attr("cx", d => d.x)
      .attr("cy", d => d.y);

    label
      .attr("x", d => d.x + 10)
      .attr("y", d => d.y);
  });

  function drag(sim) {
    return d3.drag()
      .on("start", (event, d) => {
        if (!event.active) sim.alphaTarget(0.3).restart();
        d.fx = d.x;
        d.fy = d.y;
      })
      .on("drag", (event, d) => {
        d.fx = event.x;
        d.fy = event.y;
      })
      .on("end", (event, d) => {
        if (!event.active) sim.alphaTarget(0);
        d.fx = null;
        d.fy = null;
      });
  }
}
