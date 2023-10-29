<p align="center"><a href="https://www.linkedin.com/company/monoma-technology/" target="_blank"><img src="./public/logo-white.png" width="400" alt="Laravel Logo"></a></p>


## Desafio de codigo Back-end developer

La prueba consiste en desarrollar una API Rest con 4 endpoints.

## Cosas a tener en cuenta sobre esta implementacion
- Algunas convenciones de laravel no fueron implementadas ya que trate de seguir al maximo las reglas requeridas en el desafio (ejemplo: el estandar de los nombres de rutas y manejo de errores)
- Realicé dos implementaciones del codigo en distintas ramas, una de ellas (`main branch`) la cual posee la implementacion de `REDIS` y la rama `without_redis` la cual no posee dicha implementacion en caso de que no tenga instalado `REDIS` en su maquina
- Los tests para la rama `main` que tienen implementado Redis solo dan verde para todos los test si posee redis instalado
- Si utiliza la rama `without_redis` debe obligatoriamente utilizar el `.env.example` de esa ramma (SESSION_DRIVER=file)
- Entre las variables de entorno `./env.example` se encuentra una llamada `TOKEN_EXPIRATION_TIME_MIN` la cual permite configurar cuantos `minutos` persiste un token, manipule este valor para comprobar que la expiracion de token realmente funciona (recuerde correr los commandos `php artisan config:clear` despues de cambiar cada valor de la variable)
- El seeder requerido para la creacion de 2 usuarios con roles diferentes es `database/seeders/DatabaseSeeder.php`
- Configure su archivo `phpunit.xml` para comenzar a correr los `test`
- Toda la API fue realizadA utilizando `TDD`
- Puede probar la API en el workspace de postman https://app.getpostman.com/join-team?invite_code=c8e1daa2f1d3dbc968cc8de3353da97f&target_code=b2cb4865dca5bed1b7f23de30eff54ea

## Utilidades

- Documentacion de Redis para tu stack de desarrollo https://redis.io/docs/install/install-stack/
- Comando para inicar servidor de redis: `redis-stack-server`

## Pasos a seguir para montar el proyecto

- `cd test_backend`
- `composer install`
- copiar contenido de `./env.example` en un nuevo archivo llamado `./env`
- rellenar las variables relacionadas a la base de datos en `.env` file
- prender servicio de `mysql`
- `php artisan generate:key`
- `php artisan migrate --seed`



