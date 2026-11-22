
const tbody = document.getElementById("tabla-canciones");
const mensajes = document.getElementById("mensajes");
const formulario = document.getElementById("formulario-cancion");
const botonGuardar = document.getElementById("boton-guardar");

const tituloFormulario = document.getElementById("titulo-formulario");
const modoEdicion = document.getElementById("modo-edicion");
const botonCancelar = document.getElementById("boton-cancelar");

const token = document.querySelector('meta[name="csrf-token"]').content;

let canciones = [];
let operacionEnCurso = false;
let idEnEdicion = null;

function mostrarMensaje(texto) {
  mensajes.textContent = texto;
}

function duracionLegible(segundos) {
  const m = Math.floor(segundos / 60);
  const s = String(segundos % 60).padStart(2, "0");
  return `${m}:${s}`;
}

function bloquearAcciones(bloquear) {
  operacionEnCurso = bloquear;
  botonGuardar.disabled = bloquear;
  botonCancelar.disabled = bloquear;

  const botonesAccion = tbody.querySelectorAll("button");

  botonesAccion.forEach((boton) => {
    boton.disabled = bloquear;
  });
}

function renderizarCanciones(lista) {
  tbody.replaceChildren();

  if (lista.length === 0) {
    const fila = document.createElement("tr");
    const celda = document.createElement("td");

    celda.colSpan = 5;
    celda.textContent = "Todavía no hay canciones";

    fila.append(celda);
    tbody.append(fila);
    return;
  }

  for (const cancion of lista) {
    const fila = document.createElement("tr");

    for (const valor of [
      cancion.title,
      cancion.artist,
      cancion.category,
      duracionLegible(cancion.duration),
    ]) {
      const celda = document.createElement("td");
      celda.textContent = valor;
      fila.append(celda);
    }

    const acciones = document.createElement("td");

    const editar = document.createElement("button");
    editar.type = "button";
    editar.textContent = "Editar";
    editar.dataset.id = cancion.id;
    editar.dataset.accion = "editar";
    editar.disabled = false;

    const eliminar = document.createElement("button");
    eliminar.type = "button";
    eliminar.textContent = "Eliminar";
    eliminar.dataset.id = cancion.id;
    eliminar.dataset.accion = "eliminar";
    eliminar.disabled = false;

    acciones.append(editar, " ", eliminar);
    fila.append(acciones);
    tbody.append(fila);
  }
}

async function cargarCatalogo() {
  mostrarMensaje("Cargando catálogo…");

  try {
    const respuesta = await fetch("api/catalogo.php?action=list");
    const contenido = await respuesta.json();

    if (!respuesta.ok || contenido.ok !== true) {
      throw new Error(contenido.error || "No se pudo consultar.");
    }

    if (!Array.isArray(contenido.data)) {
      throw new Error("La respuesta no contiene una lista.");
    }

    canciones = contenido.data;
    renderizarCanciones(canciones);
    mostrarMensaje("");
  } catch (error) {
    mostrarMensaje("Error: " + error.message);
  }
}

async function eliminarCancion(id) {
  const elegida = canciones.find(
    (cancion) => Number(cancion.id) === id
  );

  if (!elegida) {
    mostrarMensaje("No se ha encontrado la canción.");
    return;
  }

  const confirmar = window.confirm(
    "¿Seguro que quieres eliminar la canción '" + elegida.title + "'?"
  );

  if (!confirmar) {
    return;
  }

  bloquearAcciones(true);
  mostrarMensaje("Eliminando canción…");

  try {
    const respuesta = await fetch("api/catalogo.php?action=delete", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": token,
      },
      body: JSON.stringify({ id: id }),
    });

    const contenido = await respuesta.json();

    if (!respuesta.ok || contenido.ok !== true) {
      throw new Error(contenido.error || "No se pudo eliminar la canción.");
    }

    if (!Array.isArray(contenido.data)) {
      throw new Error("La respuesta no contiene una lista.");
    }

    canciones = contenido.data;
    renderizarCanciones(canciones);

    if (idEnEdicion === id) {
      salirEdicion();
    }

    mostrarMensaje("Canción eliminada correctamente.");
  } catch (error) {
    mostrarMensaje("Error: " + error.message);
  } finally {
    bloquearAcciones(false);
  }
}

tbody.addEventListener("click", function (evento) {
  const boton = evento.target.closest("button[data-accion]");

  if (!boton) return;
  if (operacionEnCurso) return;

  const accion = boton.dataset.accion;

  if (accion !== "editar" && accion !== "eliminar") return;

  const id = Number(boton.dataset.id);

  if (!Number.isInteger(id) || id <= 0) {
    mostrarMensaje("Identificador no válido");
    return;
  }

  if (accion === "eliminar") {
    eliminarCancion(id);
    return;
  }

  const elegida = canciones.find(
    (cancion) => Number(cancion.id) === id
  );

  if (!elegida) {
    mostrarMensaje("No se ha encontrado la canción");
    return;
  }

  formulario.elements.title.value = elegida.title;
  formulario.elements.artist.value = elegida.artist;
  formulario.elements.category.value = elegida.category;
  formulario.elements.duration.value = elegida.duration;
  formulario.elements.src.value = elegida.src;

  idEnEdicion = id;

  tituloFormulario.textContent = "Editar canción";
  botonGuardar.textContent = "Guardar cambios";
  modoEdicion.textContent = "Editando: " + elegida.title;
  botonCancelar.hidden = false;

  mostrarMensaje("");
});

function salirEdicion() {
  formulario.reset();

  idEnEdicion = null;

  tituloFormulario.textContent = "Añadir canción";
  botonGuardar.textContent = "Añadir canción";
  modoEdicion.textContent = "";
  botonCancelar.hidden = true;

  mostrarMensaje("");
}

async function crearCancion(evento) {
  evento.preventDefault();

  if (operacionEnCurso) return;
  if (!formulario.reportValidity()) return;

  const nuevaCancion = {
    title: formulario.elements.title.value,
    artist: formulario.elements.artist.value,
    category: formulario.elements.category.value,
    duration: Number(formulario.elements.duration.value),
    src: formulario.elements.src.value,
  };

  let accion = "create";

  if (idEnEdicion !== null) {
    accion = "update";
    nuevaCancion.id = idEnEdicion;
  }

  bloquearAcciones(true);
  mostrarMensaje("Guardando…");

  try {
    const respuesta = await fetch("api/catalogo.php?action=" + accion, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-Token": token,
      },
      body: JSON.stringify(nuevaCancion),
    });

    const contenido = await respuesta.json();

    if (!respuesta.ok || contenido.ok !== true) {
      throw new Error(contenido.error || "No se pudo guardar.");
    }

    if (!Array.isArray(contenido.data)) {
      throw new Error("La respuesta no contiene una lista.");
    }

    canciones = contenido.data;
    renderizarCanciones(canciones);

    if (accion === "update") {
      salirEdicion();
      mostrarMensaje("Canción modificada");
    } else {
      formulario.reset();
      mostrarMensaje("Canción añadida");
    }
  } catch (error) {
    mostrarMensaje("Error: " + error.message);
  } finally {
    bloquearAcciones(false);
  }
}

formulario.addEventListener("submit", crearCancion);

botonCancelar.addEventListener("click", function () {
  salirEdicion();
});

cargarCatalogo();
