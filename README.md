# Micro Blog



## Description

This is a demo api quickly built to replicate Twitter. There's a ton of room left for improvement and is converted from my Ruby on Rails attempt.

**Not recommended for deployment**

Would need proper rate limiting and probably other features after testing with a front-end application. Would also need proper cleanup and seperating some variables as environment variables.

## Dependencies

This application requires:

* Lumen 8.3.4

## Configure Application

## Create JWT Token

```
php artisan jwt:secret
```

## Create Demo DB

```
php artisan db:migrate
```
The following command creates demo data in the DB
```
php artisan db:seed
```

## Start Rails Server

```
php artisan serve
```

## Test API routes

```
Needs to be implemented
```

## Admin Account

The following details for the admin account is only available when you've finished seeding the database.

Email: `admin@fake.com`  
Password: `password`

## API

### Register

Allows you to create accounts.

```
http://127.0.0.1:3000/api/v1/register
```

Post:

```
{
    "user": {
        "name": "New User",
        "email": "new_user@fake.com",
        "birth_date": "2021-12-19",
        "password": "Password1",
        "password_confirmation": "Password1"
    }
}
```

Response:

```
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sdW1lbi1taWNyby1ibG9nLWFwaS50ZXN0XC9hcGlcL3YxXC9yZWdpc3RlciIsImlhdCI6MTY0NTAwMTg1MCwiZXhwIjoxNjQ1MDA1NDUwLCJuYmYiOjE2NDUwMDE4NTAsImp0aSI6IlNsT1F1WTFIOHpXYXp2Q3ciLCJzdWIiOjIwMDMsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.rwCO3BU4GsD7E0-xKHaeI0r3H1MOk5v7kREHtul7Mh4",
    "token_type": "bearer",
    "expires_in": 86400
}
```

### Authentication

Firstly you need to athenticate at the following url which will return a JWT token.

```
http://127.0.0.1:3000/api/v1/authenticate
```

Post:

```
{
    "email": "admin@fake.com",
    "password": "password"
}
```

Response:

```
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sdW1lbi1taWNyby1ibG9nLWFwaS50ZXN0XC9hcGlcL3YxXC9yZWdpc3RlciIsImlhdCI6MTY0NDkyMTgxMCwiZXhwIjoxNjQ0OTI1NDEwLCJuYmYiOjE2NDQ5MjE4MTAsImp0aSI6IjR2YkF4UmVsZEVmTnQ5SnciLCJzdWIiOjksInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.qiB2Ui-TFXeBl2kar_puU6nJYgndNFHef2KCQHNmHZ8",
    "token_type": "bearer",
    "expires_in": 86400
}
```

### Posts

#### Fetch

You don't need a JWT token here in the header, but with one you'll have your blocks and mutes filtered out.

```
http://127.0.0.1:3000/api/v1/posts
```

Response:

```
[
    {
        "id": 31,
        "user_id": 1,
        "content": "Test post from API",
        "created_at": "2021-12-19T20:05:21.111Z",
        "updated_at": "2021-12-19T20:05:21.111Z"
    }
]
```

#### Create

Posting requires JWT.

```
http://127.0.0.1:3000/api/v1/posts
```

Post:

```
{
    "post": {
        "content": "Test post from API #2"
    }
}
```

Response:

```
{
    "id": 33,
    "user_id": 1,
    "content": "Test post from API #2",
    "created_at": "2021-12-21T12:21:12.774Z",
    "updated_at": "2021-12-21T12:21:12.774Z"
}
```

#### Delete

Deleting requires JWT and for you to be the owner.

```
http://127.0.0.1:3000/api/v1/posts/32
```

Post:

```
{
    "post": {
        "content": "Test post from API #2"
    }
}
```

Response:

The header should be `204 No Content` on successful delete.

### Like

Requires a JWT token.

#### Get

```
http://127.0.0.1:3000/api/v1/likes/admin
```

Response:

```
[
    {
        "id": 1,
        "user_id": 1603,
        "content": "Let me ask you. How fast do you think you could jerk off every guy in this room? Because I know how long it would take me. And I can prove it",
        "created_at": "2021-12-19T19:49:02.771Z",
        "updated_at": "2021-12-19T19:49:02.771Z"
    }
]
```

#### Post

Toggles like.

```
http://127.0.0.1:3000/api/v1/like/1
```

Response:

Returns a `201 Created` if post wasn't liked and `204 No Content` if it removed the like.

```
{
    "id": 4,
    "post_id": 1,
    "user_id": 1,
    "created_at": "2021-12-20T17:56:40.324Z",
    "updated_at": "2021-12-20T17:56:40.324Z"
}
```

### Save

Requires a JWT token.

#### Get

```
http://127.0.0.1:3000/api/v1/saves/admin
```

Response:

```
[
    {
        "id": 1,
        "user_id": 1603,
        "content": "Let me ask you. How fast do you think you could jerk off every guy in this room? Because I know how long it would take me. And I can prove it",
        "created_at": "2021-12-19T19:49:02.771Z",
        "updated_at": "2021-12-19T19:49:02.771Z"
    }
]
```

#### Post

Toggles save.

```
http://127.0.0.1:3000/api/v1/save/1
```

Response:

Returns a `201 Created` if post wasn't saved and `204 No Content` if it removed the save.

```
{
    "id": 3,
    "post_id": 1,
    "user_id": 1,
    "created_at": "2021-12-21T12:59:12.421Z",
    "updated_at": "2021-12-21T12:59:12.421Z"
}
```

### Mute

Requires a JWT token.

#### Get

Returns a list of people you've muted.

```
http://127.0.0.1:3000/api/v1/muted
```

Response:

```
[
    {
        "id": 7,
        "muted_id": 6,
        "muted_by_id": 1,
        "created_at": "2021-12-20T16:39:26.495Z",
        "updated_at": "2021-12-20T16:39:26.495Z",
        "muted": {
            "id": 6,
            "name": "Dave Turner",
            "username": "dave.turner"
        }
    }
]
```

#### Post

```
http://127.0.0.1:3000/api/v1/mute/dave.turner
```

Response:

Returns a `201 Created` if mute was created and `204 No Content` if it removed the mute.

```
{
    "id": 8,
    "muted_id": 6,
    "muted_by_id": 1,
    "created_at": "2021-12-21T13:02:26.054Z",
    "updated_at": "2021-12-21T13:02:26.054Z"
}
```

### Block

Requires a JWT token.

#### Get

Returns a list of people you've blocked.

```
http://127.0.0.1:3000/api/v1/blocked
```

Response:

```
[
    {
        "id": 1,
        "blocked_id": 2,
        "blocked_by_id": 1,
        "created_at": "2021-12-20T10:32:49.696Z",
        "updated_at": "2021-12-20T10:32:49.696Z",
        "blocked": {
            "id": 2,
            "name": "Ollie Lynch",
            "username": "ollie.lynch"
        }
    }
]
```

#### Post

```
http://127.0.0.1:3000/api/v1/block/dave.turner
```

Response:

Returns a `201 Created` if block was created and `204 No Content` if it removed the block.

```
{
    "id": 7,
    "blocked_id": 6,
    "blocked_by_id": 1,
    "created_at": "2021-12-21T13:09:14.687Z",
    "updated_at": "2021-12-21T13:09:14.687Z"
}
```

### Profile

#### Get

Doesn't require JWT, but with JWT it'll not show content if blocked.

```
http://127.0.0.1:3000/api/v1/admin
```

Response:

```
{
    "id": 1,
    "username": "admin",
    "name": "Admin",
    "location": "Cape Town",
    "gender": "male",
    "birth_date": "2022-02-16 08:43:29",
    "website": "",
    "bio": "Time is of the essence.",
    "created_at": "2021-12-19T19:41:52.694Z",
}
```

### Settings

Requires a JWT token.

#### Get

```
http://127.0.0.1:3000/api/v1/profile/settings
```

Response:

```
{
    "id": 1,
    "name": "Admin",
    "username": "admin",
    "bio": "Time is of the essence.",
    "birth_date": "2022-02-16 08:43:29",
    "gender": "male",
    "location": "Cape Town",
    "website": "",
    "email": "admin@fake.com",
    "created_at": "2021-12-19T19:41:52.694Z",
}
```

#### Post

```
http://127.0.0.1:3000/api/v1/profile/settings
```

Post:

```
{
    "user": {
        "name": "Admin",
        "username": "admin",
        "bio": "Time is of the essence.",
        "location": "Cape Town",
        "birth_date": "2022-02-15 12:48:10",
        "gender": "male",
        "website": "",
        "email": "admin@fake.com",
        "created_at": "2021-12-19T19:41:52.694Z",
    }
}
```

Response:

The header should be `204 No Content` on successful update.

### Follower

#### Fetch

For fetching people the user is following.

```
http://127.0.0.1:3000/api/v1/following/admin
```

For fetching people following the user.

```
http://127.0.0.1:3000/api/v1/followers/admin
```

Response:

```
[
    {
        "id": 1811,
        "following_id": 6,
        "follower_id": 1,
        "created_at": "2021-12-20T17:13:46.143Z",
        "updated_at": "2021-12-20T17:13:46.143Z",
        "following": {
            "id": 6,
            "name": "Dave Turner",
            "username": "dave.turner"
        }
    }
]
```

```
[
    {
        "id": 2,
        "following_id": 1,
        "follower_id": 2,
        "created_at": "2021-12-20T17:10:23.842Z",
        "updated_at": "2021-12-20T17:10:23.842Z",
        "follower": {
            "id": 2,
            "name": "Ollie Lynch",
            "username": "ollie.lynch"
        }
    }
]
```

#### Create

Requires JWT and toggles the following status.

```
http://127.0.0.1:3000/api/v1/follow/dave.turner
```

Response:

```
{
    "id": 1812,
    "following_id": 6,
    "follower_id": 1,
    "created_at": "2021-12-21T12:41:24.491Z",
    "updated_at": "2021-12-21T12:41:24.491Z"
}
```

### Conversations

#### Fetch

Requires JWT.

```
http://127.0.0.1:3000/api/v1/conversations
```

Response:

```
[
    {
        "id": 3,
        "sender_id": 1,
        "recipient_id": 1230,
        "created_at": "2021-12-20T15:12:21.596Z",
        "updated_at": "2021-12-20T15:12:21.596Z",
        "sender": {
            "id": 1,
            "name": "Admin",
            "username": "admin"
        },
        "recipient": {
            "id": 1230,
            "name": "Paul Barton",
            "username": "paul.barton"
        }
    }
]
```

#### Create

Requires JWT. Creates conversation or returns existing one when requested.

```
http://127.0.0.1:3000/api/v1/conversations?recipient_id=2
```

Response:

```
{
    "id": 1,
    "sender_id": 1,
    "recipient_id": 2,
    "created_at": "2021-12-20T10:32:49.696Z",
    "updated_at": "2021-12-20T10:32:49.696Z"
}
```

### Messages

Firstly you need to get the conversation id by posting to conversation.

#### Fetch

Requires JWT.

```
http://127.0.0.1:3000/api/v1/conversations/1/messages
```

Response:

```
[
    {
        "id": 1,
        "content": "hello",
        "conversation_id": 1,
        "user_id": 1,
        "created_at": "2021-12-20T15:44:59.713Z",
        "updated_at": "2021-12-20T15:44:59.713Z",
        "user": {
            "id": 1,
            "name": "Admin",
            "username": "admin"
        }
    }
]
```

#### Post

Requires JWT.

```
http://127.0.0.1:3000/api/v1/conversations/1/messages
```

Post:

```
{
    "message": {
        "content": "Hello, world!!"
    }
}
```

Response:

```
{
    "id": 8,
    "content": "Hello, world!!",
    "conversation_id": 1,
    "user_id": 1,
    "created_at": "2021-12-21T12:51:39.207Z",
    "updated_at": "2021-12-21T12:51:39.207Z"
}
```
