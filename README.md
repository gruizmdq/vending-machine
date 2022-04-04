# Vending Machine

## Aclaraciones
Antes que nada, tengo que ser honesto y decir que no estoy para nada conforme con la solución que realicé. Tuve varios inconvenientes no técnicos a la hora de encarar el ejercicio. El principal de todos fue que no disponía de ordenador, debido a que antes de venir a Europa el mío se rompió.
Pude conseguir que me presten uno, pero el hardware no era de lo más cómodo para trabajar, sumado también que tuve que trabajar con otro sistema operativo.
No estoy poniendo excusas, solo para aclarar que fue bastante tedioso en algunos momentos y en algunas features tuve que resignar la mejor solución.

## Consideraciones
Hay varios aspectos a considerar.
En esta versión, por los problemas ya mencionados, no pude realizar la API de la mejor manera. 
- Faltantes:
    - Error Handling: No se realiza un correcto manejo de cómo trabajar y capturar errores en los distintos procesos. No se crearon clases genéricas para darle robustez al código.
    - Seguridad: No se realiza ningún tipo de verificación y autenticación de usuario. Se tendría que haber implementado algún sistema como podría ser OAuth y jwt
    - Docker: considero que la solución prevista no es la mejor. Debería haber realizado los "seed" de la base de datos automáticamente al hacer el set up del contenedor.
    - Responses: No se trató de manera genérica las responses de cada método. Se debería haber creado una clase DefaultResponse que cree los json de manera más robusta.
    - Tipados: no se realizó control de tipado estático en todos los casos.
    - Swagger/OpenApi
    - Test: no se implementaron testde integración.
    
## Requerimientos
Tener docker instalado y corriendo.

## SetUp
El setup es simple utilizando las ventajas que brinda laravel a partir de la versión 8.
```sh
bash
./vendor/bin/sail up
```
Luego, realizar los seeds de la base de datos
```sh
php artisan db:seed
```

## Endpoints

| Método | Url | Descripción | Requiere Autorización?
| ------ | ------ | ------ | ------ | 
| GET | /api/coin | Retorna la cantidad de monedas | No
| POST | /api/coin | Incrementa los créditos de acuerdo a la moneda insertada. Si no es válida, la devuelve | No
| DELETE | /api/coin | Devuelve todas las monedas insertadas que no hayan sido utilizado previamente para adquirir un producto. | No
| POST | /order | Devuelve el producto elegido y el cambio correspondiente | No
| POST | /service | Setea la cantidad de monedas y los productos | Sí


### GET /api/coin
- 200
```json
[
    {
        "value": 0.05,
        "qty": 5
    },
    {
        "value": 0.1,
        "qty": 5
    },
    {
        "value": 0.25,
        "qty": 5
    },
    {
        "value": 1,
        "qty": 5
    }
]
```
### POST /api/coin
Request:
| Parámetro | Opcional | Tipo | 
| ------ | ------ | ------ | 
| value | No | float | 

Response:
- 200
``` 1```
- 400
```"Coin not accepted, coin was returned."```
Se introdujo una moneda con vaslor no válido.
- 500
```"There was an error."```
Error genérico.

### DELETE /api/coin
- 200
```json
[
    {
        "value": 1
    },
    {
        "value": 1
    },
    {
        "value": 0.25
    }
]
```
- 500
```"There was an error."```
Error genérico.

### PUT /api/order
Request:
| Parámetro | Opcional | Tipo | 
| ------ | ------ | ------ | 
| item | No | String | No

Response:
- 200
```json
{
    "item": "SODA",
    "change": [
        0.25,
        0.25
    ]
}
```
- 400
```"You must select a product."```
No se envió el ítem que se desea comprar

- 404
 ```"Product not found."```
No se encontró el código del item seleccionado.

- 500
```Sorry, there is no more ${item code}```
El stock del ítem seleccionado es 0.

- 500
```Please insert more coins.```
Saldo insuficiente.

### POST /api/service
Request:
| Parámetro | Opcional | Tipo | 
| ------ | ------ | ------ | 
| items | Si | Arreglo | 
| items.code | No | string | 
| items.qty | No | int | 
| items.price | Si | float | 
| coins | Si | Arreglo | 
| coins.value | No | float | 
| coins.qty | No | int | 

Si el item.code existe, se actualiza el stock. Si item.price se manda en la request, se actualiza también el precio.
Si el item.code no existe, lo crea.

```json
{
    "items": [
        {
            "code": "Milanesa",
            "qty": 5,
            "price": 1.5 
        }
    ],
    "coins": [
        {
            "value": 0.1,
            "qty": 5
        },
        {
            "value": 1,
            "qty": 5
        },
        {
            "value": 0.05,
            "qty": 5
        },
        {
            "value": 0.25,
            "qty": 5
        }
    ]
}
```

Response:
- 200
 ```1 ```
- 500
```There was an error with coins."```
Hubo un error al agregar las monedas a la base de datos.

Este método me planteó algunas dudas sobre cómo está resuelto. Preferí dejar la performance de lado e insertar tantas monedas individualmente como indique el administrador. Se podría haber diseñado de manera diferente para que exista un registro con la cantidad de monedas de cada tipo existente, pero me pareció más correcto trabajar cada moneda de manera individual, sobretodo porque se puede "suponer" que una máquina expendedora no va a tener millones de monedas.


