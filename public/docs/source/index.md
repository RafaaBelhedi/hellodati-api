---
title: API Reference

language_tabs:
- bash
- javascript

includes:

search: true

toc_footers:
- <a href='http://github.com/mpociot/documentarian'>Documentation Powered by Documentarian</a>
---
<!-- START_INFO -->
# Info

Welcome to the generated API reference.

<!-- END_INFO -->

#Authentification

APIs for managing authentification
<!-- START_44652685eefa8022f19b92a3ce78d990 -->
## Login
login user and create access token

> Example request:

```bash
curl -X POST "http://apitndati.com/v001/public/auth/login" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}" \
    -d '{"email":"KwVDqm3LYcYGXima","password":"x1h9wkkyAQSuurcx","client_id":"m8wCJsXCjwQnoKpr","client_secret":"OL4YeMbOPwAgR7V5","device_imei":"G1AD0K5C4GkckOvE"}'

```

```javascript
const url = new URL("http://apitndati.com/v001/public/auth/login");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

let body = {
    "email": "KwVDqm3LYcYGXima",
    "password": "x1h9wkkyAQSuurcx",
    "client_id": "m8wCJsXCjwQnoKpr",
    "client_secret": "OL4YeMbOPwAgR7V5",
    "device_imei": "G1AD0K5C4GkckOvE"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response (200):

```json
{
    "token_type": "Bearer",
    "expires_in": "Integer",
    "access_token": "String",
    "refresh_token": "String"
}
```

### HTTP Request
`POST auth/login`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    email | string |  required  | The e-mail of the user.
    password | string |  required  | The password of the user.
    client_id | string |  required  | The id of used platform.
    client_secret | string |  required  | The secret pass code of used platform.
    device_imei | string |  optional  | optional Only if the user is not an admin.

<!-- END_44652685eefa8022f19b92a3ce78d990 -->

<!-- START_a4c0709f3df307737a90fa96aa40fa31 -->
## Refresh
Extend the duration of authentification sessions

> Example request:

```bash
curl -X POST "http://apitndati.com/v001/public/auth/refresh" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}" \
    -d '{"refresh_token":"o2CzZbnP0pPGXVIM","client_id":"OgeS2Q31H0zKptl9","client_secret":"QkLC6A2I8KcFlWPW"}'

```

```javascript
const url = new URL("http://apitndati.com/v001/public/auth/refresh");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

let body = {
    "refresh_token": "o2CzZbnP0pPGXVIM",
    "client_id": "OgeS2Q31H0zKptl9",
    "client_secret": "QkLC6A2I8KcFlWPW"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response (200):

```json
{
    "token_type": "Bearer",
    "expires_in": "Integer",
    "access_token": "String",
    "refresh_token": "String"
}
```

### HTTP Request
`POST auth/refresh`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    refresh_token | string |  required  | The refresh token provided when logged-in.
    client_id | string |  required  | The id of used platform.
    client_secret | string |  required  | The secret pass code of used platform.

<!-- END_a4c0709f3df307737a90fa96aa40fa31 -->

<!-- START_7ac06ca44d2f051c325ec79032d7ccad -->
## Logout
Revoke the access token

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/auth/logout" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/auth/logout");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response (200):

```json
{
    "message": "Successfully logged out"
}
```

### HTTP Request
`GET auth/logout`


<!-- END_7ac06ca44d2f051c325ec79032d7ccad -->

<!-- START_b25bea9b921111447db41e83ca0f4564 -->
## Account
Get the authenticated User

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/auth/user" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/auth/user");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response (200):

```json
{
    "id": "integer",
    "name": "string",
    "email": "string",
    "approved": "integer",
    "role": "integer",
    "created_at": "Timestamp",
    "updated_at": "Timestamp"
}
```

### HTTP Request
`GET auth/user`


<!-- END_b25bea9b921111447db41e83ca0f4564 -->

<!-- START_961fec832b62cbba5d4c04d71169b36b -->
## Privileges
Get the privileges of the authenticated User

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/auth/privileges" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/auth/privileges");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response (200):

```json
{
    "user_id": "integer",
    "role": "integer",
    "client_id": "integer",
    "hotel_id": "array",
    "device_imei": "string"
}
```

### HTTP Request
`GET auth/privileges`


<!-- END_961fec832b62cbba5d4c04d71169b36b -->

#Device

APIs for managing device
<!-- START_5cf5d85b4b7fc15783f184f7d2456f84 -->
## Columns
Display the possible fields of Device.

These fields can also be used to filter the search.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/device/columns" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/device/columns");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET device/columns`


<!-- END_5cf5d85b4b7fc15783f184f7d2456f84 -->

<!-- START_2ab314387cb0d975e7259ee2ca28cc47 -->
## Index
Display a listing of device.

To filter devices, add any of the Device object properties to the querry ?{property}={value}

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/devices" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/devices");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET devices`


<!-- END_2ab314387cb0d975e7259ee2ca28cc47 -->

<!-- START_b63ce50912495dff099f0a94cb25611b -->
## Contacts
Display a listing of rooms and relative phone number within the same hotel.

To filter devices, add any of the Device object properties to the querry ?{property}={value}

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/devices/contacts" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/devices/contacts");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET devices/contacts`


<!-- END_b63ce50912495dff099f0a94cb25611b -->

<!-- START_9bf55187824ff92cc42f1be52e862ab6 -->
## Show
Display the specified App by {id}.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/device/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/device/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET device/{id}`


<!-- END_9bf55187824ff92cc42f1be52e862ab6 -->

<!-- START_97c416ac9711e2e82970c65041a354f0 -->
## Store
Create a new Device.

> Example request:

```bash
curl -X POST "http://apitndati.com/v001/public/device" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/device");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`POST device`


<!-- END_97c416ac9711e2e82970c65041a354f0 -->

<!-- START_f3a4c9a4aa5a85de90ccb563c5ee91a5 -->
## Update
Edit properties of existing Device.

> Example request:

```bash
curl -X PUT "http://apitndati.com/v001/public/device/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/device/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`PUT device/{id}`


<!-- END_f3a4c9a4aa5a85de90ccb563c5ee91a5 -->

<!-- START_1f2958855b6c9625ba7752984661c391 -->
## Link to room
Switch the room to where a device is linked.

> Example request:

```bash
curl -X PUT "http://apitndati.com/v001/public/deviceRoomSwitch/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}" \
    -d '{"room_id":7}'

```

```javascript
const url = new URL("http://apitndati.com/v001/public/deviceRoomSwitch/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

let body = {
    "room_id": 7
}

fetch(url, {
    method: "PUT",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`PUT deviceRoomSwitch/{id}`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    room_id | integer |  optional  | If 'room_id' equales <b>Null</b> or <b>0</b> or if 'room_id' does not belong to same Hotel as the device, the result will be a device with no link to any room..

<!-- END_1f2958855b6c9625ba7752984661c391 -->

<!-- START_43db6117efdd1c5ef78d27b19cf18992 -->
## Link to tourist
Switch the tourist to where a device is linked.

> Example request:

```bash
curl -X PUT "http://apitndati.com/v001/public/deviceTouristSwitch/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}" \
    -d '{"tourist_id":2}'

```

```javascript
const url = new URL("http://apitndati.com/v001/public/deviceTouristSwitch/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

let body = {
    "tourist_id": 2
}

fetch(url, {
    method: "PUT",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`PUT deviceTouristSwitch/{id}`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    tourist_id | integer |  optional  | If 'tourist_id' equales <b>Null</b> or <b>0</b> or if 'tourist_id' does not belong to same Hotel as the device, the result will be a device with no link to any tourist..

<!-- END_43db6117efdd1c5ef78d27b19cf18992 -->

<!-- START_98df8edf5efda6f14eef2f72ae7d1a92 -->
## Destroy
Remove an Device.

> Example request:

```bash
curl -X DELETE "http://apitndati.com/v001/public/device/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/device/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`DELETE device/{id}`


<!-- END_98df8edf5efda6f14eef2f72ae7d1a92 -->

#DeviceRooms

APIs for managing relation between devices and rooms
<!-- START_ddc1ff1f45d0c35bf6c62b2efcea5fe5 -->
## Columns
Display the possible fields of DeviceRoom.

These fields can also be used to filter the search.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/device_room/columns" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/device_room/columns");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET device_room/columns`


<!-- END_ddc1ff1f45d0c35bf6c62b2efcea5fe5 -->

<!-- START_75acf7ed8441c00a77aa840d7ffb0d22 -->
## Index
Display a listing of device-room relations.

To filter device rooms, add any of the DeviceRoom object properties to the querry ?{property}={value}

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/device_rooms" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/device_rooms");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET device_rooms`


<!-- END_75acf7ed8441c00a77aa840d7ffb0d22 -->

<!-- START_21244281a0fbce1bd03713c7fe89a950 -->
## Show
Display the specified App by {id}.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/device_room/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/device_room/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET device_room/{id}`


<!-- END_21244281a0fbce1bd03713c7fe89a950 -->

<!-- START_bffebdfb88a1113ce01902ca7233df16 -->
## Store
Create a new DeviceRoom.

> Example request:

```bash
curl -X POST "http://apitndati.com/v001/public/device_room" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/device_room");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`POST device_room`


<!-- END_bffebdfb88a1113ce01902ca7233df16 -->

<!-- START_0e6b324f669dae48a8fae4de3edcc7cb -->
## Destroy
Remove an DeviceRoom.

> Example request:

```bash
curl -X DELETE "http://apitndati.com/v001/public/device_room/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/device_room/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`DELETE device_room/{id}`


<!-- END_0e6b324f669dae48a8fae4de3edcc7cb -->

#ExtraPosts

APIs for managing posts outside hotels
<!-- START_bffb805bf187be7bf3ed42c422c0b720 -->
## Columns
Display the possible fields of ExtraPost.

These fields can also be used to filter the search.

* <b>Please note: </b> the property 'post_type' refers to the template
used for the ExtraPost, which can override a ExtraPost.property's default
value or force it to be required.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/extra_post/columns" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/extra_post/columns");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET extra_post/columns`


<!-- END_bffb805bf187be7bf3ed42c422c0b720 -->

<!-- START_8b4b92797326391c102d77c9b9f8780f -->
## Index
Display a listing Extra posts.

To filter Extra posts, add any of the ExtraPost object properties to the querry ?{property}={value}

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/extra_posts" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/extra_posts");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET extra_posts`


<!-- END_8b4b92797326391c102d77c9b9f8780f -->

<!-- START_bf17e1c6c0327e71c2b2e50b413bcb26 -->
## Show
Display the specified ExtraPost by {id}.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/extra_post/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/extra_post/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET extra_post/{id}`


<!-- END_bf17e1c6c0327e71c2b2e50b413bcb26 -->

<!-- START_c0c93c0c25048b997aed57f6591183c9 -->
## extra_post
> Example request:

```bash
curl -X POST "http://apitndati.com/v001/public/extra_post" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/extra_post");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`POST extra_post`


<!-- END_c0c93c0c25048b997aed57f6591183c9 -->

<!-- START_969eab7c4313eeb33eebf0420be56358 -->
## Update
Edit properties of existing ExtraPost.

> Example request:

```bash
curl -X PUT "http://apitndati.com/v001/public/extra_post/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/extra_post/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`PUT extra_post/{id}`


<!-- END_969eab7c4313eeb33eebf0420be56358 -->

<!-- START_f7b655802534aa7afaf137548a90a3fd -->
## Multi-Update
Edit properties of multiple existing ExtraPost.

> Example request:

```bash
curl -X PUT "http://apitndati.com/v001/public/extra_posts" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/extra_posts");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`PUT extra_posts`


<!-- END_f7b655802534aa7afaf137548a90a3fd -->

<!-- START_e1f85f1a9ee68781288f7a02be6bf8e2 -->
## Destroy
Remove an ExtraPost.

> Example request:

```bash
curl -X DELETE "http://apitndati.com/v001/public/extra_post/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/extra_post/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`DELETE extra_post/{id}`


<!-- END_e1f85f1a9ee68781288f7a02be6bf8e2 -->

<!-- START_9cd23f192a5e5db529fd1772f10b39da -->
## Seen
Increment the property &#039;nbr_views&#039; of an ExtraPost by 1.

> Example request:

```bash
curl -X PUT "http://apitndati.com/v001/public/extra_post/{id}/seen" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/extra_post/{id}/seen");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`PUT extra_post/{id}/seen`


<!-- END_9cd23f192a5e5db529fd1772f10b39da -->

<!-- START_df9c5572639e4a4b1b0f20b7de8cec45 -->
## Click
Increment the property &#039;nbr_clicks&#039; of an ExtraPost by 1.

> Example request:

```bash
curl -X PUT "http://apitndati.com/v001/public/extra_post/{id}/click" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/extra_post/{id}/click");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`PUT extra_post/{id}/click`


<!-- END_df9c5572639e4a4b1b0f20b7de8cec45 -->

#ExtraPostsTranslates

APIs for managing translations of posts outside hotels
<!-- START_e44fde9a7d7c7b1ef4b556ffb6fc0160 -->
## Columns
Display the possible fields of ExtraPostsTranslate.

These fields can also be used to filter the search.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/extra_posts_translate/columns" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/extra_posts_translate/columns");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET extra_posts_translate/columns`


<!-- END_e44fde9a7d7c7b1ef4b556ffb6fc0160 -->

<!-- START_3e8ea535649c9f4e0e8a6054589e050d -->
## Index
Display a listing of Extra posts translates.

To filter Extra posts translates, add any of the ExtraPostTranslate object properties to the querry ?{property}={value}

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/extra_posts_translates" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/extra_posts_translates");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET extra_posts_translates`


<!-- END_3e8ea535649c9f4e0e8a6054589e050d -->

<!-- START_6ce31ab7171e3c6f969e42250115479f -->
## Show
Display the specified App by {id}.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/extra_posts_translate/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/extra_posts_translate/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET extra_posts_translate/{id}`


<!-- END_6ce31ab7171e3c6f969e42250115479f -->

<!-- START_be58c5a43cd258a470342e5936ce4256 -->
## Store
Create a new ExtraPostsTranslate.

> Example request:

```bash
curl -X POST "http://apitndati.com/v001/public/extra_posts_translate" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/extra_posts_translate");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`POST extra_posts_translate`


<!-- END_be58c5a43cd258a470342e5936ce4256 -->

<!-- START_454c11ecb05964ed5e97eaa75c95cb35 -->
## Update
Edit properties of existing DeviceRoom.

> Example request:

```bash
curl -X PUT "http://apitndati.com/v001/public/extra_posts_translate/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/extra_posts_translate/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`PUT extra_posts_translate/{id}`


<!-- END_454c11ecb05964ed5e97eaa75c95cb35 -->

<!-- START_5edf409c01a6fd8b96106c1d54c8ba74 -->
## Destroy
Remove an DeviceRoom.

> Example request:

```bash
curl -X DELETE "http://apitndati.com/v001/public/extra_posts_translate/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/extra_posts_translate/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`DELETE extra_posts_translate/{id}`


<!-- END_5edf409c01a6fd8b96106c1d54c8ba74 -->

#Hotel

APIs for managing hotels
<!-- START_f588bb22b8ac60d5136714492148ffde -->
## Columns
Display the possible fields of Hotel.

These fields can also be used to filter the search.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/hotel/columns" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/hotel/columns");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET hotel/columns`


<!-- END_f588bb22b8ac60d5136714492148ffde -->

<!-- START_80e4eaeee3688f766803b4bfead371af -->
## Index
Display a listing of hotels.

To filter hotels, add any of the Hotel object properties to the querry ?{property}={value}

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/hotels" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/hotels");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET hotels`


<!-- END_80e4eaeee3688f766803b4bfead371af -->

<!-- START_c1d91ad71e9c5b9c50cde84c5ca5694e -->
## Show
Display the specified App by {id}.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/hotel/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/hotel/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET hotel/{id}`


<!-- END_c1d91ad71e9c5b9c50cde84c5ca5694e -->

<!-- START_7a52b53c24c11eb99e055b5a229b8913 -->
## Store
Create a new Hotel.

> Example request:

```bash
curl -X POST "http://apitndati.com/v001/public/hotel" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/hotel");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`POST hotel`


<!-- END_7a52b53c24c11eb99e055b5a229b8913 -->

<!-- START_77c7d40271296c2682ab701c89beaf96 -->
## Update
Edit properties of existing DeviceRoom.

> Example request:

```bash
curl -X PUT "http://apitndati.com/v001/public/hotel/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/hotel/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`PUT hotel/{id}`


<!-- END_77c7d40271296c2682ab701c89beaf96 -->

<!-- START_f27701eb8ce6d009964298dec5e9d79d -->
## Destroy
Remove an DeviceRoom.

> Example request:

```bash
curl -X DELETE "http://apitndati.com/v001/public/hotel/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/hotel/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`DELETE hotel/{id}`


<!-- END_f27701eb8ce6d009964298dec5e9d79d -->

#Notifications

APIs for managing notifications
<!-- START_d8e850ae4d203ccf3d17ba0c97c43cde -->
## Columns
Display the possible fields of a Notification.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/notif_structure" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/notif_structure");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET notif_structure`


<!-- END_d8e850ae4d203ccf3d17ba0c97c43cde -->

<!-- START_c1e72ead638ee06a29280d5e447a28f5 -->
## Send a notification
Create a notification and send it to target Device(s).

Filtering target devices is exactly the same way as searching for devices using filters.
To filter target devices, add any of the Device object properties to the querry ?{property}={value}

> Example request:

```bash
curl -X POST "http://apitndati.com/v001/public/notif_hotels_devices" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/notif_hotels_devices");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`POST notif_hotels_devices`


<!-- END_c1e72ead638ee06a29280d5e447a28f5 -->

#PostTypes

APIs for managing templates of posts
<!-- START_844ee3b86207b8ce2448e3c66131cfc6 -->
## Columns
Display the possible fields of PostType.

These fields can also be used to filter the search.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/post_type/columns" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/post_type/columns");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET post_type/columns`


<!-- END_844ee3b86207b8ce2448e3c66131cfc6 -->

<!-- START_aa46235c7eb2b887fe3f1439a64d43fb -->
## Index
Display a listing of posts templates.

To filter posts templates, add any of the PostType object properties to the querry ?{property}={value}

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/post_types" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/post_types");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET post_types`


<!-- END_aa46235c7eb2b887fe3f1439a64d43fb -->

<!-- START_a1d8b366fa85e7d880b9a27e4b2103ea -->
## Show
Display the specified App by {id}.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/post_type/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/post_type/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET post_type/{id}`


<!-- END_a1d8b366fa85e7d880b9a27e4b2103ea -->

<!-- START_3251b54a32a2a549abca71b1dcd78b2f -->
## Store
Create a new PostType.

> Example request:

```bash
curl -X POST "http://apitndati.com/v001/public/post_type" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/post_type");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`POST post_type`


<!-- END_3251b54a32a2a549abca71b1dcd78b2f -->

<!-- START_3b44c4109be518601e924274d6882bdf -->
## Update
Edit properties of existing DeviceRoom.

> Example request:

```bash
curl -X PUT "http://apitndati.com/v001/public/post_type/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/post_type/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`PUT post_type/{id}`


<!-- END_3b44c4109be518601e924274d6882bdf -->

<!-- START_f3024d586dbdb680486de290405cc87e -->
## Destroy
Remove an DeviceRoom.

> Example request:

```bash
curl -X DELETE "http://apitndati.com/v001/public/post_type/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/post_type/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`DELETE post_type/{id}`


<!-- END_f3024d586dbdb680486de290405cc87e -->

#Posts

APIs for managing posts that belongs in the hotels
<!-- START_f6e6a8f63fa88c10a810d956287115f7 -->
## Columns
Display the possible fields of Post.

These fields can also be used to filter the search.

* <b>Please note: </b> the property 'post_type' refers to the template
used for the post, which can override a post.property's default
value or force it to be required.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/post/columns" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/post/columns");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET post/columns`


<!-- END_f6e6a8f63fa88c10a810d956287115f7 -->

<!-- START_b50fbd1dc666341a0aba5436344a60d9 -->
## Index
Display a listing of posts.

To filter posts, add any of the Post object properties to the querry ?{property}={value}

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/posts" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/posts");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET posts`


<!-- END_b50fbd1dc666341a0aba5436344a60d9 -->

<!-- START_afe2787ea395275b46eb1ce2380cd9a0 -->
## Show
Display the specified App by {id}.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/post/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/post/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET post/{id}`


<!-- END_afe2787ea395275b46eb1ce2380cd9a0 -->

<!-- START_6bb34778bbf4ff8243bcb491022de63a -->
## Store
Create a new Post.

<b>Please note: </b>
- When creating new Post you can pass PostTranslate properties, this will create an english translation for the Post.
- Every Post has at least one translation so providing an english title is required.
- Required fields are defined by the chosen PostType, including the once that belong to the PostTranslate.

> Example request:

```bash
curl -X POST "http://apitndati.com/v001/public/post" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/post");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`POST post`


<!-- END_6bb34778bbf4ff8243bcb491022de63a -->

<!-- START_645a4195608b74f2da2f351a0a46c520 -->
## Update
Edit properties of existing DeviceRoom.

> Example request:

```bash
curl -X PUT "http://apitndati.com/v001/public/post/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/post/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`PUT post/{id}`


<!-- END_645a4195608b74f2da2f351a0a46c520 -->

<!-- START_2ce059c7088f98d376b746b52a11e0d7 -->
## Multi-Update
Edit properties of multiple existing Post.

> Example request:

```bash
curl -X PUT "http://apitndati.com/v001/public/posts" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/posts");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`PUT posts`


<!-- END_2ce059c7088f98d376b746b52a11e0d7 -->

<!-- START_629779ab47d01268662b313a43e29077 -->
## Destroy
Remove an DeviceRoom.

> Example request:

```bash
curl -X DELETE "http://apitndati.com/v001/public/post/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/post/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`DELETE post/{id}`


<!-- END_629779ab47d01268662b313a43e29077 -->

#PostsTranslates

APIs for managing translations of posts that belongs in the hotels
<!-- START_a71d42a03c21d1efb5b60c04f4c81a1b -->
## Columns
Display the possible fields of PostsTranslate.

These fields can also be used to filter the search.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/posts_translate/columns" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/posts_translate/columns");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET posts_translate/columns`


<!-- END_a71d42a03c21d1efb5b60c04f4c81a1b -->

<!-- START_e56557007b915714ee075d32fe343914 -->
## Index
Display a listing of posts translates.

To filter posts translate, add any of the PostTranslate object properties to the querry ?{property}={value}

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/posts_translates" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/posts_translates");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET posts_translates`


<!-- END_e56557007b915714ee075d32fe343914 -->

<!-- START_307e20756277eadf5c05776d62e9d3f6 -->
## Show
Display the specified App by {id}.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/posts_translate/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/posts_translate/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET posts_translate/{id}`


<!-- END_307e20756277eadf5c05776d62e9d3f6 -->

<!-- START_c818ae418ceccd58247266ab68f8931f -->
## Store
Create a new PostsTranslate.

> Example request:

```bash
curl -X POST "http://apitndati.com/v001/public/posts_translate" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/posts_translate");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`POST posts_translate`


<!-- END_c818ae418ceccd58247266ab68f8931f -->

<!-- START_fd7b5a3529ba3721405d3d921c9568b4 -->
## Update
Edit properties of existing DeviceRoom.

> Example request:

```bash
curl -X PUT "http://apitndati.com/v001/public/posts_translate/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/posts_translate/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`PUT posts_translate/{id}`


<!-- END_fd7b5a3529ba3721405d3d921c9568b4 -->

<!-- START_69e668c979e9369d6957e02802ef8e11 -->
## Destroy
Remove an DeviceRoom.

> Example request:

```bash
curl -X DELETE "http://apitndati.com/v001/public/posts_translate/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/posts_translate/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`DELETE posts_translate/{id}`


<!-- END_69e668c979e9369d6957e02802ef8e11 -->

#Public

APIs for managing requests that doesn't require authentification
<!-- START_515cba9e41609d341721af816a2f591f -->
## Dati versions
Display all the verions of Dati App.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/dati_app_version" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/dati_app_version");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET dati_app_version`


<!-- END_515cba9e41609d341721af816a2f591f -->

<!-- START_c3471b300943ae219fd2e3c02b9ca943 -->
## Dati last version
Display only the last &#039;Live&#039; verion of Dati App.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/dati_last_version" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/dati_last_version");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET dati_last_version`


<!-- END_c3471b300943ae219fd2e3c02b9ca943 -->

<!-- START_7609a2d501c53fd287dec4034f7cf14c -->
## Request password reset
Send a link to the specified email (if it is registered) allowing the user to reset his/her password.

> Example request:

```bash
curl -X POST "http://apitndati.com/v001/public/password/request" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}" \
    -d '{"email":"uA2ZftMU15veZsrZ","redirect_after_reset":"RH7b0q9IsBTEw6yf"}'

```

```javascript
const url = new URL("http://apitndati.com/v001/public/password/request");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

let body = {
    "email": "uA2ZftMU15veZsrZ",
    "redirect_after_reset": "RH7b0q9IsBTEw6yf"
}

fetch(url, {
    method: "POST",
    headers: headers,
    body: body
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`POST password/request`

#### Body Parameters

Parameter | Type | Status | Description
--------- | ------- | ------- | ------- | -----------
    email | string |  required  | The email address of the user that require password reset.
    redirect_after_reset | URL |  optional  | This will tell the API where to redirect the user after successfully reseted password.

<!-- END_7609a2d501c53fd287dec4034f7cf14c -->

<!-- START_cafb407b7a846b31491f97719bb15aef -->
## Submit new password
Change the user password and redirect him/her to the specified link, or return {&#039;message&#039; =&gt; &#039;Success&#039;} if no url is provided.

> Example request:

```bash
curl -X POST "http://apitndati.com/v001/public/password/reset" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/password/reset");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`POST password/reset`


<!-- END_cafb407b7a846b31491f97719bb15aef -->

#Rooms

APIs for managing rooms of hotels
<!-- START_6c2fa7a6fb13ee5ec73f50d672f5ec20 -->
## Columns
Display the possible fields of Room.

These fields can also be used to filter the search.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/room/columns" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/room/columns");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET room/columns`


<!-- END_6c2fa7a6fb13ee5ec73f50d672f5ec20 -->

<!-- START_a1b2184064abffa4b42babd2e458ebea -->
## Index
Display a listing of rooms.

To filter rooms, add any of the Room object properties to the querry ?{property}={value}

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/rooms" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/rooms");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET rooms`


<!-- END_a1b2184064abffa4b42babd2e458ebea -->

<!-- START_b4e373f2a94258f2043428bac37a51be -->
## Show
Display the specified App by {id}.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/room/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/room/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET room/{id}`


<!-- END_b4e373f2a94258f2043428bac37a51be -->

<!-- START_ddb21b77ba3ecbec7015e9bb6b9422fe -->
## Store
Create a new Room.

> Example request:

```bash
curl -X POST "http://apitndati.com/v001/public/room" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/room");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`POST room`


<!-- END_ddb21b77ba3ecbec7015e9bb6b9422fe -->

<!-- START_09c49e144a075ec8e1e450e9621ef561 -->
## Update
Edit properties of existing DeviceRoom.

> Example request:

```bash
curl -X PUT "http://apitndati.com/v001/public/room/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/room/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`PUT room/{id}`


<!-- END_09c49e144a075ec8e1e450e9621ef561 -->

<!-- START_2bcf81981001b1842981a61483f0d0cd -->
## Destroy
Remove an DeviceRoom.

> Example request:

```bash
curl -X DELETE "http://apitndati.com/v001/public/room/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/room/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`DELETE room/{id}`


<!-- END_2bcf81981001b1842981a61483f0d0cd -->

#ShoppingOrders

APIs for managing shopping orders
<!-- START_add82e5e10652a42143a3686a14fec53 -->
## Columns
Display the possible fields of ShoppingOrder.

These fields can also be used to filter the search.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/shopping_order/columns" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/shopping_order/columns");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET shopping_order/columns`


<!-- END_add82e5e10652a42143a3686a14fec53 -->

<!-- START_afe05b4c59fd8e43c86b555cb8e263e5 -->
## Index
Display a listing of shopping orders.

To filter shopping orders, add any of the ShoppingOrder object properties to the querry ?{property}={value}

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/shopping_orders" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/shopping_orders");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET shopping_orders`


<!-- END_afe05b4c59fd8e43c86b555cb8e263e5 -->

<!-- START_06a5d894d186c9e1f46b1ed52659266b -->
## Show
Display the specified App by {id}.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/shopping_order/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/shopping_order/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET shopping_order/{id}`


<!-- END_06a5d894d186c9e1f46b1ed52659266b -->

<!-- START_6f85b66331e9d543b18b8409049ed059 -->
## Store
Create a new ShoppingOrder.

> Example request:

```bash
curl -X POST "http://apitndati.com/v001/public/shopping_order" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/shopping_order");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`POST shopping_order`


<!-- END_6f85b66331e9d543b18b8409049ed059 -->

<!-- START_72f97ea6ed3d5900c47b9da7f0769494 -->
## Update
Edit properties of existing DeviceRoom.

> Example request:

```bash
curl -X PUT "http://apitndati.com/v001/public/shopping_order/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/shopping_order/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`PUT shopping_order/{id}`


<!-- END_72f97ea6ed3d5900c47b9da7f0769494 -->

<!-- START_56f69a46831e85eb22f9b4541d9161d3 -->
## Destroy
Remove an DeviceRoom.

> Example request:

```bash
curl -X DELETE "http://apitndati.com/v001/public/shopping_order/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/shopping_order/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`DELETE shopping_order/{id}`


<!-- END_56f69a46831e85eb22f9b4541d9161d3 -->

#Stays

APIs for managing stays which represent the relation between a Tourist and DeviceRoom
<!-- START_e1c4c9233bfd1d2ada5c1ed2ca2a2b3c -->
## Columns
Display the possible fields of Stay.

These fields can also be used to filter the search.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/stay/columns" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/stay/columns");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET stay/columns`


<!-- END_e1c4c9233bfd1d2ada5c1ed2ca2a2b3c -->

<!-- START_7ec93600986b0513ffb54141bfa58a1c -->
## Index
Display a listing of stays.

To filter stays, add any of the Stay object properties to the querry ?{property}={value}

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/stays" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/stays");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET stays`


<!-- END_7ec93600986b0513ffb54141bfa58a1c -->

<!-- START_5537b565d5ee022cff682b26abfe4a33 -->
## Show
Display the specified App by {id}.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/stay/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/stay/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET stay/{id}`


<!-- END_5537b565d5ee022cff682b26abfe4a33 -->

<!-- START_662c400ea1be1d71b1c283348b3ad4a5 -->
## Store
Create a new Stay.

> Example request:

```bash
curl -X POST "http://apitndati.com/v001/public/stay" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/stay");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`POST stay`


<!-- END_662c400ea1be1d71b1c283348b3ad4a5 -->

<!-- START_73d7932ebec954c3b9c6243d4441cafd -->
## Destroy
Remove an DeviceRoom.

> Example request:

```bash
curl -X DELETE "http://apitndati.com/v001/public/stay/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/stay/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`DELETE stay/{id}`


<!-- END_73d7932ebec954c3b9c6243d4441cafd -->

#Tourists

APIs for managing tourists
<!-- START_0b613b3ad37dcb2d37cc5c14626ef656 -->
## Columns
Display the possible fields of Tourist.

These fields can also be used to filter the search.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/tourist/columns" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/tourist/columns");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET tourist/columns`


<!-- END_0b613b3ad37dcb2d37cc5c14626ef656 -->

<!-- START_e3af04dfe3145a52a28a66188e65a036 -->
## Index
Display a listing of tourists.

To filter tourists, add any of the Tourist object properties to the querry ?{property}={value}

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/tourists" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/tourists");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET tourists`


<!-- END_e3af04dfe3145a52a28a66188e65a036 -->

<!-- START_2b29fd02788e5c4edc8a7471e1e75b9d -->
## Show
Display the specified App by {id}.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/tourist/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/tourist/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET tourist/{id}`


<!-- END_2b29fd02788e5c4edc8a7471e1e75b9d -->

<!-- START_b582cfb013c26a9e6a95f11b0a4834f6 -->
## Store
Create a new Tourist.

> Example request:

```bash
curl -X POST "http://apitndati.com/v001/public/tourist" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/tourist");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`POST tourist`


<!-- END_b582cfb013c26a9e6a95f11b0a4834f6 -->

<!-- START_27a5d04128bce45417fed04809926aec -->
## Update
Edit properties of existing DeviceRoom.

> Example request:

```bash
curl -X PUT "http://apitndati.com/v001/public/tourist/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/tourist/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`PUT tourist/{id}`


<!-- END_27a5d04128bce45417fed04809926aec -->

<!-- START_3e7434f43147082d7b4720f36d6349a8 -->
## Destroy
Remove an DeviceRoom.

> Example request:

```bash
curl -X DELETE "http://apitndati.com/v001/public/tourist/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/tourist/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`DELETE tourist/{id}`


<!-- END_3e7434f43147082d7b4720f36d6349a8 -->

#User

APIs for managing users
<!-- START_89966bfb9ab533cc3249b91a9090d3dc -->
## Index
Display a listing of users.

To filter users, add any of the user object properties to the querry ?{property}={value}

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/users" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/users");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response (200):

```json
{
    "data": [
        {
            "id": "integer",
            "name": "string",
            "email": "string",
            "approved": "integer",
            "role": "integer",
            "created_at": "Timestamp",
            "updated_at": "Timestamp"
        }
    ]
}
```

### HTTP Request
`GET users`


<!-- END_89966bfb9ab533cc3249b91a9090d3dc -->

<!-- START_5da5fc82b574fb975787032a8fdb66e6 -->
## Show
Display the specified User by {id}.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/user/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/user/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET user/{id}`


<!-- END_5da5fc82b574fb975787032a8fdb66e6 -->

<!-- START_3efbce72c5183a8fae61143a8bcdd44a -->
## Store
Create a new User.

> Example request:

```bash
curl -X POST "http://apitndati.com/v001/public/user" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/user");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`POST user`


<!-- END_3efbce72c5183a8fae61143a8bcdd44a -->

<!-- START_6df5775a997877107592d7839ce70310 -->
## Update
Edit properties of existing User.

> Example request:

```bash
curl -X PUT "http://apitndati.com/v001/public/user/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/user/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`PUT user/{id}`


<!-- END_6df5775a997877107592d7839ce70310 -->

<!-- START_b81b03c80aa59c476a5cd8e7e12a1d4e -->
## Destroy
Remove an User.

> Example request:

```bash
curl -X DELETE "http://apitndati.com/v001/public/user/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/user/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`DELETE user/{id}`


<!-- END_b81b03c80aa59c476a5cd8e7e12a1d4e -->

#app version

APIs for managing apps versions
<!-- START_b11d848e3031bbd50554d0b8eab85ad4 -->
## Columns
Display the possible fields of AppVersion.

These fields can also be used to filter the search.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/app_version/columns" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/app_version/columns");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET app_version/columns`


<!-- END_b11d848e3031bbd50554d0b8eab85ad4 -->

<!-- START_73f4102fa7022b15f443eac66e808537 -->
## Index
Display a listing of apps versions.

To filter apps versions, add any of the AppVersion object properties to the querry ?{property}={value}

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/app_versions" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/app_versions");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET app_versions`


<!-- END_73f4102fa7022b15f443eac66e808537 -->

<!-- START_da9d1321bb91f2f23d56e95bc4317f5b -->
## Show
Display the specified AppVersion by {id}.

> Example request:

```bash
curl -X GET -G "http://apitndati.com/v001/public/app_version/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/app_version/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "GET",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```

> Example response:

```json
null
```

### HTTP Request
`GET app_version/{id}`


<!-- END_da9d1321bb91f2f23d56e95bc4317f5b -->

<!-- START_cc77505cd729f388325410d57082c315 -->
## Store
Create a new AppVersion.

> Example request:

```bash
curl -X POST "http://apitndati.com/v001/public/app_version" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/app_version");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "POST",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`POST app_version`


<!-- END_cc77505cd729f388325410d57082c315 -->

<!-- START_e1030ae6805f8f7ef0e0201f80a16933 -->
## Update
Edit properties of existing AppVersion.

> Example request:

```bash
curl -X PUT "http://apitndati.com/v001/public/app_version/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/app_version/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "PUT",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`PUT app_version/{id}`


<!-- END_e1030ae6805f8f7ef0e0201f80a16933 -->

<!-- START_6758265b64a71a633954eeac75076094 -->
## Destroy
Remove an AppVersion.

> Example request:

```bash
curl -X DELETE "http://apitndati.com/v001/public/app_version/{id}" \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer {token}"
```

```javascript
const url = new URL("http://apitndati.com/v001/public/app_version/{id}");

let headers = {
    "Accept": "application/json",
    "Content-Type": "application/json",
    "Authorization": "Bearer {token}",
}

fetch(url, {
    method: "DELETE",
    headers: headers,
})
    .then(response => response.json())
    .then(json => console.log(json));
```


### HTTP Request
`DELETE app_version/{id}`


<!-- END_6758265b64a71a633954eeac75076094 -->


