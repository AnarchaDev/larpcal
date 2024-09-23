# larpcal
A larp calendar (backend and API).

## TODOs
- [ ] Set up a dev recipie in docker-compose
  - [ ] Mount local codebase using a volume/bind-mount so we don't need to rebuild every time we change something
- [ ] Exports
  - [ ] RSS
  - [ ] CSV
  - [ ] iCal
- [ ] Look into websockets for mina
- [ ] How to solve "desktop notices" and app pushes?

# Public endpoints
## Get all larps
Gets all the (published) larps that have one or more dates that are in the future.

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

## Get larps from specific date
Gets all the (published) larps that have one or more dates that are equal to or greater than the given date (in YYYYMMDD format).

**URL** : `/from/YYYYMMDD`<br>
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
  }
]
```
</details>
