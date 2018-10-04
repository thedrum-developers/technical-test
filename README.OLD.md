# The Drum Technical Test

The goal of this test is to create a RESTful API using the PHP framework of your choice.

The API should focus on two entities: agencies and services, with a relationship between the two entities - an agency can provide multiple services, and a service can be offered by multiple agencies.

An example Docker Compose configuration is provided in this repository that you may use as a base for your development environment. It will require some tweaking depending on your framework, however, if you are more comfortable with another environment such as Vagrant or Homestead please use that instead.

Please fork this repository, and place your application code in the `application` directory.

## Requirements

- The API should have some form of token based authentication.
- There should be testing in place covering the key areas of the API - this can be unit and / or functional testing using PHPUnit, Behat etc.

## Expected Functionality

The following functionality should be accessible via appropriate RESTful HTTP calls.

### Agencies

- Retrieve an index of agencies
- View details of single agency by retrieving it by its ID
- Create a new agency

### Services

- Retrieve an index of services
- View details of a single service by retrieving it by its slug

### Relationships

- View the services an agency offers
- Update the services an agency offers

## Seed / Fixture Data

The following fixture / seed data should be used for your API:

### Agencies

|      Agency Name      |     Contact Email     |                Web Address                |                 Short Description                  | Established |
| --------------------- | --------------------- | ----------------------------------------- | -------------------------------------------------- | ----------- |
| RoRo's Rocket Chips   | hello@roro.com        | http://roro.com                           | The fieriest chips known to man.                   |        2019 |
| Heavy Profesh Web Dev | us@greatdevs.biz      | https://greatdevs.biz                     | The most professional developers in town.          |        1994 |
| Shass Kinsalott       | sounds@shasskinsal.ot | https://shasskinsal.ot                    | Post-modern audio branding agency based in London. |        2000 |


### Services

|  Service Name   |      Slug       |
| --------------- | --------------- |
| Web Development | web-development |
| PPC             | ppc             |
| SEO             | seo             |

### Agency <-> Service Relationship

|          Agency Name          |       Services       |
| ----------------------------- | -------------------- |
| RoRo's Rocket Chips           | Web Development, PPC |
| Heavy Profesh Web Dev         | Web Development, SEO |
| Shass Kinsalott               | PPC, SEO             |
