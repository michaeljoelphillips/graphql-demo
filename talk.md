%title: GraphQL
%author: Michael Phillips
%date: 11/17/20

-> Building GraphQL APIs in PHP <-
==================================

* What is GraphQL?
* GraphQL vs REST
* GraphQL in PHP

-------------------------------------------------

-> What is GraphQL? <-
======================

-------------------------------------------------

-> What is GraphQL? <-
======================

> [GraphQL](https://graphql.org) is a query language for APIs and a runtime for fulfilling those
> queries with your existing data. GraphQL provides a complete and understandable
> description of the data in your API, gives clients the power to ask for exactly
> what they need and nothing more, makes it easier to evolve APIs over time, and
> enables powerful developer tools.
<br>

# Two Main Concepts

* The Schema
* The Query Language

-------------------------------------------------

-> What is GraphQL? <-
======================

[The Schema](http://spec.graphql.org/June2018/#sec-Schema)

> A GraphQL service’s collective type system capabilities are referred to as
> that service’s “schema”. A schema is defined in terms of the types and
> directives it supports as well as the root operation types for each kind of
> operation: query, mutation, and subscription; this determines the place in the
> type system where those operations begin.

```
 
 schema {                      # Root Type
     query: Query
 }
 
 type Query {                  # Query Type
     orders: [Order]
 }
 
 type Order {                  # Object Type
     id: ID!                   # ID Type
     productsList: [Product]   # Array Type
     totalAmount: Integer!     # Scalar Type
     customer: Customer
 }
 
 type OrderItem {
     product: Product!
     quantity: Integer!
 }
 
 type Product {
     name: String!
     price: Float!
 }
 
 type Customer {
    id: ID!
    name: String!
    email: String!
 }
 
```

* "!" denotes non-nullable types

* All requests and responses are validated against the schema at runtime
  * With a proper GraphQL implementation, it is _*impossible*_ to send or recieve
    data that does not match the types defined by the schema

-------------------------------------------------

-> What is GraphQL? <-
======================

# The Query Language

```
 
    # Query (GraphQL)                        # Response (JSON)
 
    {                                        {
      orders {                                 "data": {
        id                                       "orders": [
        productsList {                             {
          product {                                  "id": 1,
            name                                     "productsList": [
            price                                      {
          }                                              "product": {
          quantity                                         "name": "orange",
        }                                                   "price": 1.5
        totalAmount                                       },
      }                                                   "quantity": 100
    }                                                  }
                                                   ],
                                                    "totalAmount": 150
                                                   }
                                                 ]
                                               }
                                             }
 
```

* Clients ask for exactly what they need
* Queries are made with the Query Language
* Responses are returned as JSON
  * GraphQL is _not_ JSON

-------------------------------------------------

-> What is GraphQL? <-
======================

# GraphQL over HTTP

## Request

```
 
 $ http -vvv POST localhost:8888 <<< '
 {
   "query": "query {\n characters {\n name\n quotes\n {\n phrase\n }\n }\n}\n"
 }
 '
 
 POST / HTTP/1.1
 Accept: application/json, */*;q=0.5
 Content-Length: 83
 Content-Type: application/json
 Host: localhost:8888
 
 {
   "query": "query {\n characters {\n name\n quotes\n {\n phrase\n }\n }\n}\n"
 }
 
```
<br>

## Response

```
 
 HTTP/1.1 200 OK
 Content-Type: application/json
 Host: localhost:8888
 
 {
     "data": {
         "characters": [
             {
                 "name": "Obi-Wan",
                 "quotes": []
             }
         ]
     }
 }
 
```

* Requests should be made via `POST` to `/graphql` as `application/json`
  - (A recommended convention)

* Uniform request and response body as per the GraphQL specification

-------------------------------------------------

-> GraphQL vs REST <-
=====================

-------------------------------------------------

-> GraphQL vs REST <-
======================

# REST

* Makes use of the various features of HTTP
    * HTTP verbs communicate intent
    * HTTP resources represent business objects

```
 
 POST /users HTTP/1.1
 Content-Type: application/json
 
 {
   "data": {
     "firstName": "Michael",
     "lastName": "Phillips",
     "userName": "michaelphillips"
   }
 }
 
 HTTP/1.1 201 OK
 Location: /user/1
 
```

* Relationships can be expressed through nested resources

```
 
 GET /users/1/posts HTTP/1.1
 Content-Type: application/json
 
 {
   "data": [
     {
       "id": "1",
       "userId": "1",
       "title": "My First Post",
       "body": "Hello World"
     }
   ]
 }
 
 HTTP/1.1 201 OK
 Location: /user/1
 
```

* Stateless
* Cacheable
* Multile repsonse formats
    * [HAL](https://en.wikipedia.org/wiki/Hypertext_Application_Language)
    * [JSON-LD](https://en.wikipedia.org/wiki/JSON-LD)
    * [JSON API](https://jsonapi.org/)

-------------------------------------------------

-> GraphQL vs REST <-
======================

# GraphQL

* Uses graph types for expressing relationships
* Multiple queries can be executed in the same request

```
 
 query {
     users(id: "1") {
         id
         userName
         posts {
             id
             title
             body
         }
     }
     featuredPosts {
         user {
             id
             userName
             firstName
             lastName
         }
         post {
             id
             title
             body
         }
     }
 }
 
```

* Uses a single HTTP verb for all requests
* Writes are performed via *mutations*

```
 
 mutation {
     addPost(input: $input) {
         id
         title
         body
     }
 }
 
 {
     "input": {
         "title": "My First Post",
         "body": "Hello World"
     }
 }
```

* Stateless
* Clients request only what they need
* Much more difficult to cache client-side

-------------------------------------------------

-> GraphQL vs REST <-
=====================

# Further Reading

https://www.howtographql.com/basics/1-graphql-is-the-better-rest/

-------------------------------------------------

-> GraphQL in PHP <-
====================

-------------------------------------------------

-> GraphQL in PHP <-
====================

# Runtime / Architecture Overview

* Takes no opinions about how to retrieve data
  * SQL
  * Object Model
  * Document Store
  * Upstream RESTful APIs

* Functional by design, functions called *_resolvers_* are used for retrieving
  and formatting data

```
 
 $myUserResolver = static function (): User {
     return new User('1', 'michaelphillips', 'Michael', 'Phillips');
 };
 
 $myPostsResolver = static function (): array {
     return [
         [
             'id' => '1',
             'title' => 'My First Post',
             'body' => 'Hello World',
         ],
     ];
 };
 
```

* Data returned *must* match the types defined by the schema, how you map the
  data to the schema is entirely up to you
* Resolvers can return ReactPHP's Promise A+ implementation for Async I/O

```
 
 $myAsyncResolver = static function(): PromiseInterface {
     $deferred = new Deferred();
 
     someAsyncOperation()
         ->then(static function ($result) use (&$deferred) {
             $data = $result['data'];
 
             $deferred->resolve($data);
         });
 
     return new $deferred->promise();
 };
 
```

-------------------------------------------------

-> GraphQL in PHP <-
====================

# Demo Time

https://github.com/webonyx/graphql-php
https://github.com/michaeljoelphillips/graphql-demo
