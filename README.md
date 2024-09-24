# larpcal
A larp calendar (backend and API).

## TODOs
- [ ] Exports
  - [ ] RSS
  - [ ] CSV
  - [ ] iCal
- [ ] Look into websockets for Mina (?)
- [ ] How to solve "desktop notices" and app pushes?

# Endpoints
## Get all larps
Gets all the (published) larps that have one or more dates that are in the future. 

### Filters
Filters can be applied by adding the `filters[]` query parameter to the call. Multiple filters can be used. The available filters are:
- `published` - If the larp is published (ie publically viewable or not). "`Y`" or "`N`", default "Y".
- `from` - Get any larps that have one or more dates later or equal to this date (YYYY-MM-DD).
- `to` - Get any larps that have one or more dates earlier or equal to this date (YYYY-MM-DD).
- `org` - Get larps where organizer matches this string (a `LIKE %string%` comparison on the backend)
- `countries` - Get larps where country matches this 3-letter ISO name (e.g `SWE`). Multiples can be commaseparated (e.g `SWE,NOR,DEN`).
- `continent` - Get larps from this continent (e.g `Europe`).

For example: `GET /?filters[published]=Y&filters[from]=2024-12-01&filters[countries]=SWE,FIN`

More filters may be added.

**URL** : `/`<br>
**Method** : `GET`<br>
**Example return** : <br>
<details>
<summary>Return data example</summary>

```json
[
  {
    "id": 2,
    "name": "Another Larp",
    "organizers": "Ho Chi HEHE Minh",
    "pitch": "In the grim dark future there is only war",
    "url": "https://goatse.cx",
    "email": null,
    "published": "Y",
    "cancelled": "N",
    "changedAt": "2024-09-16 13:40:28",
    "dates": [
      {
        "date_start": "2024-09-26",
        "date_end": "2024-09-27"
      }
    ]
  },
  {
    "id": 1,
    "name": "TestLarp",
    "organizers": "Godzilla Hårddisksson, Atropos Studios, Jan Bananberg",
    "pitch": "THis is a pitch",
    "url": "https://www.google.com",
    "email": "foo@bar.com",
    "published": "Y",
    "cancelled": "N",
    "changedAt": "2024-09-16 12:10:57",
    "dates": [
      {
        "date_start": "2024-08-22",
        "date_end": "2024-08-24"
      },
      {
        "date_start": "2024-10-04",
        "date_end": "2021-10-06"
      }
    ]
  }
]
```
</details>

## Get a specific larp
Gets a single larp based on it's numerical id

**URL** : `/{id}`<br>
**Method** : `GET`<br>
**Payload**:
```json
{
  "name": "Name of the larp",
  "organizers": "Organization 1, Organizer 1, Organizer 2 etc",
  "pitch": "A 32767 character pitch for the larp",
  "url": "https://verycoollarp.com",
  "email": "foo@bar.com",
  "countryId": 2,
  "dates": [
    {
      "date_start": "2025-02-02",
      "date_end": "2025-02-05"
    },
    {
      "date_start": "2025-02-09",
      "date_end": "2025-02-12"      
    }
  ],
  "tags": [
    12, 13, 15
  ]
}
```
**Example return** : <br>
<details>
<summary>Return data example</summary>

```json
[
  {
    "id": 2,
    "name": "Mörkrets Gryning - Silversagan, del III: Mörkret Vaknar",
    "dates": [
      {
        "date_start": "2024-11-02",
        "date_end": "2024-11-05"
      }
    ],
    "organizers": "Hamas, Antifa, Godzilla Hårddisksson",
    "pitch": "Riktigt fläskig fantasykampanj. Nu med grottalver!",
    "url": null,
    "email": null,
    "published": "Y",
    "cancelled": "N",
    "changedAt": "2024-09-18 13:03:18",
    "createdAt": "2024-09-18 13:03:18",
    "countryId": 167,
    "where": {
      "name": "Norway",
      "iso": "NOR",
      "continent": "Europe"
    },
    "tags": [
      {
        "id": 5,
        "tag": "boffer",
        "type": "content",
        "description": "Larps that use \"boffer\" weapons for combat, or where that type of combat is an integral part"
      },
      {
        "id": 10,
        "tag": "fantasy",
        "type": "genre",
        "description": "Typically played outdoors, and in various fantasy settings."
      }
    ]
  }
]
```
</details>

## Create a larp
Takes a json body. Requires the X-AUTH-API header to be set. Also returns a `token` that is to be presented to the user (which they will use to edit/delete/cancel their larp).

**URL** : `/`<br>
**Method** : `POST`<br>
**Example return** : <br>
<details>
<summary>Return data example</summary>

```json
{
  "larpId": 16,
  "token": {
    "token": "pODj8ebSeVqmfp3",
  }
}
```
</details>

## Upload an image
Takes a Multipart Form data payload. Requires the form fields `file` and `token`. The image must be at least 1024x768px, and will be centercropped and rescaled to produce a 1024x768px image (jpeg, quality=0.85).

**URL** : `/larp/{id}/image`<br>
**Method** : `POST`<br>
**Example return** : <br>
<details>
<summary>Return data example</summary>

```json
{
  "larpId": 16,
  "imageUrl": "https://foo.com/images/16.jpg"
}
```
</details>

## Get a list of available tags

**URL** : `/tags`<br>
**Method** : `GET`<br>
**Example return** : <br>
<details>
<summary>Return data example</summary>

```json
[
  {
    "id": 1,
    "tag": "1920s",
    "type": "setting",
    "description": null
  },
  {
    "id": 2,
    "tag": "1930s",
    "type": "setting",
    "description": null
  },
  {
    "id": 3,
    "tag": "blackbox",
    "type": "genre",
    "description": "Larps played in very minimalist scenography, often \"blackboxes\" found at theatres"
  },
  {
    "id": 4,
    "tag": "blockbuster",
    "type": "genre",
    "description": "Typically larps set in known intellectual properties and having high production values, fancy venues etc."
  },
  {
    "id": 5,
    "tag": "boffer",
    "type": "content",
    "description": "Larps that use \"boffer\" weapons for combat, or where that type of combat is an integral part"
  },
  {
    "id": 6,
    "tag": "chamber larp",
    "type": "genre",
    "description": "Larps played in small, enclosed spaces (somtimes a single room)."
  },
  {
    "id": 7,
    "tag": "cold war",
    "type": "setting",
    "description": null
  },
  {
    "id": 8,
    "tag": "contemporary",
    "type": "setting",
    "description": "Larps taking place in our current age."
  },
  {
    "id": 9,
    "tag": "dystopia",
    "type": "setting",
    "description": null
  },
  ...
]
```
</details>


## Get a list of countries

**URL** : `/countries`<br>
**Method** : `GET`<br>
**Example return** : <br>
<details>
<summary>Return data example</summary>

```json
[
  {
    "id": 1,
    "countryCode": "AD",
    "countryName": "Andorra",
    "isoNumeric": "020",
    "north": "42.65604389629997",
    "east": "1.7865427778319827",
    "continentName": "Europe",
    "continent": "EU",
    "isoAlpha3": "AND"
  },
  {
    "id": 2,
    "countryCode": "AE",
    "countryName": "United Arab Emirates",
    "isoNumeric": "784",
    "north": "26.08415985107422",
    "east": "56.38166046142578",
    "continentName": "Asia",
    "continent": "AS",
    "isoAlpha3": "ARE"
  },
  ...
```
</details>