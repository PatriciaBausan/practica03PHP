let canciones = [];

const PORTADA_POR_DEFECTO = "assets/portada.png";
const ETIQUETAS_CATEGORIA = {
    "electronica": "Electrónica",
    "rock": "Rock",
    "bandas-sonoras": "Bandas sonoras"
};

function adaptarCancion(ficha) {
    return {
        id: ficha.id,
        titulo: ficha.title,
        artista: ficha.artist,
        genero: ETIQUETAS_CATEGORIA[ficha.category] || ficha.category,
        audio: ficha.src,
        imagen: ficha.imagen || PORTADA_POR_DEFECTO
    };
}

const listaCanciones = document.getElementById("listaCanciones");
const numeroCanciones = document.getElementById("numeroCanciones");

const imagenCancion = document.getElementById("imagenCancion");
const estadoCancion = document.getElementById("estadoCancion");
const tituloCancion = document.getElementById("tituloCancion");
const artistaCancion = document.getElementById("artistaCancion");
const generoCancion = document.getElementById("generoCancion");

const visualizador = document.getElementById("visualizador");

const graves = document.getElementById("graves");
const medios = document.getElementById("medios");
const agudos = document.getElementById("agudos");

const valorGraves = document.getElementById("valorGraves");
const valorMedios = document.getElementById("valorMedios");
const valorAgudos = document.getElementById("valorAgudos");

const btnRestablecer = document.getElementById("btnRestablecer");

const tiempoActual = document.getElementById("tiempoActual");
const barraProgreso = document.getElementById("barraProgreso");
const tiempoTotal = document.getElementById("tiempoTotal");

const miniImagen = document.getElementById("miniImagen");
const miniTitulo = document.getElementById("miniTitulo");
const miniArtista = document.getElementById("miniArtista");

const btnAleatorio = document.getElementById("btnAleatorio");
const btnAnterior = document.getElementById("btnAnterior");
const btnPlay = document.getElementById("btnPlay");
const btnSiguiente = document.getElementById("btnSiguiente");
const btnRepetir = document.getElementById("btnRepetir");

const volumen = document.getElementById("volumen");
const valorVolumen = document.getElementById("valorVolumen");
const iconoVolumen = document.getElementById("iconoVolumen");


let cancionActual = 0;
let sonido = null;
let reproduciendo = false;

let aleatorio = false;
let repetir = false;

let historialCanciones = [];

let animacionProgreso = null;
let animacionVisualizador = null;

let filtroGraves = null;
let filtroMedios = null;
let filtroAgudos = null;


let analizador = null;
let datosFrecuencia = null;


function iniciarEcualizador() {

    if (analizador) {
        return;
    }

    if (!Howler.usingWebAudio || !Howler.ctx || !Howler.masterGain) {
        console.error("Web Audio no está disponible");
        estadoCancion.textContent = "ECUALIZADOR NO DISPONIBLE";
        return;
    }

    const contexto = Howler.ctx;

    filtroGraves = contexto.createBiquadFilter();
    filtroMedios = contexto.createBiquadFilter();
    filtroAgudos = contexto.createBiquadFilter();


    filtroGraves.type = "lowshelf";
    filtroGraves.frequency.value = 200;
    filtroGraves.gain.value = Number(graves.value);

    filtroMedios.type = "peaking";
    filtroMedios.frequency.value = 1000;
    filtroMedios.Q.value = 1;
    filtroMedios.gain.value = Number(medios.value);

    filtroAgudos.type = "highshelf";
    filtroAgudos.frequency.value = 3000;
    filtroAgudos.gain.value = Number(agudos.value);

    analizador = contexto.createAnalyser();
    analizador.fftSize = 2048;
    analizador.smoothingTimeConstant = 0.8;

    datosFrecuencia = new Uint8Array(
        analizador.frequencyBinCount
    );

    Howler.masterGain.disconnect();
    Howler.masterGain.connect(filtroGraves);
    filtroGraves.connect(filtroMedios);
    filtroMedios.connect(filtroAgudos);
    filtroAgudos.connect(analizador);
    analizador.connect(contexto.destination);

    console.log("Ecualizador real iniciado correctamente");

}


function mostrarEstadoVacio(mensaje) {

    canciones = [];
    crearPlaylist();

    estadoCancion.textContent = "SIN CANCIONES";
    tituloCancion.textContent = mensaje;
    artistaCancion.textContent = "";
    generoCancion.textContent = "";
    miniTitulo.textContent = mensaje;
    miniArtista.textContent = "";

    [btnPlay, btnAnterior, btnSiguiente, btnAleatorio, btnRepetir, barraProgreso]
        .forEach(function (elemento) {
            elemento.disabled = true;
        });
}


function crearPlaylist() {

    listaCanciones.innerHTML = "";
    numeroCanciones.textContent =
        canciones.length + (canciones.length === 1 ? " canción" : " canciones");

    canciones.forEach(function (cancion, indice) {

        const elementoCancion = document.createElement("div");
        elementoCancion.classList.add("cancion");

        const imagen = document.createElement("img");
        imagen.src = cancion.imagen;
        imagen.alt = cancion.titulo;

        const texto = document.createElement("div");
        const titulo = document.createElement("h3");
        titulo.textContent = cancion.titulo;
        const artista = document.createElement("p");
        artista.textContent = cancion.artista;
        texto.appendChild(titulo);
        texto.appendChild(artista);

        elementoCancion.appendChild(imagen);
        elementoCancion.appendChild(texto);

        elementoCancion.addEventListener("click", function () {
            reproducirCancion(indice);
        });

        listaCanciones.appendChild(elementoCancion);

    });

}


function cargarCancion(indice) {

    if (canciones.length === 0 || !canciones[indice]) {
        return;
    }

    if (animacionProgreso !== null) {
        cancelAnimationFrame(animacionProgreso);
        animacionProgreso = null;
    }

    if (sonido) {
        sonido.stop();
        sonido.unload();
    }

    cancionActual = indice;

    const cancion = canciones[cancionActual];

    sonido = new Howl({
        src: [cancion.audio],
        volume: Number(volumen.value),
        html5: false,

        onplay: function () {
            reproduciendo = true;
            actualizarBotonPlay();
            estadoCancion.textContent = "REPRODUCIENDO";
            actualizarProgreso();
        },

        onpause: function () {
            reproduciendo = false;
            actualizarBotonPlay();
            estadoCancion.textContent = "PAUSADO";
        },

        onstop: function () {
            reproduciendo = false;
            actualizarBotonPlay();
        },

        onend: function () {
            reproduciendo = false;
            actualizarBotonPlay();
            if (repetir) {
                this.seek(0);
                this.play();
            } else {
                siguienteCancion();
            }
        },

        onload: function () {
            tiempoTotal.textContent =
                convertirTiempo(this.duration());
        },

        onloaderror: function (id, error) {
            estadoCancion.textContent = "ERROR AL CARGAR";
            console.error(
                "Error al cargar:",
                cancion.audio,
                error
            );
        },

        onplayerror: function (id, error) {
            estadoCancion.textContent = "ERROR AL REPRODUCIR";
            console.error(
                "Error al reproducir:",
                cancion.audio,
                error
            );
        }
    });

    imagenCancion.src = cancion.imagen;
    imagenCancion.alt = cancion.titulo;

    tituloCancion.textContent = cancion.titulo;
    artistaCancion.textContent = cancion.artista;
    generoCancion.textContent = cancion.genero;

    miniImagen.src = cancion.imagen;
    miniImagen.alt = cancion.titulo;

    miniTitulo.textContent = cancion.titulo;
    miniArtista.textContent = cancion.artista;

    barraProgreso.value = 0;

    tiempoActual.textContent = "0:00";
    tiempoTotal.textContent = "0:00";

    estadoCancion.textContent = "LISTO PARA REPRODUCIR";

}

function reproducirCancion(indice, guardarHistorial = true) {

    if (canciones.length === 0) {
        return;
    }

    iniciarEcualizador();

    if (guardarHistorial && indice !== cancionActual) {
        historialCanciones.push(cancionActual);
    }

    cargarCancion(indice);
    sonido.play();
}


function playPause() {

    if (canciones.length === 0) {
        return;
    }
    iniciarEcualizador();
    if (!sonido) {
        cargarCancion(cancionActual);
    }

    if (sonido.playing()) {
        sonido.pause();
    } else {
        sonido.play();
    }
}

function actualizarBotonPlay() {

    if (reproduciendo) {
        btnPlay.textContent = "⏸";
        btnPlay.title = "Pausar";

    } else {
        btnPlay.textContent = "▶";
        btnPlay.title = "Reproducir";
    }
}

function siguienteCancion() {
    if (canciones.length === 0) {
        return;
    }

    let siguiente;

    if (aleatorio && canciones.length > 1) {
        do {
            siguiente = Math.floor(Math.random() * canciones.length);
        } while (siguiente === cancionActual);

    } else {
        siguiente = cancionActual + 1;
        if (siguiente >= canciones.length) {
            siguiente = 0;
        }
    }
    reproducirCancion(siguiente);
}


function anteriorCancion() {

    if (canciones.length === 0) {
        return;
    }

    let anterior;

    if (historialCanciones.length > 0) {
        anterior = historialCanciones.pop();
    } else {
        anterior = cancionActual - 1;
        if (anterior < 0) {
            anterior = canciones.length - 1;
        }
    }
    reproducirCancion(anterior, false);
}

btnAleatorio.addEventListener("click", function () {

    aleatorio = !aleatorio;

    btnAleatorio.classList.toggle("activo", aleatorio);

    if (aleatorio) {
        btnAleatorio.title = "Desactivar aleatorio";
    } else {
        btnAleatorio.title = "Activar aleatorio";
    }
});

btnRepetir.addEventListener("click", function () {

    repetir = !repetir;

    btnRepetir.classList.toggle("activo", repetir);

    if (repetir) {
        btnRepetir.title = "Desactivar repetir";
    } else {
        btnRepetir.title = "Activar repetir";
    }
});

btnPlay.addEventListener("click", function () {
    playPause();
});

btnSiguiente.addEventListener("click", function () {
    siguienteCancion();
});

btnAnterior.addEventListener("click", function () {
    anteriorCancion();
});


volumen.addEventListener("input", function () {

    const valor = Number(volumen.value);

    valorVolumen.textContent = Math.round(valor * 100) + "%";

    actualizarIconoVolumen();

    if (sonido) {
        sonido.volume(valor);
    }
});

function actualizarIconoVolumen() {

    const valor = Number(volumen.value);

    if (valor === 0) {
        iconoVolumen.textContent = "🔇";
    } else {
        iconoVolumen.textContent = "🔊";
    }
}

barraProgreso.addEventListener("input", function () {

    if (!sonido) {
        return;
    }

    const duracion = sonido.duration();

    if (duracion > 0) {
        const porcentaje = Number(barraProgreso.value) / 100;
        const posicion = duracion * porcentaje;
        sonido.seek(posicion);
        tiempoActual.textContent = convertirTiempo(posicion);
    }
});

function actualizarProgreso() {

    if (!sonido) {
        return;
    }

    const sonidoActual = sonido;
    const posicion = sonidoActual.seek();
    const duracion = sonidoActual.duration();

    if (typeof posicion === "number" && duracion > 0) {
        barraProgreso.value = (posicion / duracion) * 100;
        tiempoActual.textContent = convertirTiempo(posicion);
        tiempoTotal.textContent = convertirTiempo(duracion);
    }

    if (sonidoActual === sonido && sonidoActual.playing()) {
        animacionProgreso = requestAnimationFrame(actualizarProgreso);
    } else {
        animacionProgreso = null;
    }
}

function convertirTiempo(segundos) {

    if (!segundos || isNaN(segundos)) {
        return "0:00";
    }

    const minutos = Math.floor(segundos / 60);

    let segundosRestantes = Math.floor(segundos % 60);

    if (segundosRestantes < 10) {
        segundosRestantes = "0" + segundosRestantes;
    }
    return minutos + ":" + segundosRestantes;
}

graves.addEventListener("input", function () {

    valorGraves.textContent = graves.value + " dB";

    if (filtroGraves) {
        filtroGraves.gain.value = Number(graves.value);
    }
});

medios.addEventListener("input", function () {

    valorMedios.textContent = medios.value + " dB";

    if (filtroMedios) {
        filtroMedios.gain.value = Number(medios.value);
    }
});

agudos.addEventListener("input", function () {

    valorAgudos.textContent = agudos.value + " dB";

    if (filtroAgudos) {
        filtroAgudos.gain.value = Number(agudos.value);
    }
});

btnRestablecer.addEventListener("click", function () {
    graves.value = 0;
    medios.value = 0;
    agudos.value = 0;

    valorGraves.textContent = "0 dB";
    valorMedios.textContent = "0 dB";
    valorAgudos.textContent = "0 dB";

    if (filtroGraves) {
        filtroGraves.gain.value = 0;
    }

    if (filtroMedios) {
        filtroMedios.gain.value = 0;
    }

    if (filtroAgudos) {
        filtroAgudos.gain.value = 0;
    }
});

function crearVisualizador() {

    visualizador.innerHTML = "";

    for (let i = 0; i < 30; i++) {

        const barra = document.createElement("div");
        barra.classList.add("barraEcualizador");
        barra.style.height = "5%";
        visualizador.appendChild(barra);
    }
}

function animarEcualizador() {

    const barras = document.querySelectorAll(".barraEcualizador");

    if (analizador && datosFrecuencia && reproduciendo) {
        analizador.getByteFrequencyData(datosFrecuencia);

        const cantidadBarras = barras.length;

        const frecuenciaMaxima = 12000;
        const frecuenciaMinima = 40;
        const frecuenciaPorDato =
            Howler.ctx.sampleRate / analizador.fftSize;

        barras.forEach(function (barra, indice) {

            const proporcion = indice / cantidadBarras;

            const frecuenciaInicio =
                frecuenciaMinima *
                Math.pow(
                    frecuenciaMaxima / frecuenciaMinima,
                    proporcion
                );

            const frecuenciaFin =
                frecuenciaMinima *
                Math.pow(
                    frecuenciaMaxima / frecuenciaMinima,
                    (indice + 1) / cantidadBarras
                );

            let inicio = Math.floor(
                frecuenciaInicio / frecuenciaPorDato
            );

            let fin = Math.ceil(
                frecuenciaFin / frecuenciaPorDato
            );

            inicio = Math.max(0, inicio);
            fin = Math.min(datosFrecuencia.length, fin);

            let suma = 0;
            let cantidad = 0;

            for (let j = inicio; j < fin; j++) {
                suma += datosFrecuencia[j];
                cantidad++;
            }

            const promedio =
                cantidad > 0 ? suma / cantidad : 0;

            const altura = Math.max(
                5,
                Math.min(100, (promedio / 255) * 130)
            );
            barra.style.height = altura + "%";
        });
    } else {
        barras.forEach(function (barra) {
            barra.style.height = "5%";
        });
    }
    animacionVisualizador =
        requestAnimationFrame(animarEcualizador);
}

async function cargarCatalogo() {

    let datos;
    try {
        const respuesta = await fetch("./api/canciones.php");

        if (respuesta.status === 401) {
            mostrarEstadoVacio("Tu sesión ha caducado. Vuelve a entrar.");
            return;
        }
        if (!respuesta.ok) {
            throw new Error("HTTP " + respuesta.status);
        }
        datos = await respuesta.json();
        if (!Array.isArray(datos)) {
            throw new Error("Formato no válido");
        }
    } catch (error) {
        console.error("No se pudo cargar el catálogo:", error);
        mostrarEstadoVacio("No se pudo cargar el catálogo.");
        return;
    }

    if (datos.length === 0) {
        mostrarEstadoVacio("Todavía no hay canciones.");
        return;
    }

    canciones = datos.map(adaptarCancion);
    cancionActual = 0;
    crearPlaylist();
    cargarCancion(cancionActual);
}

crearVisualizador();
animarEcualizador();
actualizarIconoVolumen();
cargarCatalogo();