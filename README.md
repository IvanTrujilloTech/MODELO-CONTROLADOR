NOTAS:

1. He reaprovechado el mismo repostorio para el proyecto de Laravel, haciendo simplemente un commit nuevo, si necesito en algun momento el proyecto mvc en php anterior a la migración a Laravel simplemente haré un rollback.

2. En la carpeta migrations podeis ver las migraciones a Laravel de mis archivos de la base de datos. Tienen un nombre raro pero deben tenerlo, he utilizado un comando que es `php artisan make:migration create_transactions_table` y este crea de forma automatica las migraciones a laravel añadiendo delante el formato de fecha/hora. No sabía si había algun tipo de parametro que pudiera añadir al comando para que no apareciera esa nomenclatura pero ha funcionado y me es suficiente, sinceramente.
