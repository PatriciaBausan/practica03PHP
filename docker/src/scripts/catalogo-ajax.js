const tbody = document.getElementById('tabla-canciones');
const mensajes = document.getElementById('mensajes');

let canciones = [];

function mostrarMensaje(texto) {
  mensajes.textContent = texto;
}

function duracionLegible(segundos) {
  const m = Math.floor(segundos / 60);
  const s = String(segundos % 60).padStart(2, '0');
  return `${m}:${s}`;
}

function renderizarCanciones(lista) {
  tbody.replaceChildren();

  if (lista.length === 0) {
    const fila = document.createElement('tr');
    const celda = document.createElement('td');
    celda.colSpan = 5;
    celda.textContent = 'Todavía no hay canciones';
    fila.append(celda);
    tbody.append(fila);
    return;
  }

  for (const cancion of lista) {
    const fila = document.createElement('tr');

    for (const valor of [
      cancion.title,
      cancion.artist,
      cancion.category,
      duracionLegible(cancion.duration),
    ]) {
      const celda = document.createElement('td');
      celda.textContent = valor;
      fila.append(celda);
    }

    const acciones = document.createElement('td');

    const editar = document.createElement('button');
    editar.type = 'button';
    editar.textContent = 'Editar';
    editar.dataset.id = cancion.id;
    editar.disabled = true; 

    const eliminar = document.createElement('button');
    eliminar.type = 'button';
    eliminar.textContent = 'Eliminar';
    eliminar.dataset.id = cancion.id;
    eliminar.disabled = true; 

    acciones.append(editar, ' ', eliminar);
    fila.append(acciones);
    tbody.append(fila);
  }
}

async function cargarCatalogo() {
  mostrarMensaje('Cargando catálogo…');
  try {
    const respuesta = await fetch('api/catalogo.php?action=list');
    const contenido = await respuesta.json();

    if (!respuesta.ok || contenido.ok !== true) {
      throw new Error(contenido.error || 'No se pudo consultar.');
    }
    if (!Array.isArray(contenido.data)) {
      throw new Error('La respuesta no contiene una lista.');
    }

    canciones = contenido.data;
    renderizarCanciones(canciones);
    mostrarMensaje('');
  } catch (error) {
    mostrarMensaje('Error: ' + error.message);
  } finally {
  
  }
}

cargarCatalogo();