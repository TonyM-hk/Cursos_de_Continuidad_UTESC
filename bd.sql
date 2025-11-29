estructura:

carreras:
pk_carrera int
nom_carrera varchar
estatus int

instructores:
pk_instructor int
nombres varchar
apaterno varchar
amaterno varchar
estatus int
cv varchar (este es para subir archivos)

cursos:
pk_curso int
nom_curso varchar
objetivo text
fk_instructor int
fk_carrera int
estatus int
cronograma varchar (este es para subir archivos)
