FROM python:3.11-slim

WORKDIR /app

COPY . /app

# Instala netcat y las dependencias de Python
RUN apt-get update && apt-get install -y netcat-openbsd && \
    pip install requests beautifulsoup4 mysql-connector-python && \
    chmod +x wait-for-db.sh

# Comando de inicio
CMD ["./wait-for-db.sh", "db", "3306", "python", "guias_docentes.py"]
