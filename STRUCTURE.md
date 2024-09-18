#

# Operations without auth
## Get all larps
Method: `GET`
Endpoint: `/larps`
Arguments: None

## Get all larps (filter)
Note that this only fetches larps that have `published=Y`.
Method: `GET`
Endpoint: `/larps`
Arguments: Added by `?argument=value`
- `org` - Fetches all larps where organizers matches string (`LIKE %{org}%`)
- `from` - Fetches all larps that have one (or more) dates on or after given date (YYYYMMDD).
- `after` - Fetches all larps that have one (or more) dates on or before given date (YYYYMMDD).
- `country` - Fetches all larps in one or more countries (commaseparated list of isoAlpha3 countrycodes, like `SWE,NOR`)
- `continent` - Fetches all larps in a single continent (2-char, like `AF` or `EU`)

## Get a specific larp
Method: `GET`
Endpoint: `/larp/{id}`

# Exports
Any of the above endpoints can also be exported (ie the output format is different) by adding the query parameter `format=`.

## Get RSS feed
Method: `GET`
Argument: `format=rss`
Returns RSS2.0 data

## iCal data
tbd

## Excel
tbd

## CSV

# Operations requiring token
## Update larp
Note that this endpoint *only* updates supplied fields, eg if you omit `dates` the existing dates will be unaffected.
Method: `PATCH`
Endpoint `/larp/{id}`
Arguments (json as payload):
```json
{
    "name": "A new title, max 255 chars",
    "pitch": "A new pitch (max 65355 chars)",
    "dates": [ // Replaces all current dates
        {
            "date_start": "2025-02-18",
            "date_end": "2025-02-20"
        },
        {
            "date_start": "2025-02-26",
            "date_end": "2025-02-28"
        }
    ],
    "organizers": "Joe Smith, Jane Doe, The Organization",
    "url": "https://goatse.cx",
    "email": "Contact email adress",
    "published": "Y",
    "cancelled": "N",
    "country": "SWE",
    "location": "Göteborg"
}
```

- `token` - The token provided upon creating a larp, will be matched to hash in db to allow/disallow operation

## Add an image to a larp
Method: `PUT`
Endpoint `/larp/{id}`
Arguments:
- `file` - ??
- `token` - The token provided upon creating a larp, will be matched to hash in db to allow/disallow operation


# Operations requiring auth-key
## Create a larp
