#!/bin/sh

HOST="db"
PORT="3306"

echo "⏳ Esperando a que MySQL esté disponible en $HOST:$PORT..."

while ! nc -z "$HOST" "$PORT"; do
  sleep 1
done

echo "✅ MySQL está listo en $HOST:$PORT — continuando..."

exec python3 guias_docentes.py
