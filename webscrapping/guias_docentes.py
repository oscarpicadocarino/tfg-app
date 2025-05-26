from bs4 import BeautifulSoup
import requests
import mysql.connector 

# Lista de asignaturas con nombre y URL
ASIGNATURAS = [
    {
        "nombre": "Programación y Diseño Orientado a Objetos",
        "url": "https://lsi.ugr.es/docencia/grados/grado-ingenieria-informatica/programacion-y-diseno-orientado-objetos/guia-docente"
    },
    {
        "nombre": "Fundamentos de Ingeniería del Software",
        "url": "https://www.ugr.es/estudiantes/grados/grado-ingenieria-informatica/fundamentos-ingenieria-software/guia-docente"
    },
    {
        "nombre": "Sistemas Concurrentes y Distribuidos",
        "url": "https://www.ugr.es/estudiantes/grados/grado-ingenieria-informatica/sistemas-concurrentes-distribuidos/guia-docente"
    }
]

def extraer_seccion(soup, titulo):
    secciones = soup.find_all("h2")
    for i, sec in enumerate(secciones):
        if titulo.lower() in sec.text.lower():
            contenido = []
            siguiente = sec.find_next_sibling()
            while siguiente and siguiente.name != 'h2':
                contenido.append(siguiente.text.strip())
                siguiente = siguiente.find_next_sibling()
            return "\n".join(contenido).strip()
    return ""


def conectar_db():
    return mysql.connector.connect(
        host="db",
        user="usuario",
        password="password",
        database="tfg_app_db"
    )

def obtener_asignatura_id(cursor, nombre):
    cursor.execute("SELECT id_asignatura FROM asignaturas WHERE nombre = %s", (nombre,))
    resultado = cursor.fetchone()
    return resultado[0] if resultado else None

def insertar_guia(cursor, asignatura_id, descripcion, competencias, resultados, programa_teorico, programa_practico):
    query = """
        INSERT INTO guias_docentes (
            asignatura_id,
            descripcion,
            competencias,
            resultados_aprendizaje,
            programa_teorico,
            programa_practico
        ) VALUES (%s, %s, %s, %s, %s, %s)
        ON DUPLICATE KEY UPDATE
            descripcion = VALUES(descripcion),
            competencias = VALUES(competencias),
            resultados_aprendizaje = VALUES(resultados_aprendizaje),
            programa_teorico = VALUES(programa_teorico),
            programa_practico = VALUES(programa_practico)
    """
    cursor.execute(query, (
        asignatura_id,
        descripcion,
        competencias,
        resultados,
        programa_teorico,
        programa_practico
    ))


def procesar_asignatura(cursor, nombre, url):
    print(f"\nProcesando: {nombre}")
    response = requests.get(url)
    soup = BeautifulSoup(response.text, "html.parser")

    competencias = extraer_seccion(soup, "Competencias")
    resultados = extraer_seccion(soup, "Resultados de aprendizaje")
    programa = extraer_seccion(soup, "Programa de contenidos Teóricos y Prácticos")

    # Separar programa teórico y práctico si es posible
    programa_teorico, programa_practico = "", ""
    if "Práctico" in programa:
        partes = programa.split("Práctico")
        programa_teorico = partes[0].strip()
        programa_practico = "Práctico" + partes[1].strip()
    else:
        programa_teorico = programa

    # Buscar id de la asignatura
    asignatura_id = obtener_asignatura_id(cursor, nombre)
    if not asignatura_id:
        print(f"[✗] La asignatura '{nombre}' no existe en la base de datos.")
        return

    descripcion = f"Guía docente de la asignatura {nombre}."  # Opcional: puedes scrapearla

    insertar_guia(cursor, asignatura_id, descripcion, competencias, resultados, programa_teorico, programa_practico)
    print(f"[✓] Guía insertada para '{nombre}'.")

def main():
    conn = conectar_db()
    cursor = conn.cursor()

    for asignatura in ASIGNATURAS:
        procesar_asignatura(cursor, asignatura["nombre"], asignatura["url"])

    conn.commit()
    cursor.close()
    conn.close()
    print("\n🚀 Proceso completado.")

if __name__ == "__main__":
    main()
