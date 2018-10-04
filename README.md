# Chris Bradbury Technical Test

Request and response bodies are JSON:API-esque. Examples for the requirements
will be given below, more can be found in the "Agencies" and "Services"
integration tests directories.

Please let me know if you have further questions.

## Setup and Teardown

Running the `setup` file in the root directory will build and start the 
development environment. The app should be available immediately on `localhost`.
Running `setup` with the `--tests` option will run all tests before setup
exits.

After testing the `teardown` script will stop the running Docker containers and
delete a couple of Docker volumes used by the app.

## Use

Please set the "x-api-key" header field to "1234567890" to authenticate.
The server will be available on `http://localhost/api/`. Sending a `GET` request 
to the `services` and `agencies` endpoints will fetch the relevant indexes.

## Expected Functionality

The following functionality is accessible via appropriate RESTful HTTP calls.

### Agencies

#### Retrieve an index of agencies:
```http request
GET http://localhost/api/agencies
Content-Type: application/json
x-api-key: 1234567890
```

#### View details of single agency by retrieving it by its ID:
```http request
GET http://localhost/api/agencies/1
Content-Type: application/json
x-api-key: 1234567890
```

#### Create a new agency:
```http request
POST http://localhost/api/agencies
Content-Type: application/json
x-api-key: 1234567890

{
    "data": [
        {
            "type": "agencies",
            "attributes": {
                "name": "First Created Agency",
                "contactEmail": "hello@test.com",
                "webAddress": "http://test.com",
                "shortDescription": "The testiest chips known to man.",
                "established": "2019"
            },
            "relationships": {
                "services": {
                    "data": [
                        {
                            "type": "services",
                            "id": "1"
                        },
                        {
                            "type": "services",
                            "id": "2"
                        }
                    ]
                }
            }
        }
    ]
}
```

### Services

#### Retrieve an index of services:
```http request
GET http://localhost/api/services
Content-Type: application/json
x-api-key: 1234567890
```

#### View details of a single service by retrieving it by its slug:
```http request
GET http://localhost/api/services/web-development
Content-Type: application/json
x-api-key: 1234567890
```

### Relationships

#### View the services an agency offers:

*View full details of the related services.*
```http request
GET http://localhost/api/agencies/1/services
Content-Type: application/json
x-api-key: 1234567890
```

*View a relationships object containing the related services.*
```http request
GET http://localhost/api/agencies/1/relationships/services
Content-Type: application/json
x-api-key: 1234567890
```

#### Update the services an agency offers

*Only updating service relationships:*
```http request
PUT http://localhost/api/agencies/1/relationships/services
Content-Type: application/json
x-api-key: 1234567890

{
    "data": [
        {
            "id": "1",
            "type": "agencies",
            "relationships": {
                "services": {
                    "data": [
                        {
                            "type": "services",
                            "id": "2"
                        },
                        {
                            "type": "services",
                            "id": "3"
                        }
                    ]
                }
            }
        }
    ]
}
```

*Updating both service relationships and agency details:*
```http request
PUT http://localhost/api/agencies/1/relationships/services
Content-Type: application/json
x-api-key: 1234567890

{
    "data": [
        {
            "id": "1",
            "type": "agencies",
            "attributes": {
                "name": "First Updated agency",
                "contactEmail": "hello@firstupdated.com",
                "webAddress": "http://firstupdated.com",
                "shortDescription": "The firstiest update known to man.",
                "established": "2019"
            },
            "relationships": {
                "services": {
                    "data": [
                        {
                            "type": "services",
                            "id": "2"
                        },
                        {
                            "type": "services",
                            "id": "3"
                        }
                    ]
                }
            }
        }
    ]
}
```
