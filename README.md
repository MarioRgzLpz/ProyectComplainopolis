# PROYECTO DE LA ASIGNATURA TECNOLOGIAS WEB DE LA ESPECIALIDAD TI (UGR)
## USO: 
Es necesario tener instalado docker y docker-compose para el manejo de varios contenedores.
Para poder visualizar la pagina web debemos descargar el codigo y usar el siguiente comando en el lugar donde lo tengamos:
```bash
docker-compose up
```
Tras esto tendremos 3 contenedores activos uno para el servidor que aloja la pagina web y nos permite cargar el codigo php, otro para phpmyadmin que nos permite manejar la base de datos y otro con la base de datos de mysql.

Para que cargue la pagina es necesario acceder a phpmyadmin en la direccion http:localhost:8081 y acceder usando el usuario root, contraseña root. Tras esto vemos que esta la base de datos complainopolis donde deberemos importar el archivo complainopolis.sql para cargar las tablas de la base de datos.

Con esto ya podemos acceder a la direccion de la pagina web: http:localhost:8080 en la cual tendremos la pagina de inicio. Podemos iniciar sesion con el usuario eladmin y contraseña santiagomario123 que tiene el rol de administrador.
