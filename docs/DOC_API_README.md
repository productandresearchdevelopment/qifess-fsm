## DOCUMENTATION POST PUBLIC API

### MASTER DATA
```bashd
MASTER DATA
Menampilkan JSON Data Master yang dibutuhkan untuk Create WO
URL		: https://post.petro1-indonesia.com/api/public/master
METHOD	: GET
HEADER	: key = "YOUR TOKEN"
RESPON	: JSON
{
  "activities": ARRAY [
    {
      "id": Integer,
      "alias": String,
      "name": String,
      "color": STRING HEX(DATA EXAMPLE: "0066CC"),
      "site_on": Bool (1/0),
      "site_off": Bool (1/0),
      "description": String
    }
  ],
  "owners": ARRAY [
    {
      "id": Integer,
      "name": String,
      "alias": String,
      "address": String
    }
  ],
  "services": ARRAY [
    {
      "id": Integer,
      "alias": String,
      "name": String,
      "color": STRING HEX(DATA EXAMPLE: "0066CC"),
      "description": String,
      "created_by": CHAR (UUID),
      "updated_by": CHAR (UUID),
      "deleted_by": CHAR (UUID),
      "created_at": TIMESTAMP,
      "updated_at": TIMESTAMP,
      "deleted_at": TIMESTAMP
    }
  ]
}
```

### CLIENT
```bashd
CLIENT DATA
Menampilkan list data Client
URL		: https://post.petro1-indonesia.com/api/public/client/data
METHOD	: GET
HEADER	: key = "YOUR TOKEN"
PARAM	: query = "String (Search Field Data)"
RESPON	: JSON (ARRAY) (LIMIT MAX 20 RECORD)
[
  {
    "id": Integer,
    "customer_id": String,
    "name": String,
    "alias": String,
    "address": String,
    "phone": String,
    "email": String,
    "description": String,
    "created_by": CHAR (UUID),
    "updated_by": CHAR (UUID),
    "deleted_by": CHAR (UUID),
    "created_at": TIMESTAMP,
    "updated_at": TIMESTAMP,
    "deleted_at": TIMESTAMP
  }
]

CLIENT GET
Menampilkan list data Client
URL		: https://post.petro1-indonesia.com/api/public/client/get/{client_id}
METHOD	: GET
HEADER	: key = "YOUR TOKEN"
RESPON	: JSON (OBJECT)
{
    "id": Integer,
    "customer_id": String,
    "name": String,
    "alias": String,
    "address": String,
    "phone": String,
    "email": String,
    "description": String,
    "created_by": CHAR (UUID),
    "updated_by": CHAR (UUID),
    "deleted_by": CHAR (UUID),
    "created_at": TIMESTAMP,
    "updated_at": TIMESTAMP,
    "deleted_at": TIMESTAMP
}

CLIENT PUSH
Menambahkan Client baru ke aplikasi POST
URL		: https://post.petro1-indonesia.com/api/public/client/push
METHOD	: POST
HEADER	: key = "YOUR TOKEN"
PARAM	: 	- customer_id (*): String (20)
            - name (*): String (50)
            - alias (*): String (10)
            - phone: String (30)
            - address: String (255)
            - email: String (255)
            - description: String (255)
RESPON	: JSON (OBJECT)
{
    "success": Bool (true / false),
    "message": String,
    "data": {
        "id": Integer,
        "customer_id": String,
        "name": String,
        "alias": String,
        "address": String,
        "phone": String,
        "email": String,
        "description": String,
        "created_by": CHAR (UUID),
        "updated_by": CHAR (UUID),
        "deleted_by": CHAR (UUID),
        "created_at": TIMESTAMP,
        "updated_at": TIMESTAMP,
        "deleted_at": TIMESTAMP
    }
}	
```

### SITE
```bashd 
	SITE DATA
		Menampilkan list data Site
		URL		: https://post.petro1-indonesia.com/api/public/site/data
		METHOD	: GET
		HEADER	: key = "YOUR TOKEN"
		PARAM	: query = "String (Search Field Data)"
		RESPON	: JSON (ARRAY) (LIMIT MAX 20 RECORD)
		[
			{
				"id": int,
				"link_id": varchar,
				"client_id": int,
				"name": varchar,
				"terminal_name": varchar,
				"beam": varchar,
				"airmac": varchar,
				"serial_number": varchar,
				"service_id": int,
				"owner_id": int,
				"address": varchar,
				"pic": varchar,
				"pic_phone": varchar,
				"pic_email": varchar,
				"lat": double,
				"long": double,
				"is_active": int,
				"active_date": date,
				"inactive_date": date,
				"description": varchar,
				"created_by": CHAR (UUID),
				"updated_by": CHAR (UUID),
				"deleted_by": CHAR (UUID),
				"created_at": TIMESTAMP,
				"updated_at": TIMESTAMP,
				"deleted_at": TIMESTAMP	
			}
		]
		
	SITE GET
		Menampilkan list data Site
		URL		: https://post.petro1-indonesia.com/api/public/site/get/{site_id}
		METHOD	: GET
		HEADER	: key = "YOUR TOKEN"
		RESPON	: JSON (OBJECT)
		{
			"id": int,
			"link_id": varchar,
			"client_id": int,
			"name": varchar,
			"terminal_name": varchar,
			"beam": varchar,
			"airmac": varchar,
			"serial_number": varchar,
			"service_id": int,
			"owner_id": int,
			"address": varchar,
			"pic": varchar,
			"pic_phone": varchar,
			"pic_email": varchar,
			"lat": double,
			"long": double,
			"is_active": int,
			"active_date": date,
			"inactive_date": date,
			"description": varchar,
			"created_by": CHAR (UUID),
			"updated_by": CHAR (UUID),
			"deleted_by": CHAR (UUID),
			"created_at": TIMESTAMP,
			"updated_at": TIMESTAMP,
			"deleted_at": TIMESTAMP	
		}
		
	SITE PUSH
		Menambahkan Client baru ke aplikasi POST
		URL		: https://post.petro1-indonesia.com/api/public/client/push
		METHOD	: POST
		HEADER	: key = "YOUR TOKEN"
		PARAM	: 	- client_id (*): int (Foreignkey Client)
					- service_id (*): int (Foreignkey Master > Service)
					- name (*):  varchar (100) 
					- link_id:  varchar (30) 
					- terminal_name: varchar (30)
					- beam: varchar (30)
					- airmac: varchar (30)
					- serial_number: varchar (30)
					- owner_id: int (Foreignkey Master > Owner)
					- address: varchar (255)
					- pic: varchar (100)
					- pic_phone: varchar (30)
					- pic_email: varchar (150)
					- lat: double (Coordinate Decimal)
					- long: double (Coordinate Decimal)
					- is_active: int (Boolean 1 Aktif / 0 Non Aktif)
					- active_date: date (Format: 'Y-m-d')
					- inactive_date: date (Format: 'Y-m-d')
					- description: varchar (255)
		RESPON	: JSON (OBJECT)
		{
			"success": Bool (true / false),
			"message": String,
			"data": {
				"id": Integer,
				"customer_id": String,
				"name": String,
				"alias": String,
				"address": String,
				"phone": String,
				"email": String,
				"description": String,
				"created_by": CHAR (UUID),
				"updated_by": CHAR (UUID),
				"deleted_by": CHAR (UUID),
				"created_at": TIMESTAMP,
				"updated_at": TIMESTAMP,
				"deleted_at": TIMESTAMP
			}
		}	
```

### WORK ORDER
```bashd
WORKORDER GET
Menampilkan Record WO By ID
URL		: https://post.petro1-indonesia.com/api/public/wo/get/{wo_id}
METHOD	: GET
HEADER	: key = "YOUR TOKEN"
RESPON	: JSON (OBJECT)
{
    "id": INT,
    "site_id": INT,
    "remove_site_id": INT,
    "activity_id": INT,
    "vendor_id": INT,
    "client_id": INT,
    "fieldtech_id": CHAR (UUID),
    "service_id": INT,
    "owner_id": INT,
    "no_wo": String,
    "description": String,
    "start_date": Date,
    "expire_date": Date,
    "close_date": Date,
    "last_action": CHAR (UUID),
    "created_by": CHAR (UUID),
    "updated_by": CHAR (UUID),
    "deleted_by": CHAR (UUID),
    "created_at": TIMESTAMP,
    "updated_at": TIMESTAMP,
    "deleted_at": TIMESTAMP
}

WORKORDER PUSH
Menambahkan WO baru ke aplikasi POST
URL		: https://post.petro1-indonesia.com/api/public/wo/push
METHOD	: POST
HEADER	: key = "YOUR TOKEN"
PARAM	: 	- activity_id (*): int (FOREIGNKEY MASTER > ACTIVITY)
            - site_id (* JIKA ACTIVITY "site_on" = 1): int (FOREIGNKEY SITE)
            - remove_site_id (* JIKA ACTIVITY "site_off" = 1): int (FOREIGNKEY SITE)
            - service_id (*): int (11) (FOREIGNKEY MASTER > SERVICE)
            - no_wo (*): varchar (50) 
            - owner_id (*): int (11) (FOREIGNKEY MASTER > OWNER)
            - description(*): varchar (255)
            - start_date: date (Format: 'Y-m-d')
            - expire_date: date (Format: 'Y-m-d')

RESPON	: JSON (OBJECT)
{
    "success": Bool (true / false),
    "message": String,
    "data": "JIKA SUSKSES" {
        "id": INT,
        "site_id": INT,
        "remove_site_id": INT,
        "activity_id": INT,
        "vendor_id": INT,
        "client_id": INT,
        "fieldtech_id": CHAR (UUID),
        "service_id": INT,
        "owner_id": INT,
        "no_wo": String,
        "description": String,
        "start_date": Date,
        "expire_date": Date,
        "close_date": Date,
        "last_action": CHAR (UUID),
        "created_by": CHAR (UUID),
        "updated_by": CHAR (UUID),
        "deleted_by": CHAR (UUID),
        "created_at": TIMESTAMP,
        "updated_at": TIMESTAMP,
        "deleted_at": TIMESTAMP
    }
}	
```

