# 🧠 Generación de Actividades Educativas con LLMs

Este proyecto tiene como objetivo desarrollar una aplicación web que utiliza **modelos de lenguaje (LLMs)** para ayudar tanto a añumnos como profesores en asignaturas de programación como *Fundamentos de Ingeniería del Software*, *Programación Orientada a Objetos* o *Sistemas Concurrentes y Distribuidos*.

El chabot se basa en los objetivos y competencias de las guías docentes y están pensadas para adaptarse a las necesidades de los usuarios. La aplicación permite la interacción de estudiantes y profesorado, la gestión académica (usuarios, clases, asignaturas) y el registro de todas las interacciones para análisis pedagógico posterior.

Además, el sistema incorpora un componente de **web scraping** para obtener recursos educativos complementarios, y se ejecuta mediante **contenedores Docker** que facilitan su despliegue.

---

## 📁 Estructura del Proyecto

### `app/`
Contiene todos los archivos PHP necesarios para la ejecución de la aplicación web:

- Controladores para manejar las solicitudes del usuario.
- Vistas y formularios para interacción con el usuario.
- Lógica de conexión y consultas a la base de datos.
- Comunicación con el modelo LLM para generar las actividades.

Este directorio es el núcleo de la aplicación y donde se gestiona todo el flujo de la web educativa.

📂 Ver contenido: [`app/`](./app/)

---

### `webscraping/`
Contiene los scripts encargados de realizar el scraping de contenidos educativos desde páginas seleccionadas. Estos recursos pueden alimentar al modelo o ser utilizados como base de conocimiento.

Incluye:

- Scripts de scraping automatizado.
- Contenedor Docker para su ejecución programada.

📂 Ver contenido: [`webscraping/`](./webscraping/)

---

## ⚙️ Archivos Principales

- **`Dockerfile`**  
  Define la imagen base y las dependencias necesarias para ejecutar la aplicación y sus servicios.

- **`docker-compose.yml`**  
  Orquesta los servicios del sistema, incluyendo la aplicación web, el scraping, la base de datos y cualquier otro componente (como Ollama para el modelo LLM).

---

## 🚀 Tecnologías Utilizadas

- **PHP** – Lógica del backend de la aplicación.
- **HTML & CSS** – Diseño de la interfaz.
- **Javascript** – Comunicaciones con el chatbot sin necesidad de recargar.
- **MySQL** – Base de datos para usuarios, clases, interacciones y actividades generadas.
- **Docker & Docker Compose** – Contenerización de la aplicación y servicios.
- **API OpenAI** – Motor de generación de actividades mediante modelos LLM locales.
- **Python** – Scripts de scraping educativo.

---

## ✅ Funcionalidades Principales

- Generación automática de ejercicios según los objetivos del curso.
- Resolución de dudas de cnceptos y en tiempo real sobre actividades.
- Gestión de usuarios, clases y asignaturas.
- Expansión automática del conocimiento mediante scraping.
- Despliegue rápido y reproducible mediante Docker.

---
