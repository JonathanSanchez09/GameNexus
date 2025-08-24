import pandas as pd
from sklearn.neighbors import NearestNeighbors
import mysql.connector

# Configura tus datos de conexión MySQL
conexion = mysql.connector.connect(
    host="db",
    user="root",
    password="rootpass",  # Cambia aquí tu contraseña
    database="tienda_videojuegos"
)

cursor = conexion.cursor()

# Leer reseñas de la base de datos
query = "SELECT usuario_id, juego_id, calificacion FROM resenas WHERE calificacion IS NOT NULL"
cursor.execute(query)
datos = cursor.fetchall()

# Convertir a DataFrame
df = pd.DataFrame(datos, columns=['usuario', 'juego', 'calificacion'])

# Limpieza y preparación
df['usuario'] = df['usuario'].astype(int)
df['juego'] = df['juego'].astype(int)
df['calificacion'] = df['calificacion'].astype(float)

# Promediar calificaciones múltiples por usuario y juego
df = df.groupby(['usuario', 'juego'], as_index=False).mean()
df = df.drop_duplicates(subset=['usuario', 'juego'])

# Crear matriz usuario-juego
matriz = df.pivot(index='usuario', columns='juego', values='calificacion').fillna(0)

n_usuarios = len(matriz)
if n_usuarios < 2:
    print("No hay suficientes usuarios para generar recomendaciones.")
    exit()

# Entrenar modelo KNN
modelo = NearestNeighbors(metric='cosine', algorithm='brute')
modelo.fit(matriz)

# Crear tabla recomendaciones si no existe
cursor.execute("""
    CREATE TABLE IF NOT EXISTS recomendaciones (
        id_usuario INT NOT NULL,
        id_juego INT NOT NULL,
        PRIMARY KEY (id_usuario, id_juego)
    )
""")

# Limpiar recomendaciones previas
cursor.execute("DELETE FROM recomendaciones")

# Generar recomendaciones para cada usuario
for usuario_id in matriz.index:
    distancias, indices = modelo.kneighbors([matriz.loc[usuario_id]], n_neighbors=min(3, n_usuarios))
    vecinos = indices[0][1:]  # Ignorar el propio usuario

    juegos_usuario = set(df[df['usuario'] == usuario_id]['juego'])
    recomendaciones = set()

    for vecino_idx in vecinos:
        vecino_id = matriz.index[vecino_idx]
        juegos_vecino = set(df[df['usuario'] == vecino_id]['juego'])
        recomendaciones.update(juegos_vecino - juegos_usuario)

    # Insertar en la tabla
    for juego_id in recomendaciones:
        cursor.execute(
            "INSERT INTO recomendaciones (id_usuario, id_juego) VALUES (%s, %s)",
            (usuario_id, juego_id)
        )

conexion.commit()
conexion.close()
print("Recomendaciones generadas correctamente.")
